<?php

namespace Stoyko\SandboxDeployer\Services;

use Exception;
use Stoyko\SandboxDeployer\Exceptions\AppErrorException;
use Stoyko\SandboxDeployer\Exceptions\CodeErrorException;
use Stoyko\SandboxDeployer\Exceptions\DatabaseErrorException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Stoyko\SandboxDeployer\Exceptions\DeployException;

class SandboxDeployer
{
    /**
     * @param string $relativeFilePath
     * @param string $codeContent
     * @return bool
     * @throws DeployException
     */
    public function injectCode(string $relativeFilePath, string $codeContent): bool
    {
        if (stripos(PHP_OS, 'LINUX') === false) {
            throw new DeployException("Not running on Linux.");
        }

        $sandboxRoot = env('AI_PHP_LARAVEL_SANDBOX_PATH');
        $fullPath = $sandboxRoot . '/' . $relativeFilePath;

        // Създаваме директорията, ако не съществува (напр. нов Controller)
        $directory = dirname($fullPath);
        if (!is_dir($directory)) {
            @mkdir($directory, 0755, true)
                ?? throw new DeployException("File is not wrote in dir: " . $fullPath);
        }

        // Записваме файла - това автоматично го "инжектира" в Docker чрез Volumes
        $result = @file_put_contents($fullPath, $codeContent);
        if (!$result) {
            throw new DeployException("File is not wrote in dir: " . $fullPath);
        }

        return true;
    }

    /**
     * @throws DeployException
     */
    public function ensureSailIsRunning(): void
    {
        if (stripos(PHP_OS, 'LINUX') === false) {
            throw new DeployException("Not running on Linux.");
        }

        $sandboxRoot = env('AI_PHP_LARAVEL_SANDBOX_PATH');

        // Проверяваме статуса на контейнерите
        // 'bin/sail ps --status=running' връща списък само с активните контейнери
        $checkProcess = new Process(['./vendor/bin/sail', 'ps', '--status=running'], $sandboxRoot);
        $checkProcess->run();

        // Ако изходът е празен, значи нищо не работи
        if (empty(trim($checkProcess->getOutput()))) {

            // Стартираме Sail в background режим
            $startProcess = new Process(['./vendor/bin/sail', 'up', '-d'], $sandboxRoot);
            $startProcess->setTimeout(300); // Даваме му 5 минути максимум за теглене/старт

            try {
                $startProcess->mustRun();

                // Малка пауза, за да сме сигурни, че MySQL/PHP са готови да поемат заявки
                sleep(2);
            } catch (ProcessFailedException $e) {
                throw new DeployException("Could not start Sail: " . $e->getMessage());
            }
        }
    }

    /**
     * @return string
     * @throws AppErrorException
     * @throws CodeErrorException
     * @throws DatabaseErrorException
     * @throws DeployException
     */
    public function runLaravelMigrationTest(): string
    {
        $sandboxRoot = env('AI_PHP_LARAVEL_SANDBOX_PATH');
        $process = new Process(['./vendor/bin/sail', 'artisan', 'migrate:fresh'], $sandboxRoot);

        try {
            $process->setTimeout(300);
            $process->run();
        } catch (Exception $e) {
            throw new DeployException("SERVER_ERROR: Process failed to trigger: " . $e->getMessage());
        }

        $output = $process->getOutput();
        $errorOutput = $process->getErrorOutput();
        $exitCode = $process->getExitCode();

        // 1. ПРОВЕРКА ЗА ИНФРАСТРУКТУРНА ГРЕШКА (Docker/Sail)
        if ($exitCode === 127 || str_contains($errorOutput, 'Docker is not running')) {
            throw new DeployException("INFRA_ERROR: Docker is down or Sail is missing.");
        }

        if (str_contains($errorOutput, 'Permission denied') || str_contains($errorOutput, 'address already in use')) {
            throw new DeployException("SERVER_ERROR: File permissions or Port conflicts on host.");
        }

        // 2. ПРОВЕРКА ЗА ГРЕШКА В КОДА (Генериран от AI)
        if (!$process->isSuccessful()) {
            // Комбинираме изходите, защото Laravel понякога праща грешките в STDOUT
            $combinedOutput = $output . ' ' . $errorOutput;

            $combinedOutput =  $this->cleanLaravelError($combinedOutput);
            if (str_contains($combinedOutput, 'Syntax error') || str_contains($combinedOutput, 'ParseError')) {
                throw new CodeErrorException("CODE_ERROR: AI generated invalid PHP syntax. Error: " . $combinedOutput);
            }

            if (str_contains($combinedOutput, 'SQLSTATE') || str_contains($combinedOutput, 'QueryException')) {
                throw new DatabaseErrorException("DATABASE_ERROR: Migration failed. Check table names or logic. Error: " .$combinedOutput );
            }

            // Ако не е нито едно от горните, връщаме обща грешка за кода
            throw new AppErrorException("APP_ERROR: Laravel crashed with: " . $combinedOutput);
        }

        return "SUCCESS: Migrations completed.";
    }

    private function cleanLaravelError(string $rawOutput): string
    {
        // 1. Търсим само същинското съобщение за грешка (преди Stack trace)
        if (preg_match('/(?:FatalError|ErrorException|QueryException)\s+(.*?)(?=\s+at\s+database\/|#0|Stack trace:|$)/s', $rawOutput, $matches)) {
            $message = trim($matches[1]);

            // 2. Вземаме и мястото на грешката (файла и реда)
            if (preg_match('/at (database\/migrations\/.*?:\d+)/', $rawOutput, $fileMatches)) {
                return "ERROR: " . $message . " in " . $fileMatches[1];
            }

            return "ERROR: " . $message;
        }

        // Ако не намерим чиста грешка, просто режем първите 300 символа, за да не претоварваме AI контекста
        return "APP_ERROR: " . substr($rawOutput, 0, 300) . "...";
    }

    public function migrationFileExists(string $migrationPath): bool
    {
        $sandboxRoot = env('AI_PHP_LARAVEL_SANDBOX_PATH');

        // Ако $migrationPath е "database/migrations/2026_05_01_create_tasks_table.php"
        $fullPath = rtrim($sandboxRoot, '/') . '/' . ltrim($migrationPath, '/');

        return file_exists($fullPath);
    }

    public function checkMigrationInDb(string $migrationPath): bool
    {
        $sandboxRoot = env('AI_PHP_LARAVEL_SANDBOX_PATH');

        // 1. Първо извличаме само името на файла без разширението .php
        // От "database/migrations/2026_05_01_create_users_table.php"
        // става "2026_05_01_create_users_table"
        $migrationName = basename($migrationPath, '.php');

        // 2. Проверяваме дали таблицата 'migrations' съществува
        $tableCheckQuery = "SHOW TABLES LIKE 'migrations';";
        $tableProcess = new Process(['./vendor/bin/sail', 'mysql', '-N', '-s', '-e', $tableCheckQuery], $sandboxRoot);
        $tableProcess->run();

        if (empty(trim($tableProcess->getOutput()))) {
            return false;
        }

        // 3. Търсим точното име в базата данни
        // Използваме директно името, вместо LIKE, за по-голяма прецизност
        $query = "SELECT COUNT(*) FROM migrations WHERE migration = '$migrationName';";

        $process = new Process([
            './vendor/bin/sail', 'mysql', '-N', '-s', '-e', $query
        ], $sandboxRoot);

        $process->run();

        if (!$process->isSuccessful()) {
            return false;
        }

        $count = trim($process->getOutput());

        return is_numeric($count) && (int)$count > 0;
    }

    /**
     * @param string $testName
     * @return string
     * @throws DeployException
     */
    public function runSandboxTest(string $testName): string
    {
        if (stripos(PHP_OS, 'LINUX') === false) {
            throw new DeployException("Not running on Linux.");
        }

        $sandboxRoot = env('AI_PHP_LARAVEL_SANDBOX_PATH');

        // Използваме директно "./vendor/bin/sail", но задаваме CWD (Current Working Directory)
        $process = new Process(['./vendor/bin/sail', 'test', '--filter=' . $testName], $sandboxRoot);

        // Важно: Sail/Docker изискват време. Настрой висок timeout.
        try {
            $process->setTimeout(300);
            $process->run();
        }catch (Exception $e) {
            throw new DeployException($e->getMessage());
        }

        if (!$process->isSuccessful()) {
            throw new DeployException('The process is not success');
        }

        return $process->getOutput();
    }
}
