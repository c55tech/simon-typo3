# SIMON TYPO3 Extension

TYPO3 extension for integrating with the SIMON monitoring system.

## Installation

### Via Composer (Recommended)

Add the repository to your `composer.json`:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/c55tech/simon-typo3"
    }
  ],
  "require": {
    "simon/integration": "dev-main"
  }
}
```

Then install:

```bash
composer require simon/integration:dev-main
```

### Manual Installation

1. Copy the extension to: `typo3conf/ext/simon_integration`
2. Go to **Admin Tools → Extensions**
3. Find "SIMON Integration" and click the **+** icon to activate
4. Click the floppy disk icon to save

## Configuration

### Step 1: Configure Settings

1. Go to **Admin Tools → Settings → Extension Configuration**
2. Search for "SIMON Integration"
3. Configure:
   - **API URL**: Base URL of your SIMON API (e.g., `http://localhost:3000`)
   - **Auth Key**: Your SIMON authentication key
   - **Client ID**: Your SIMON client ID
   - **Site ID**: Your SIMON site ID

### Step 2: Setup Scheduled Data Submission

**For TYPO3 11.x:**
1. Go to **Admin Tools → Scheduler**
2. Click **Add Task**
3. Select **SIMON Data Submission**
4. Configure API settings and execution frequency
5. Save and enable the task

**For TYPO3 12.x and later:**
Use cron jobs or the task scheduler system to run data submission at regular intervals:
```bash
vendor/bin/typo3 simon:submit
```

## CLI Command (TYPO3 Console)

If using TYPO3 Console, you can create a custom command:

```bash
vendor/bin/typo3 simon:submit
```

## What Data is Collected

- **Core**: TYPO3 version
- **Log Summary**: Error/warning counts from sys_log
- **Environment**: PHP version, database info, web server
- **Extensions**: All installed extensions with versions

## Requirements

- TYPO3 11.5, 12.x, 13.x, or 14.x (including 14.0.1)
- PHP 8.1 or higher
- cURL extension enabled

## Troubleshooting

- Check TYPO3 logs: **Admin Tools → Log**
- Verify API URL is accessible
- Ensure Client ID and Site ID are configured
- Check scheduler task execution logs
