<?php

namespace Stoyko\SandboxDeployer;

use Stoyko\SandboxDeployer\Exceptions\DeployException;
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
