<?php

namespace App\Http\Controllers\Storage;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\UploadedFile;

class GoogleOneController
{
    /**
     * $settings yêu cầu:
     * - client_id, client_secret, redirect_uri
     * - access_token, refresh_token, token_expiry (timestamp Unix, optional)
     * - root_folder_id (optional)
     */
    public function __construct(
        protected array $settings
    ) {}

    /* =======================
     *  Google OAuth Helpers
     * ======================= */
    protected function http(): Client
    {
        return new Client([
            'base_uri' => 'https://www.googleapis.com/',
            'timeout'  => 60,
        ]);
    }

    protected function token(): string
    {
        // Nếu gần hết hạn thì refresh
        $exp = $this->settings['token_expiry'] ?? 0;
        if (!$this->settings['access_token'] || (time() + 60) >= $exp) {
            $this->refreshToken();
        }
        return $this->settings['access_token'];
    }

    protected function refreshToken(): void
    {
        $client = new Client(['timeout' => 30]);
        $resp = $client->post('https://oauth2.googleapis.com/token', [
            'form_params' => [
                'client_id'     => $this->settings['client_id'],
                'client_secret' => $this->settings['client_secret'],
                'refresh_token' => $this->settings['refresh_token'],
                'grant_type'    => 'refresh_token',
            ],
        ])->getBody()->getContents();

        $data = json_decode($resp, true);
        if (!isset($data['access_token'])) {
            throw new \RuntimeException('Cannot refresh Google token');
        }
        $this->settings['access_token'] = $data['access_token'];
        if (isset($data['expires_in'])) {
            $this->settings['token_expiry'] = time() + (int)$data['expires_in'];
        }
        // TODO: Lưu lại $this->settings vào DB/cấu hình của bạn.
    }

    protected function gRequest(string $method, string $url, array $opts = [])
    {
        $opts['headers']['Authorization'] = 'Bearer '.$this->token();
        return $this->http()->request($method, $url, $opts);
    }

    /* =======================
     *  Core Operations
     * ======================= */

    /**
     * Upload file (≤ ~5MB dùng multipart đơn giản).
     * $source: đường dẫn file local hoặc UploadedFile.
     * $name: tên file hiển thị trên Drive.
     * return: ['id' => fileId, 'name' => ..., 'webViewLink' => ..., 'webContentLink' => ...]
     */
    public function upload(string|UploadedFile $source, string $name, ?string $mime = null, ?string $parentId = null): array
    {
        $parentId = $parentId ?: ($this->settings['root_folder_id'] ?? null);

        $contents = ($source instanceof UploadedFile)
            ? file_get_contents($source->getRealPath())
            : file_get_contents($source);

        $mime = $mime ?: ($source instanceof UploadedFile ? $source->getMimeType() : 'application/octet-stream');

        // Metadata part
        $metadata = [
            'name' => $name,
        ];
        if ($parentId) $metadata['parents'] = [$parentId];

        $boundary = '===============gpt5boundary'.uniqid();
        $body  = "--$boundary\r\n";
        $body .= "Content-Type: application/json; charset=UTF-8\r\n\r\n";
        $body .= json_encode($metadata)."\r\n";
        $body .= "--$boundary\r\n";
        $body .= "Content-Type: $mime\r\n\r\n";
        $body .= $contents."\r\n";
        $body .= "--$boundary--";

        $resp = $this->gRequest('POST', 'upload/drive/v3/files?uploadType=multipart&fields=id,name,webViewLink,webContentLink', [
            'headers' => ['Content-Type' => "multipart/related; boundary=$boundary"],
            'body'    => $body,
        ])->getBody()->getContents();

        return json_decode($resp, true);
    }

    /**
     * Xoá file theo $fileId
     */
    public function delete(string $fileId): bool
    {
        try {
            $this->gRequest('DELETE', "drive/v3/files/{$fileId}");
            return true;
        } catch (RequestException $e) {
            return false;
        }
    }

    /**
     * Tạo link chia sẻ công khai (anyone with the link)
     * Trả về webViewLink hoặc webContentLink.
     */
    public function createPublicLink(string $fileId): array
    {
        // Tạo permission public
        $this->gRequest('POST', "drive/v3/files/{$fileId}/permissions", [
            'json' => [
                'role' => 'reader',
                'type' => 'anyone',
            ],
        ]);

        // Lấy link
        $resp = $this->gRequest('GET', "drive/v3/files/{$fileId}?fields=id,name,webViewLink,webContentLink")
            ->getBody()->getContents();

        return json_decode($resp, true);
    }

    /**
     * Kiểm tra kết nối — lấy profile user Drive
     */
    public function ping(): array
    {
        $resp = $this->gRequest('GET', 'drive/v3/about?fields=user,storageQuota')
            ->getBody()->getContents();
        return json_decode($resp, true);
    }
}
