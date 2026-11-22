<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

final class CompressHtml implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // The development toolbar appends HTML after response filters run.
        if ((defined('CI_DEBUG') && CI_DEBUG)
            || $response->getStatusCode() !== 200
            || ! function_exists('gzencode')) {
            return $response;
        }

        $acceptEncoding = $request->getHeaderLine('Accept-Encoding');
        if (! preg_match('/(?:^|,)\s*gzip(?:\s*;\s*q=([0-9.]+))?(?:\s*,|\s*$)/i', $acceptEncoding, $matches)
            || (isset($matches[1]) && (float) $matches[1] <= 0)) {
            return $response;
        }

        $contentType = strtolower($response->getHeaderLine('Content-Type'));
        $body = $response->getBody();
        if (! str_starts_with($contentType, 'text/html')
            || $response->getHeaderLine('Content-Encoding') !== ''
            || ! is_string($body)
            || strlen($body) < 4096) {
            return $response;
        }

        $compressed = gzencode($body, 5);
        if ($compressed === false || strlen($compressed) >= strlen($body)) {
            return $response;
        }

        $vary = $response->getHeaderLine('Vary');
        if (! preg_match('/(?:^|,)\s*Accept-Encoding\s*(?:,|$)/i', $vary)) {
            $response->setHeader('Vary', $vary === '' ? 'Accept-Encoding' : $vary . ', Accept-Encoding');
        }

        return $response
            ->removeHeader('Content-Length')
            ->setHeader('Content-Encoding', 'gzip')
            ->setBody($compressed);
    }
}
