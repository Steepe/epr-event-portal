<?php

namespace App\Modules\Api\Controllers;

use App\Controllers\BaseController;
use App\Services\MailchimpService;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\ResponseInterface;
use Ramsey\Uuid\Uuid;

class EmergenceFunnelController extends BaseController
{
    use ResponseTrait;

    protected \CodeIgniter\Database\BaseConnection $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function register(): ResponseInterface
    {
        $this->applyFunnelCorsHeaders();

        $payload = $this->request->getJSON(true) ?: $this->request->getPost();
        $payload = $this->normalisePayload($payload);
        $errors = $this->validatePayload($payload);

        if ($errors !== []) {
            return $this->failValidationErrors($errors);
        }

        $status = 'created';

        try {
            $this->db->transStart();
            $user = $this->findUser($payload['email']);

            if ($user) {
                $status = 'updated';
                $userId = (int) $user['id'];
                $this->db->table('tbl_users')
                    ->where('id', $userId)
                    ->update(['updated_at' => date('Y-m-d H:i:s')]);
            } else {
                $this->db->table('tbl_users')->insert([
                    'uuid' => Uuid::uuid4()->toString(),
                    'email' => $payload['email'],
                    'password' => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT),
                    'role' => 'attendee',
                    'is_verified' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                $userId = (int) $this->db->insertID();
            }

            $attendeeId = $this->upsertAttendee($userId, $payload);
            $submissionId = $this->recordSubmission($userId, $payload, $status);

            $this->db->transComplete();

            if (! $this->db->transStatus()) {
                return $this->failServerError('Unable to save registration.');
            }
        } catch (\Throwable $e) {
            $this->db->transRollback();
            log_message('error', 'Emergence funnel registration failed: ' . $e->getMessage());

            return $this->failServerError('Unable to save registration.');
        }

        $mailchimp = (new MailchimpService())->syncEmergenceContact($payload);
        $this->updateSubmissionMailchimpStatus($submissionId, $mailchimp);

        if ($mailchimp['status'] === 'failed') {
            log_message('error', 'Emergence funnel Mailchimp sync failed for ' . $payload['email'] . ': ' . json_encode($mailchimp['detail']));
        }

        return $this->respond([
            'status' => $status,
            'message' => $status === 'updated' ? 'Registration updated.' : 'Registration created.',
            'user_id' => $userId,
            'attendee_id' => $attendeeId,
            'mailchimp' => $mailchimp['status'],
        ]);
    }

    public function preflight(): ResponseInterface
    {
        $this->applyFunnelCorsHeaders();

        return $this->response->setStatusCode(204);
    }

    private function normalisePayload(array $payload): array
    {
        $mailchimp = $payload['mailchimp'] ?? [];
        $utm = $payload['utm'] ?? [];

        return [
            'email' => strtolower(trim((string) ($payload['email'] ?? ''))),
            'first_name' => trim((string) ($payload['first_name'] ?? '')),
            'last_name' => trim((string) ($payload['last_name'] ?? '')),
            'phone' => trim((string) ($payload['phone'] ?? '')),
            'city' => trim((string) ($payload['city'] ?? '')),
            'country' => trim((string) ($payload['country'] ?? '')),
            'referral_source' => trim((string) ($payload['referral_source'] ?? '')),
            'marketing_consent' => filter_var($payload['marketing_consent'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'mailchimp' => [
                'tags' => $mailchimp['tags'] ?? ['emergence-registrant'],
                'status_if_new' => $mailchimp['status_if_new'] ?? 'transactional',
            ],
            'utm' => [
                'source' => $this->nullableString($utm['source'] ?? null),
                'medium' => $this->nullableString($utm['medium'] ?? null),
                'campaign' => $this->nullableString($utm['campaign'] ?? null),
            ],
            'submitted_at' => $this->nullableString($payload['submitted_at'] ?? null),
        ];
    }

    private function validatePayload(array $payload): array
    {
        $errors = [];

        if ($payload['first_name'] === '') {
            $errors['first_name'] = 'First name is required.';
        }

        if ($payload['last_name'] === '') {
            $errors['last_name'] = 'Last name is required.';
        }

        if (! filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'A valid email is required.';
        }

        if ($payload['phone'] !== '' && strlen(preg_replace('/\D+/', '', $payload['phone'])) < 7) {
            $errors['phone'] = 'Enter a valid phone number.';
        }

        return $errors;
    }

    private function findUser(string $email): ?array
    {
        return $this->db->table('tbl_users')
            ->where('email', $email)
            ->get()
            ->getRowArray();
    }

    private function upsertAttendee(int $userId, array $payload): int
    {
        $data = [
            'attendee_id' => $userId,
            'firstname' => $payload['first_name'],
            'lastname' => $payload['last_name'],
            'telephone' => $payload['phone'],
            'country' => $payload['country'],
            'city' => $payload['city'],
            'ipaddress' => $this->request->getIPAddress(),
            'is_verified' => 1,
            'registration_timestamp' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $attendee = $this->db->table('tbl_attendees')
            ->where('attendee_id', $userId)
            ->get()
            ->getRowArray();

        if ($attendee) {
            $this->db->table('tbl_attendees')
                ->where('id', $attendee['id'])
                ->update($data);

            return (int) $attendee['id'];
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->table('tbl_attendees')->insert($data);

        return (int) $this->db->insertID();
    }

    private function recordSubmission(int $userId, array $payload, string $status): int
    {
        $this->db->table('tbl_emergence_funnel_submissions')->insert([
            'user_id' => $userId,
            'email' => $payload['email'],
            'first_name' => $payload['first_name'],
            'last_name' => $payload['last_name'],
            'phone' => $payload['phone'] ?: null,
            'city' => $payload['city'] ?: null,
            'country' => $payload['country'] ?: null,
            'referral_source' => $payload['referral_source'] ?: null,
            'marketing_consent' => $payload['marketing_consent'] ? 1 : 0,
            'utm_source' => $payload['utm']['source'],
            'utm_medium' => $payload['utm']['medium'],
            'utm_campaign' => $payload['utm']['campaign'],
            'db_status' => $status,
            'submitted_at' => $this->submittedAt($payload['submitted_at']),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return (int) $this->db->insertID();
    }

    private function updateSubmissionMailchimpStatus(int $submissionId, array $mailchimp): void
    {
        try {
            $this->db->table('tbl_emergence_funnel_submissions')
                ->where('id', $submissionId)
                ->update([
                    'mailchimp_status' => $mailchimp['status'],
                    'mailchimp_detail' => json_encode($mailchimp['detail'] ?? null),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Emergence funnel submission Mailchimp status update failed: ' . $e->getMessage());
        }
    }

    private function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function submittedAt(?string $submittedAt): ?string
    {
        if ($submittedAt === null) {
            return null;
        }

        $timestamp = strtotime($submittedAt);

        return $timestamp ? date('Y-m-d H:i:s', $timestamp) : null;
    }

    private function applyFunnelCorsHeaders(): void
    {
        $origin = $this->request->getHeaderLine('Origin');
        $allowedOrigins = [
            'https://portal.eprglobal.com',
            'https://eventportal.creyatif',
            'http://localhost:8080',
            'http://127.0.0.1:8080',
            'http://localhost:8099',
            'http://127.0.0.1:8099',
        ];

        if ($origin !== '' && in_array($origin, $allowedOrigins, true)) {
            $this->response->setHeader('Access-Control-Allow-Origin', $origin);
            $this->response->setHeader('Vary', 'Origin');
        }

        $this->response->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
        $this->response->setHeader('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With');
        $this->response->setHeader('Access-Control-Max-Age', '7200');
    }
}
