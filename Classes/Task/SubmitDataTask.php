<?php
declare(strict_types=1);

namespace Simon\Integration\Task;

use TYPO3\CMS\Scheduler\Task\AbstractTask;
use Simon\Integration\Service\DataCollector;
use Simon\Integration\Service\ApiClient;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SubmitDataTask extends AbstractTask
{
    public string $apiUrl = '';
    public string $authKey = '';
    public int $clientId = 0;
    public int $siteId = 0;

    public function execute(): bool
    {
        $dataCollector = GeneralUtility::makeInstance(DataCollector::class);
        $apiClient = GeneralUtility::makeInstance(ApiClient::class);

        $apiClient->setConfig($this->apiUrl, $this->authKey);

        $siteData = $dataCollector->collect();
        $baseUrl = GeneralUtility::getIndpEnv('TYPO3_SITE_URL');

        $payload = [
            'client_id' => $this->clientId,
            'site_id' => $this->siteId,
            'cms_type' => 'typo3',
            'site_name' => $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'] ?? 'TYPO3 Site',
            'site_url' => $baseUrl,
            'data' => $siteData,
        ];

        return $apiClient->submit('intake', $payload);
    }
}
