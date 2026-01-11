<?php
declare(strict_types=1);

namespace Simon\Integration\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Information\Typo3Version;

class DataCollector
{
    public function collect(): array
    {
        $data = [];

        // Core version
        $typo3Version = GeneralUtility::makeInstance(Typo3Version::class);
        $data['core'] = [
            'version' => $typo3Version->getVersion(),
            'status' => $this->getCoreStatus(),
        ];

        // Log summary
        $data['log_summary'] = $this->getLogSummary();

        // Environment
        $data['environment'] = $this->getEnvironment();

        // Extensions
        $data['extensions'] = $this->getExtensions();

        // Templates (not directly available in TYPO3)
        $data['themes'] = [];

        return $data;
    }

    private function getCoreStatus(): string
    {
        // Would check for TYPO3 updates
        return 'up-to-date';
    }

    private function getLogSummary(): array
    {
        // TYPO3 log query
        $queryBuilder = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
            ->getQueryBuilderForTable('sys_log');

        $result = $queryBuilder
            ->select('level')
            ->from('sys_log')
            ->where(
                $queryBuilder->expr()->gt('tstamp', time() - 86400)
            )
            ->executeQuery();

        $errorCount = 0;
        $warningCount = 0;

        while ($row = $result->fetchAssociative()) {
            if ($row['level'] <= 2) {
                $errorCount++;
            } elseif ($row['level'] <= 4) {
                $warningCount++;
            }
        }

        return [
            'total' => $errorCount + $warningCount,
            'error' => $errorCount,
            'warning' => $warningCount,
            'by_level' => [],
        ];
    }

    private function getEnvironment(): array
    {
        $dbConnection = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
            ->getConnectionByName(\TYPO3\CMS\Core\Database\ConnectionPool::DEFAULT_CONNECTION_NAME);

        return [
            'php_version' => PHP_VERSION,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => (int) ini_get('max_execution_time'),
            'web_server' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
            'database_type' => 'mysql',
            'database_version' => $dbConnection->getServerVersion(),
            'php_modules' => get_loaded_extensions(),
        ];
    }

    private function getExtensions(): array
    {
        $packageManager = GeneralUtility::makeInstance(PackageManager::class);
        $activePackages = $packageManager->getActivePackages();
        $extensions = [];

        foreach ($activePackages as $package) {
            if ($package->getPackageMetaData()->getPackageType() === 'typo3-cms-extension') {
                $extensions[] = [
                    'type' => 'extension',
                    'machine_name' => $package->getPackageKey(),
                    'human_name' => $package->getPackageMetaData()->getTitle(),
                    'version' => $package->getPackageMetaData()->getVersion(),
                    'status' => 'enabled',
                    'is_custom' => strpos($package->getPackageKey(), 'simon_') === 0 ? false : true,
                ];
            }
        }

        return $extensions;
    }
}
