<?php

namespace Stoyko\SandboxDeployer\Services;

use Exception;
use Symfony\Component\Process\Process;
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
     * @param string $testName
     * @return string
     * @throws DeployException
     */
    public function runLaravelMigrationTest(string $testName): string
    {
        $sandboxRoot = env('AI_PHP_LARAVEL_SANDBOX_PATH');

        // Използваме директно "./vendor/bin/sail", но задаваме CWD (Current Working Directory)
        $process = new Process(['./vendor/bin/sail', 'artisan', 'migrate:fresh'], $sandboxRoot);

        // Важно: Sail/Docker изискват време. Настрой висок timeout.
        try {
            $process->setTimeout(300);
            $process->run();
        }catch (Exception $e) {
            throw new DeployException($e->getMessage());
        }

        if ($error = $process->getErrorOutput()) {
            throw new DeployException($error);
        }

        if (!$process->isSuccessful()) {
            throw new DeployException('The process is not success');
        }

        return $process->getOutput();
    }

    /**
     * @param string $testName
     * @return string
     * @throws DeployException
     */
    public function runSandboxTest(string $testName): string
    {
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
