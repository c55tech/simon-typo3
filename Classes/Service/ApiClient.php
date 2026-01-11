<?php
declare(strict_types=1);

namespace Simon\Integration\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Log\LogManager;
use Psr\Log\LoggerInterface;

class ApiClient
{
    private LoggerInterface $logger;
    private string $baseUrl;
    private string $authKey;

    public function __construct()
    {
        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
    }

    public function setConfig(string $baseUrl, string $authKey): void
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->authKey = $authKey;
    }

    public function submit(string $endpoint, array $data): bool
    {
        if (empty($this->baseUrl) || empty($this->authKey)) {
            $this->logger->error('SIMON: API URL or Auth Key not configured');
            return false;
        }

        $url = $this->baseUrl . '/api/' . ltrim($endpoint, '/');

        $headers = [
            'Content-Type: application/json',
            'X-Auth-Key: ' . $this->authKey,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            $this->logger->error('SIMON API cURL Error: ' . $error);
            return false;
        }

        if ($statusCode >= 200 && $statusCode < 300) {
            return true;
        }

        $this->logger->error('SIMON API Error: ' . $statusCode . ' - ' . $response);
        return false;
    }
}
