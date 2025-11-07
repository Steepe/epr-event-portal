<?php
/**
 * Created by PhpStorm.
 * User: oluwamayowasteepe
 * Project: epr-event-portal
 * Date: 27/10/2025
 * Time: 10:51
 */


namespace App\Modules\Auth\Services;

use App\Modules\Auth\Models\AttendeeModel;

class WeavyService
{
    protected $model;
    protected $weavyUrl;
    protected $weavyApiKey;

    public function __construct()
    {
        $this->model = new AttendeeModel();
        $this->weavyUrl = getenv('WEAVY_URL') ?: env('WEAVY_URL');
        $this->weavyApiKey = getenv('WEAVY_API_KEY') ?: env('WEAVY_API_KEY');
    }

    /**
     * Mutates $user array to ensure weavy_id and weavy_token exist (best-effort).
     */
    public function syncWeavyData(array &$user)
    {
        try {
            // Ensure weavy_id
            if (empty($user['weavy_id']) && !empty($user['uid'])) {
                $res = $this->get("users/{$user['uid']}");
                if (!empty($res->httpcode) && $res->httpcode == 200) {
                    $user['weavy_id'] = $res->id ?? $res->Id ?? null;
                    if (!empty($user['weavy_id'])) {
                        $this->model->updateWeavyId($user['attendee_id'], $user['weavy_id']);
                    }
                }
            }

            // Ensure token
            if (empty($user['weavy_token']) || empty($user['token_expiry']) || time() > (int)$user['token_expiry']) {
                if (!empty($user['uid'])) {
                    $res = $this->post("users/{$user['uid']}/tokens", json_encode(['expires_in' => 3600]));
                    if (!empty($res->httpcode) && $res->httpcode == 200) {
                        $token = $res->access_token ?? null;
                        $expiry = time() + 3600;
                        if ($token) {
                            $user['weavy_token'] = $token;
                            $user['token_expiry'] = $expiry;
                            $this->model->updateWeavyToken($user['uid'], $token, $expiry);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            // swallow errors — we don't want login to fail because we couldn't contact Weavy
        }
    }

    /* Basic curl helpers */
    protected function request(string $method, string $endpoint, $body = null)
    {
        $url = rtrim($this->weavyUrl, '/').'/'.$endpoint;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = ['Content-Type: application/json'];
        if ($this->weavyApiKey) {
            $headers[] = "Authorization: Bearer {$this->weavyApiKey}";
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if ($method === 'POST' || $method === 'PATCH') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body ?? '');
        }
        $raw = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        $decoded = json_decode($raw);
        // attach httpcode for backwards-compat
        if (is_object($decoded)) {
            $decoded->httpcode = $info['http_code'] ?? null;
            return $decoded;
        }
        return (object)['httpcode' => $info['http_code'] ?? null, 'raw' => $raw];
    }

    protected function get($endpoint) { return $this->request('GET', $endpoint); }
    protected function post($endpoint, $body = null) { return $this->request('POST', $endpoint, $body); }
    protected function patch($endpoint, $body = null) { return $this->request('PATCH', $endpoint, $body); }
}
