<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a secure timestamped backup of the database (SQLite or MySQL)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database backup process...');

        // Ensure backup directory exists in storage
        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $connection = config('database.default');
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "backup-{$timestamp}";

        if ($connection === 'sqlite') {
            $dbPath = DB::connection()->getDatabaseName();
            if ($dbPath === ':memory:' || $dbPath === '') {
                $this->info("In-memory SQLite database detected. Skipping physical file backup.");
                return 0;
            }
            if ($dbPath && File::exists($dbPath)) {
                $backupPath = "{$backupDir}/{$filename}.sqlite";
                File::copy($dbPath, $backupPath);
                $this->info("SQLite Backup created successfully: {$backupPath}");
                return 0;
            } else {
                $this->error("SQLite database file not found at path: {$dbPath}");
                return 1;
            }
        } elseif ($connection === 'mysql') {
            $dbConfig = config('database.connections.mysql');
            $backupPath = "{$backupDir}/{$filename}.sql";

            // Attempt using mysqldump
            $host = $dbConfig['host'] ?? '127.0.0.1';
            $port = $dbConfig['port'] ?? '3306';
            $database = $dbConfig['database'] ?? '';
            $username = $dbConfig['username'] ?? '';
            $password = $dbConfig['password'] ?? '';

            $mysqldumpPath = 'mysqldump';
            
            // Construct dump command
            $command = sprintf(
                '"%s" --user="%s" --password="%s" --host="%s" --port="%s" "%s" > "%s"',
                $mysqldumpPath,
                $username,
                $password,
                $host,
                $port,
                $database,
                $backupPath
            );

            $output = [];
            $resultCode = null;
            
            // Try running command
            @exec($command, $output, $resultCode);

            if ($resultCode === 0 && File::exists($backupPath) && File::size($backupPath) > 0) {
                $this->info("MySQL Backup created via mysqldump: {$backupPath}");
                return 0;
            }

            // Fallback to Pure-PHP Table-by-Table Dumper if command line tool is absent/fails
            $this->warn('mysqldump failed or is unavailable. Falling back to pure PHP backup engine...');
            
            try {
                $sqlDump = "-- Database Backup\n-- Date: " . now()->toDateTimeString() . "\n-- Connection: MySQL Fallback Dumper\n\n";
                $sqlDump .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

                // Get all tables
                $tables = DB::select('SHOW TABLES');
                $dbKey = "Tables_in_{$database}";

                foreach ($tables as $table) {
                    $tableName = $table->$dbKey;
                    $sqlDump .= "DROP TABLE IF EXISTS `{$tableName}`;\n";

                    // Get CREATE TABLE
                    $createTableResult = DB::select("SHOW CREATE TABLE `{$tableName}`");
                    $sqlDump .= $createTableResult[0]->{'Create Table'} . ";\n\n";

                    // Get Rows
                    $rows = DB::table($tableName)->get();
                    foreach ($rows as $row) {
                        $rowArray = (array) $row;
                        $keys = array_keys($rowArray);
                        $values = array_values($rowArray);

                        $escapedValues = array_map(function ($value) {
                            if (is_null($value)) {
                                return 'NULL';
                            }
                            return "'" . addslashes($value) . "'";
                        }, $values);

                        $sqlDump .= sprintf(
                            "INSERT INTO `%s` (%s) VALUES (%s);\n",
                            $tableName,
                            implode(', ', array_map(fn($k) => "`{$k}`", $keys)),
                            implode(', ', $escapedValues)
                        );
                    }
                    $sqlDump .= "\n";
                }

                $sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";

                File::put($backupPath, $sqlDump);
                $this->info("MySQL Backup created successfully via PHP Fallback engine: {$backupPath}");
                return 0;
            } catch (\Exception $e) {
                $this->error('Fallback dumper failed: ' . $e->getMessage());
                return 1;
            }
        }

        $this->error("Connection driver '{$connection}' is not supported for backup.");
        return 1;
    }
}
