<?php

use App\Libraries\AttendeePasswordReset;
use App\Database\Migrations\EnsureAttendeePasswordResets;
use CodeIgniter\Test\CIUnitTestCase;

require_once APPPATH . 'Database/Migrations/2026-10-25-000001_EnsureAttendeePasswordResets.php';

final class AttendeePasswordResetTest extends CIUnitTestCase
{
    private $connection;
    private $mailer;
    private AttendeePasswordReset $resets;
    private string $body = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection = \Config\Database::connect([
            'DBDriver' => 'SQLite3', 'database' => ':memory:', 'DBPrefix' => '', 'DBDebug' => true,
        ], false);
        $this->connection->query('CREATE TABLE tbl_users (id INTEGER PRIMARY KEY, email TEXT UNIQUE, password TEXT, updated_at TEXT)');
        $this->connection->table('tbl_users')->insert(['email' => 'person@example.test', 'password' => password_hash('OldPassword123', PASSWORD_DEFAULT)]);
        (new EnsureAttendeePasswordResets(\Config\Database::forge($this->connection)))->up();
        config('Email')->fromEmail = 'portal@example.test';
        $this->mailer = $this->getMockBuilder(\CodeIgniter\Email\Email::class)
            ->onlyMethods(['send', 'setMessage'])->getMock();
        $this->mailer->method('setMessage')->willReturnCallback(function ($body) {
            $this->body = $body;
            return $this->mailer;
        });
        $this->resets = new AttendeePasswordReset($this->connection, $this->mailer);
    }

    protected function tearDown(): void
    {
        $this->connection->close();
        parent::tearDown();
    }

    private function issue(): string
    {
        $this->mailer->method('send')->willReturn(true);
        $this->resets->sendLink('person@example.test');
        preg_match('/token=([a-f0-9]{64})/', $this->body, $match);
        $this->assertCount(2, $match);
        return $match[1];
    }

    public function testEmailLinkUpdatesPasswordAndCannotBeReused(): void
    {
        $token = $this->issue();
        $record = $this->connection->table('password_resets')->get()->getRowArray();
        $this->assertSame(hash('sha256', $token), $record['token']);
        $this->assertTrue($this->resets->isValid($token));
        $this->assertStringContainsString('/attendees/reset-password?token=', $this->body);
        $this->assertSame('html', $this->mailer->mailType);
        $this->resets->reset($token, 'NewPassword123', 'NewPassword123');
        $password = $this->connection->table('tbl_users')->get()->getRowArray()['password'];
        $this->assertTrue(password_verify('NewPassword123', $password));
        $this->assertFalse(password_verify('OldPassword123', $password));
        $this->assertFalse($this->resets->isValid($token));
        $this->expectException(InvalidArgumentException::class);
        $this->resets->reset($token, 'AnotherPassword123', 'AnotherPassword123');
    }

    public function testExpiredLinkDoesNotChangePassword(): void
    {
        $token = $this->issue();
        $this->connection->table('password_resets')->update(['expires_at' => date('Y-m-d H:i:s', time() - 1)]);
        $this->assertFalse($this->resets->isValid($token));
        $this->expectException(InvalidArgumentException::class);
        $this->resets->reset($token, 'NewPassword123', 'NewPassword123');
    }

    public function testMismatchedAndShortPasswordsDoNotConsumeToken(): void
    {
        $token = $this->issue();
        foreach ([['short', 'short'], ['NewPassword123', 'NotMatching123'], [str_repeat('x', 73), str_repeat('x', 73)]] as [$password, $confirm]) {
            try {
                $this->resets->reset($token, $password, $confirm);
                $this->fail('Invalid password should be rejected');
            } catch (InvalidArgumentException $error) {
                $this->assertTrue($this->resets->isValid($token));
            }
        }
    }

    public function testResetInvalidatesOtherLinksForTheSameAccount(): void
    {
        $token = $this->issue();
        $other = str_repeat('b', 64);
        $this->connection->table('password_resets')->insert(['email' => 'person@example.test', 'token' => $other, 'created_at' => date('Y-m-d H:i:s')]);
        $this->resets->reset($token, 'NewPassword123', 'NewPassword123');
        $this->assertFalse($this->resets->isValid($other));
    }

    public function testUnknownEmailDoesNotSendOrCreateToken(): void
    {
        $this->mailer->expects($this->never())->method('send');
        $this->resets->sendLink('unknown@example.test');
        $this->assertSame(0, $this->connection->table('password_resets')->countAllResults());
    }

    public function testFailedDeliveryRemovesTheUnsentToken(): void
    {
        $this->mailer->method('send')->willReturn(false);
        try {
            $this->resets->sendLink('person@example.test');
            $this->fail('Delivery failure must be reported');
        } catch (RuntimeException $error) {
            $this->assertSame(0, $this->connection->table('password_resets')->countAllResults());
        }
    }

    public function testLegacyLinksWithEitherTimestampStillWork(): void
    {
        foreach (['created_at' => date('Y-m-d H:i:s'), 'expires_at' => date('Y-m-d H:i:s', time() + 3600)] as $column => $value) {
            $token = bin2hex(random_bytes(32));
            $this->connection->table('password_resets')->insert(['email' => 'person@example.test', 'token' => $token, $column => $value]);
            $this->assertTrue($this->resets->isValid($token));
        }
        $this->assertFalse($this->resets->isValid('invalid'));
    }

    public function testMigrationPreservesExistingLinksAndAddsMissingTimestamp(): void
    {
        $this->connection->query('DROP TABLE password_resets');
        $this->connection->query('CREATE TABLE password_resets (email TEXT, token TEXT, expires_at TEXT)');
        $this->connection->table('password_resets')->insert(['email' => 'person@example.test', 'token' => str_repeat('c', 64), 'expires_at' => date('Y-m-d H:i:s', time() + 3600)]);
        $migration = new EnsureAttendeePasswordResets(\Config\Database::forge($this->connection));
        $migration->up();
        $migration->up();
        $this->assertTrue($this->connection->fieldExists('created_at', 'password_resets'));
        $this->assertSame(1, $this->connection->table('password_resets')->countAllResults());
    }

    public function testDesktopAndMobileFormsPreserveToken(): void
    {
        $token = str_repeat('a', 64);
        foreach (['Web', 'MobileApp'] as $module) {
            $html = module_view($module, 'reset_password', ['token' => $token]);
            $this->assertStringContainsString('name="token" value="' . $token . '"', $html);
            $this->assertStringContainsString('data-password-reset', $html);
            $this->assertStringContainsString('password-reset.js', $html);
            $this->assertStringContainsString('Request a new link', $html);
        }
    }

    public function testResetPagesAreReachableWithoutAnAttendeeSession(): void
    {
        $routes = service('routes');
        $routes->loadRoutes();
        foreach (['attendees/forgot-password', 'attendees/reset-password', 'attendees/reset-password/([^/]+)', 'mobile/reset-password'] as $route) {
            $this->assertArrayHasKey($route, $routes->getRoutes('GET'));
            $this->assertNotContains('auth', $routes->getFiltersForRoute($route, 'GET'));
            $this->assertNotContains('mobileauth', $routes->getFiltersForRoute($route, 'GET'));
        }
    }
}
