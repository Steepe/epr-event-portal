<?php

namespace App\Services;

use CodeIgniter\HTTP\Files\UploadedFile;
use RuntimeException;

/**
 * Uploads administrator-selected welcome videos to Bunny Stream.
 *
 * The Bunny API key stays on the server in .env and is never delivered to a
 * browser. Bunny hosts and transcodes the completed video.
 */
class BunnyStreamService
{
    private const API_BASE = 'https://video.bunnycdn.com';

    /**
     * @throws RuntimeException
     */
    public function uploadWelcomeVideo(UploadedFile $file, string $title): string
    {
        if (! $file->isValid()) {
            throw new RuntimeException('The video upload failed: ' . $file->getErrorString());
        }

        $extension = strtolower((string) $file->getExtension());
        if (! in_array($extension, ['mp4', 'm4v', 'mov', 'webm'], true)) {
            throw new RuntimeException('Upload an MP4, M4V, MOV, or WebM video file.');
        }

        $this->assertConfigured();

        $video = $this->createVideo($title);

        try {
            $this->uploadFile((string) $video['guid'], $file);
        } catch (RuntimeException $exception) {
            $this->deleteVideo((string) $video['guid']);
            throw $exception;
        }

        return sprintf(
            'https://iframe.mediadelivery.net/embed/%s/%s?loop=false&muted=false&preload=true&responsive=true',
            rawurlencode($this->libraryId()),
            rawurlencode((string) $video['guid'])
        );
    }

    /**
     * @return array{guid: string}
     * @throws RuntimeException
     */
    private function createVideo(string $title): array
    {
        $response = $this->request(
            'POST',
            '/library/' . rawurlencode($this->libraryId()) . '/videos',
            json_encode(['title' => $title], JSON_THROW_ON_ERROR),
            ['Content-Type: application/json']
        );

        try {
            $video = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new RuntimeException('Bunny Stream did not return a valid video record.');
        }

        $guid = $video['guid'] ?? $video['Guid'] ?? null;
        if (! is_string($guid) || $guid === '') {
            throw new RuntimeException('Bunny Stream did not return a video ID.');
        }

        return ['guid' => $guid];
    }

    /**
     * @throws RuntimeException
     */
    private function uploadFile(string $videoGuid, UploadedFile $file): void
    {
        $path = $file->getTempName();
        $handle = @fopen($path, 'rb');
        if ($handle === false) {
            throw new RuntimeException('The uploaded video could not be read.');
        }

        try {
            $this->request(
                'PUT',
                '/library/' . rawurlencode($this->libraryId()) . '/videos/' . rawurlencode($videoGuid),
                $handle,
                [
                    'Content-Type: application/octet-stream',
                    'Content-Length: ' . (string) filesize($path),
                ]
            );
        } finally {
            fclose($handle);
        }
    }

    private function deleteVideo(string $videoGuid): void
    {
        try {
            $this->request(
                'DELETE',
                '/library/' . rawurlencode($this->libraryId()) . '/videos/' . rawurlencode($videoGuid)
            );
        } catch (RuntimeException) {
            // Do not conceal the original upload failure if Bunny cleanup fails.
        }
    }

    /**
     * @param resource|string|null $body
     * @param list<string> $headers
     * @throws RuntimeException
     */
    private function request(string $method, string $path, $body = null, array $headers = []): string
    {
        $curl = curl_init(self::API_BASE . $path);
        if ($curl === false) {
            throw new RuntimeException('Could not start the Bunny Stream request.');
        }

        $headers[] = 'AccessKey: ' . $this->apiKey();
        $headers[] = 'Accept: application/json';

        curl_setopt_array($curl, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => 0,
        ]);

        if (is_resource($body)) {
            curl_setopt($curl, CURLOPT_UPLOAD, true);
            curl_setopt($curl, CURLOPT_INFILE, $body);
        } elseif ($body !== null) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        }

        $response = curl_exec($curl);
        $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($response === false || $status < 200 || $status >= 300) {
            $detail = $error !== '' ? $error : trim((string) $response);
            throw new RuntimeException('Bunny Stream upload failed' . ($detail !== '' ? ': ' . mb_substr($detail, 0, 300) : '.'));
        }

        return (string) $response;
    }

    /**
     * @throws RuntimeException
     */
    private function assertConfigured(): void
    {
        if ($this->libraryId() === '' || $this->apiKey() === '') {
            throw new RuntimeException('Bunny Stream is not configured. Add bunny.streamLibraryId and bunny.streamApiKey to this server’s .env file.');
        }
    }

    private function libraryId(): string
    {
        return trim((string) env('bunny.streamLibraryId'));
    }

    private function apiKey(): string
    {
        return trim((string) env('bunny.streamApiKey'));
    }
}

