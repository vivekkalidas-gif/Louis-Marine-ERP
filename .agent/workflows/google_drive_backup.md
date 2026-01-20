---
description: How to configure and run auto-backups to Google Drive
---

# Google Drive Backup Workflow

This workflow guides you through setting up automated backups to Google Drive using `spatie/laravel-backup` and `masbug/flysystem-google-drive-ext`.

## 1. Install & Configure (Already Done)
The necessary package has been installed and configuration files updated:
- `masbug/flysystem-google-drive-ext` installed.
- `config/filesystems.php` updated with `google` disk.
- `config/backup.php` updated to use `google` as destination.

## 2. Obtain Google Cloud Credentials
You need to generate credentials from the [Google Cloud Console](https://console.cloud.google.com/).

1.  **Create a Project**: Go to Google Cloud Console and create a new project.
2.  **Enable API**: Search for "Google Drive API" and enable it for your project.
3.  **Create Credentials**:
    - Go to "Credentials" -> "Create Credentials" -> "OAuth client ID".
    - Choose "Web application".
    - Set redirect URI to `https://developers.google.com/oauthplayground` (temporary, for generating token).
4.  **Get Tokens**:
    - Go to [OAuth 2.0 Playground](https://developers.google.com/oauthplayground).
    - Select "Drive API v3" scope (`https://www.googleapis.com/auth/drive`).
    - Click "Authorize APIs".
    - Exchange authorization code for tokens.
    - Copy the `Refresh Token`.

## 3. Update Environment Variables
Add the following to your `.env` file with the values you obtained:

```env
GOOGLE_DRIVE_CLIENT_ID=your-client-id
GOOGLE_DRIVE_CLIENT_SECRET=your-client-secret
GOOGLE_DRIVE_REFRESH_TOKEN=your-refresh-token
GOOGLE_DRIVE_FOLDER_ID=optional-folder-id
```

## 4. Test Backup
Run the backup command manually to verify connection:

```bash
php artisan backup:run
```

## 5. Automation
Ensure your server cron is running:
```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```
The backup job is scheduled to run daily at 03:00 automatically.
