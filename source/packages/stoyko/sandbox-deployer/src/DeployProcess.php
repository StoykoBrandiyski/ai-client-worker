<?php

namespace Stoyko\SandboxDeployer;

use Stoyko\SandboxDeployer\Exceptions\DeployException;
use Stoyko\SandboxDeployer\Exceptions\AppErrorException;
use Stoyko\SandboxDeployer\Exceptions\CodeErrorException;
use Stoyko\SandboxDeployer\Exceptions\DatabaseErrorException;
use Stoyko\SandboxDeployer\Services\CodeExtractor;
use Stoyko\SandboxDeployer\Services\SandboxDeployer;

class DeployProcess
{
    /**
     * DeployProcess constructor.
     * @param CodeExtractor $codeExtractor
     * @param SandboxDeployer $sandboxDeployer
     */
    public function __construct(
        private CodeExtractor $codeExtractor,
        private SandboxDeployer $sandboxDeployer
    ) {
    }


    /**
     * @param string $rawContent
     * @return string
     * @throws DeployException
     * @throws AppErrorException
     * @throws CodeErrorException
     * @throws DatabaseErrorException
     */
    public function run(string $rawContent): string
    {
        $cleanCode = array_first(
            $this->codeExtractor->extractUsefulCodeBlocks($rawContent)
        );

        $path = $this->codeExtractor->getMigrationPath($cleanCode);
        $isMigration = (bool) $path;

        if (!$path) {
            $path = $this->codeExtractor->getFilePathFromCode($cleanCode);
        }

        if (!$path) {
            throw new DeployException('Not extracted file content');
        }

        // Check before running another step
        $this->sandboxDeployer->ensureSailIsRunning();

        if ($isMigration
            && (
                $this->sandboxDeployer->migrationFileExists($path)
                || $this->sandboxDeployer->checkMigrationInDb($path)
            )
        ) {
            throw new DeployException("The migration already exist");
        }
        $this->sandboxDeployer->injectCode($path, $cleanCode);

        if ($isMigration) {
            return $this->sandboxDeployer->runLaravelMigrationTest();
        }

        $testName = $this->codeExtractor->extractDefinitionName($cleanCode);
        if (!$testName) {
            throw new DeployException('"Not extract class,interface or abstract nam');
        }

        // Is not test class, implementation class
        if (!str_contains($testName, 'Test')) {
            $testName = $testName.'Test';
        }

        return $this->sandboxDeployer->runSandboxTest($testName);
    }
}
