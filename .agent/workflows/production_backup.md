---
description: How to configure and run backups in production
---

# Production Backup Workflow

This workflow guides you through setting up automated backups for the application using `spatie/laravel-backup`.

## 1. Publish Configuration (First Time Only)
If `config/backup.php` does not exist, run this command to publish the package configuration:

```bash
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"
```

## 2. Configure Backup Destination
Open `config/filesystems.php` and add a `backups` disk. For a secure local backup (outside public webroot), add this to the `disks` array:

```php
'backups' => [
    'driver' => 'local',
    'root' => storage_path('app/backups'), // Stored in storage/app/backups
    'throw' => false,
],
```

Then, open `config/backup.php` and ensure the `destination` is set to utilize this disk:

```php
// config/backup.php
'destination' => [
    'filename_prefix' => '',
    'disks' => [
        'backups', // matches the disk name in filesystems.php
    ],
],
```

## 3. Verify Database Credentials
Ensure your production `.env` file has the correct database credentials. The backup package uses `mysqldump`, so the user must have necessary permissions.

## 4. Test Backup Manually
Run the following command to trigger an immediate backup of the database and files:

```bash
php artisan backup:run
```
*   Use `--only-db` to backup only the database.
*   Use `--only-files` to backup only the files.

## 5. Automate with Scheduler
In a production environment, you must have the Laravel Scheduler running. Add a single Cron entry to your server:

```bash
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

By default, `spatie/laravel-backup` schedules itself to run daily at 03:00 via `app/Console/Kernel.php` (or registered automatically in newer versions). You can customize this in `config/backup.php` or `routes/console.php`.

## 6. Monitor Backups
- Check the output of `php artisan backup:list` to see the status of recent backups.
- Configure mail notifications in `config/backup.php` to receive alerts on success/failure.
