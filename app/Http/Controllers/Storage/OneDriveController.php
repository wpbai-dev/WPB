<?php

namespace App\Http\Controllers\Storage;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\UploadedFile;

class OneDriveController
{
    /**
     * $settings yêu cầu:
     * - tenant_id (hoặc 'common'), client_id, client_secret, redirect_uri
     * - access_token, refresh_token, token_expiry (timestamp Unix, optional)
     * - root_path (ví dụ: "root" hoặc "root:/YourFolder")
     */
    public function __construct(
        protected array $settings
    ) {}

    /* =======================
     *  MS Graph OAuth Helpers
     * ======================= */
    protected function http(): Client
    {
        return new Client([
            'base_uri' => 'https://graph.microsoft.com/v1.0/',
            'timeout'  => 60,
        ]);
    }

    protected function token(): string
    {
        $exp = $this->settings['token_expiry'] ?? 0;
        if (!$this->settings['access_token'] || (time() + 60) >= $exp) {
            $this->refreshToken();
        }
        return $this->settings['access_token'];
    }

    protected function refreshToken(): void
    {
        $tenant = $this->settings['tenant_id'] ?? 'common';
        $client = new Client(['timeout' => 30]);
        $resp = $client->post("https://login.microsoftonline.com/{$tenant}/oauth2/v2.0/token", [
            'form_params' => [
                'client_id'     => $this->settings['client_id'],
                'client_secret' => $this->settings['client_secret'],
                'refresh_token' => $this->settings['refresh_token'],
                'grant_type'    => 'refresh_token',
                'redirect_uri'  => $this->settings['redirect_uri'],
                'scope'         => 'files.readwrite offline_access',
            ],
        ])->getBody()->getContents();

        $data = json_decode($resp, true);
        if (!isset($data['access_token'])) {
            throw new \RuntimeException('Cannot refresh OneDrive token');
        }
        $this->settings['access_token'] = $data['access_token'];
        if (isset($data['expires_in'])) {
            $this->settings['token_expiry'] = time() + (int)$data['expires_in'];
        }
        // TODO: Lưu $this->settings vào DB/cấu hình của bạn.
    }

    protected function graph(string $method, string $url, array $opts = [])
    {
        $opts['headers']['Authorization'] = 'Bearer '.$this->token();
        return $this->http()->request($method, $url, $opts);
    }

    /* =======================
     *  Core Operations
     * ======================= */

    /**
     * Upload (≤4MB) qua endpoint đơn giản.
     * $pathInDrive ví dụ: "/myfolder/filename.jpg"
     * return: ['id' => ..., 'name' => ..., 'webUrl' => ...]
     */
    public function upload(string|UploadedFile $source, string $pathInDrive, ?string $mime = null): array
    {
        $content = ($source instanceof UploadedFile)
            ? file_get_contents($source->getRealPath())
            : file_get_contents($source);

        $mime = $mime ?: ($source instanceof UploadedFile ? $source->getMimeType() : 'application/octet-stream');

        // PUT /me/drive/root:/path:/content
        $encodedPath = rawurlencode($pathInDrive);
        $resp = $this->graph('PUT', "me/drive/root:{$encodedPath}:/content", [
            'headers' => ['Content-Type' => $mime],
            'body'    => $content,
        ])->getBody()->getContents();

        return json_decode($resp, true);
    }

    /**
     * Xoá file theo itemId.
     */
    public function delete(string $itemId): bool
    {
        try {
            $this->graph('DELETE', "me/drive/items/{$itemId}");
            return true;
        } catch (RequestException $e) {
            return false;
        }
    }

    /**
     * Tạo link chia sẻ công khai (anonymous view)
     * return: ['link' => ['webUrl' => ..., 'type' => 'view']]
     */
    public function createPublicLink(string $itemId): array
    {
        $resp = $this->graph('POST', "me/drive/items/{$itemId}/createLink", [
            'json' => [
                'type'  => 'view',
                'scope' => 'anonymous',
            ],
        ])->getBody()->getContents();

        return json_decode($resp, true);
    }

    /**
     * Kiểm tra kết nối — lấy thông tin Drive
     */
    public function ping(): array
    {
        $resp = $this->graph('GET', 'me/drive')
            ->getBody()->getContents();
        return json_decode($resp, true);
    }
}
