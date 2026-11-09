<?php

namespace App\Modules\Admin\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class MessagesController extends BaseController
{
    private const MESSAGE_TABLE = 'tbl_admin_portal_messages';
    private const DELIVERY_TABLE = 'tbl_admin_portal_message_deliveries';

    public function index()
    {
        if (! $this->isReady()) {
            return module_view('Admin', 'messages/index', ['ready' => false]);
        }

        $db = db_connect();
        $messages = $db->table(self::MESSAGE_TABLE . ' m')
            ->select("m.*, a.name AS sender_name, SUM(CASE WHEN d.email_status = 'sent' THEN 1 ELSE 0 END) AS sent_count, SUM(CASE WHEN d.email_status = 'failed' THEN 1 ELSE 0 END) AS failed_count", false)
            ->join('tbl_admins a', 'a.id = m.sender_admin_id', 'left')
            ->join(self::DELIVERY_TABLE . ' d', 'd.admin_message_id = m.admin_message_id', 'left')
            ->groupBy('m.admin_message_id, m.sender_admin_id, m.subject, m.body, m.recipient_type, m.recipient_filters, m.recipient_count, m.created_at, a.name', false)
            ->orderBy('m.admin_message_id', 'DESC')
            ->limit(30)
            ->get()
            ->getResultArray();

        return module_view('Admin', 'messages/index', [
            'ready' => true,
            'countries' => $this->countries(),
            'messages' => $messages,
        ]);
    }

    public function send()
    {
        if (! $this->isReady()) {
            return redirect()->to(site_url('admin/messages'))->with('error', 'The Admin Messages migration must be run before messages can be sent.');
        }

        $subject = $this->limitedPost('subject', 255);
        $body = $this->limitedPost('body', 10000);
        $recipientType = (string) $this->request->getPost('recipient_type');
        $validTypes = ['individual', 'filtered', 'emergence_2026', 'all'];

        if ($subject === '' || $body === '') {
            return redirect()->back()->withInput()->with('error', 'A subject and message are required.');
        }
        if (! in_array($recipientType, $validTypes, true)) {
            return redirect()->back()->withInput()->with('error', 'Choose who should receive this message.');
        }
        if ($recipientType === 'all' && $this->request->getPost('confirm_all') !== '1') {
            return redirect()->back()->withInput()->with('error', 'Confirm that you want to email every portal attendee.');
        }

        $filters = [
            'recipient_email' => $this->limitedPost('recipient_email', 254),
            'email' => $this->limitedPost('email', 254),
            'country' => $this->limitedPost('country', 100),
        ];
        if ($recipientType === 'individual' && ! filter_var($filters['recipient_email'], FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('error', 'Enter the email address of the attendee to receive this message.');
        }
        if ($recipientType === 'filtered' && $filters['email'] === '' && $filters['country'] === '') {
            return redirect()->back()->withInput()->with('error', 'Add an email search or country filter before sending to a filtered group.');
        }

        $recipients = $this->recipients($recipientType, $filters);
        if ($recipients === []) {
            return redirect()->back()->withInput()->with('error', 'No portal attendees match the selected recipients.');
        }

        $db = db_connect();
        $now = date('Y-m-d H:i:s');
        $db->table(self::MESSAGE_TABLE)->insert([
            'sender_admin_id' => (int) session('admin_id') ?: null,
            'subject' => $subject,
            'body' => $body,
            'recipient_type' => $recipientType,
            'recipient_filters' => json_encode($filters, JSON_UNESCAPED_SLASHES),
            'recipient_count' => count($recipients),
            'created_at' => $now,
        ]);
        $messageId = (int) $db->insertID();

        $sent = 0;
        $failed = 0;
        foreach ($recipients as $recipient) {
            $db->table(self::DELIVERY_TABLE)->insert([
                'admin_message_id' => $messageId,
                'user_id' => (int) $recipient['user_id'],
                'email' => $recipient['email'],
                'email_status' => 'pending',
                'created_at' => $now,
            ]);
            $deliveryId = (int) $db->insertID();

            if ($this->sendEmail($recipient, $subject, $body)) {
                $db->table(self::DELIVERY_TABLE)->where('delivery_id', $deliveryId)->update([
                    'email_status' => 'sent',
                    'sent_at' => date('Y-m-d H:i:s'),
                ]);
                ++$sent;
            } else {
                $db->table(self::DELIVERY_TABLE)->where('delivery_id', $deliveryId)->update([
                    'email_status' => 'failed',
                    'email_error' => 'The configured email service did not accept this message.',
                ]);
                ++$failed;
            }
        }

        $result = $sent . ' email' . ($sent === 1 ? '' : 's') . ' sent';
        if ($failed > 0) {
            $result .= '; ' . $failed . ' failed. See delivery history below.';
        }

        return redirect()->to(site_url('admin/messages'))->with($failed > 0 ? 'error' : 'success', $result . '.');
    }

    public function view(int $id)
    {
        if (! $this->isReady()) {
            throw PageNotFoundException::forPageNotFound();
        }

        $message = db_connect()->table(self::MESSAGE_TABLE)->where('admin_message_id', $id)->get()->getRowArray();
        if (! $message) {
            throw PageNotFoundException::forPageNotFound();
        }

        return redirect()->to(site_url('admin/messages'));
    }

    private function recipients(string $type, array $filters): array
    {
        $builder = db_connect()->table('tbl_attendees a')
            ->select('a.attendee_id AS user_id, a.firstname, a.lastname, u.email')
            ->join('tbl_users u', 'u.id = a.attendee_id', 'inner')
            ->where('u.email IS NOT NULL', null, false)
            ->where("TRIM(u.email) <> ''", null, false)
            ->groupBy(['a.attendee_id', 'a.firstname', 'a.lastname', 'u.email']);

        if ($type === 'individual') {
            $builder->where('u.email', $filters['recipient_email']);
        } elseif ($type === 'filtered') {
            if ($filters['email'] !== '') {
                $builder->like('u.email', $filters['email']);
            }
            if ($filters['country'] !== '') {
                $builder->where('a.country', $filters['country']);
            }
        } elseif ($type === 'emergence_2026') {
            $builder->where('u.registered_2026_at IS NOT NULL', null, false);
        }

        $recipients = $builder->orderBy('u.email', 'ASC')->get()->getResultArray();

        return array_values(array_filter($recipients, static fn (array $recipient): bool => filter_var($recipient['email'] ?? '', FILTER_VALIDATE_EMAIL) !== false));
    }

    private function sendEmail(array $recipient, string $subject, string $body): bool
    {
        try {
            $email = service('email');
            $email->clear(true);
            $email->setFrom(
                env('email.fromEmail') ?: 'no-reply@portal.eprglobal.com',
                env('email.fromName') ?: 'EPR Global Events'
            );
            $email->setTo($recipient['email']);
            $email->setSubject($subject);
            $email->setMailType('html');
            $email->setMessage(view('App\\Modules\\Admin\\Views\\emails\\portal_message', [
                'name' => trim((string) (($recipient['firstname'] ?? '') . ' ' . ($recipient['lastname'] ?? ''))) ?: 'there',
                'subject' => $subject,
                'body' => $body,
                'notificationsUrl' => site_url('attendees/notifications'),
            ]));

            if ($email->send()) {
                return true;
            }

            log_message('error', 'Admin portal message email failed for ' . $recipient['email']);
        } catch (\Throwable $exception) {
            log_message('error', 'Admin portal message email failed for ' . ($recipient['email'] ?? 'unknown') . ': ' . $exception->getMessage());
        }

        return false;
    }

    private function countries(): array
    {
        $rows = db_connect()->table('tbl_attendees')
            ->select('country')
            ->where('country IS NOT NULL', null, false)
            ->where("TRIM(country) <> ''", null, false)
            ->groupBy('country')
            ->orderBy('country', 'ASC')
            ->get()
            ->getResultArray();

        return array_column($rows, 'country');
    }

    private function limitedPost(string $key, int $length): string
    {
        return mb_substr(trim((string) $this->request->getPost($key)), 0, $length);
    }

    private function isReady(): bool
    {
        $db = db_connect();

        return $db->tableExists(self::MESSAGE_TABLE) && $db->tableExists(self::DELIVERY_TABLE);
    }
}
