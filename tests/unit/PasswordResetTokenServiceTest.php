<?php

use App\Services\PasswordResetTokenService;
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class PasswordResetTokenServiceTest extends CIUnitTestCase
{
    private \CodeIgniter\Database\BaseConnection $connection;
    private \CodeIgniter\Database\Forge $forge;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = db_connect('tests');
        $this->forge = \Config\Database::forge('tests');
        $this->forge->dropTable('tbl_user_tokens', true);
        $this->forge->addField([
            'token_id' => ['type' => 'INTEGER', 'auto_increment' => true],
            'user_id' => ['type' => 'INTEGER'],
            'token' => ['type' => 'VARCHAR', 'constraint' => 512],
            'type' => ['type' => 'VARCHAR', 'constraint' => 20],
            'expires_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
        ]);
        $this->forge->addKey('token_id', true);
        $this->forge->createTable('tbl_user_tokens', true);
    }

    protected function tearDown(): void
    {
        $this->forge->dropTable('tbl_user_tokens', true);
        parent::tearDown();
    }

    public function testIssuedTokenIsStoredAsDigestAndCanBeValidated(): void
    {
        $tokens = new PasswordResetTokenService($this->connection);
        $token = $tokens->issue(42);

        $record = $this->connection->table('tbl_user_tokens')->get()->getRowArray();

        $this->assertNotSame($token, $record['token']);
        $this->assertSame(hash('sha256', $token), $record['token']);
        $this->assertSame(42, (int) $tokens->valid($token)['user_id']);
    }

    public function testIssuingAnotherTokenInvalidatesTheEarlierOne(): void
    {
        $tokens = new PasswordResetTokenService($this->connection);
        $first = $tokens->issue(42);
        $second = $tokens->issue(42);

        $this->assertNull($tokens->valid($first));
        $this->assertNotNull($tokens->valid($second));
    }
}
