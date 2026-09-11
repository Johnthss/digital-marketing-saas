<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SystemBackupCommand extends Command
{
    protected $signature = 'system:backup
                            {--storage=local : Storage disk to save the backup}
                            {--compress : Compress the backup file}
                            {--retention=7 : Number of days to keep backups}';

    protected $description = 'Create a database backup and store it on the configured disk';

    public function handle(): int
    {
        $this->info('═══════════════════════════════════════════════════');
        $this->info('  DigitalMarketingSaaS — System Backup');
        $this->info('═══════════════════════════════════════════════════');
        $this->newLine();

        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");
        $database = config("database.connections.{$connection}.database");
        $storageDisk = $this->option('storage');
        $compress = $this->option('compress');
        $retention = (int) $this->option('retention');

        $timestamp = Carbon::now()->format('Y-m-d_His');
        $filename = "backups/db_backup_{$timestamp}.sql";
        $tempPath = storage_path("app/backup_temp_{$timestamp}.sql");

        $this->info("Database: {$database}");
        $this->info("Driver: {$driver}");
        $this->newLine();

        try {
            $this->info('Creating backup...');

            if ($driver === 'sqlite') {
                $this->backupSqlite($database, $tempPath);
            } elseif ($driver === 'mysql') {
                $this->backupMysql($tempPath);
            } elseif ($driver === 'pgsql') {
                $this->backupPostgres($tempPath);
            } else {
                $this->error("Unsupported database driver: {$driver}");
                return self::FAILURE;
            }

            // Compress if requested
            if ($compress && file_exists($tempPath)) {
                $this->info('Compressing backup...');
                $gzPath = $tempPath . '.gz';
                $gz = gzopen($gzPath, 'wb9');
                gzwrite($gz, file_get_contents($tempPath));
                gzclose($gz);
                unlink($tempPath);
                $tempPath = $gzPath;
                $filename .= '.gz';
            }

            // Move to storage
            $this->info('Storing backup...');
            Storage::disk($storageDisk)->put($filename, file_get_contents($tempPath));
            unlink($tempPath);

            $fileSize = Storage::disk($storageDisk)->size($filename);
            $this->info("✅ Backup saved: {$filename}");
            $this->info("   Size: " . $this->formatBytes($fileSize));

            // Clean old backups
            if ($retention > 0) {
                $this->cleanOldBackups($storageDisk, $retention);
            }

        } catch (\Exception $e) {
            $this->error("Backup failed: " . $e->getMessage());
            if (file_exists($tempPath)) {
                unlink($tempPath);
            }
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('═══════════════════════════════════════════════════');
        $this->info('  Backup completed successfully');
        $this->info('═══════════════════════════════════════════════════');

        return self::SUCCESS;
    }

    private function backupSqlite(string $database, string $outputPath): void
    {
        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // For SQLite, we can simply copy the database file
        if (file_exists($database)) {
            copy($database, $outputPath);
        } else {
            // If the database path is relative, resolve it
            $dbPath = database_path(basename($database));
            if (file_exists($dbPath)) {
                copy($dbPath, $outputPath);
            } else {
                throw new \RuntimeException("SQLite database file not found: {$database}");
            }
        }
    }

    private function backupMysql(string $outputPath): void
    {
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $charset = config('database.connections.mysql.charset', 'utf8mb4');

        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $command = sprintf(
            'mysqldump --host=%s --port=%d --user=%s --default-character-set=%s --single-transaction --routines --triggers --quick %s > %s',
            escapeshellarg($host),
            (int) $port,
            escapeshellarg($username),
            escapeshellarg($charset),
            escapeshellarg($database),
            escapeshellarg($outputPath)
        );

        if ($password) {
            $command = 'MYSQL_PWD=' . escapeshellarg($password) . ' ' . $command;
        }

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \RuntimeException("mysqldump failed with exit code: {$returnCode}");
        }
    }

    private function backupPostgres(string $outputPath): void
    {
        $host = config('database.connections.pgsql.host');
        $port = config('database.connections.pgsql.port', 5432);
        $database = config('database.connections.pgsql.database');
        $username = config('database.connections.pgsql.username');

        $dir = dirname($outputPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $command = sprintf(
            'PGPASSWORD=%s pg_dump --host=%s --port=%d --username=%s --format=plain --no-owner --no-acl %s > %s',
            escapeshellarg(config('database.connections.pgsql.password')),
            escapeshellarg($host),
            (int) $port,
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg($outputPath)
        );

        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \RuntimeException("pg_dump failed with exit code: {$returnCode}");
        }
    }

    private function cleanOldBackups(string $disk, int $days): void
    {
        $this->info("Cleaning backups older than {$days} days...");
        $files = Storage::disk($disk)->files('backups');
        $cutoff = Carbon::now()->subDays($days)->timestamp;

        $deleted = 0;
        foreach ($files as $file) {
            $lastModified = Storage::disk($disk)->lastModified($file);
            if ($lastModified < $cutoff) {
                Storage::disk($disk)->delete($file);
                $deleted++;
            }
        }

        if ($deleted > 0) {
            $this->info("   Deleted {$deleted} old backup(s)");
        }
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        $size = $bytes;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, 2) . ' ' . $units[$unitIndex];
    }
}
