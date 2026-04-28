<?php

namespace Stoyko\SandboxDeployer\Services;

use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Log;

class SandboxExporter
{
    protected string $workspace = '/opt/ai-sandboxes/php/workspace';

    /**
     * Generates the patch and deletes the branch.
     */
    public function finalizeAndDownload(int $taskId)
    {
        $branchName = "feature/task-{$taskId}";

        // 1. Generate the Diff between the feature branch and main
        // We use 'git add -N .' to ensure untracked files are included
        $patchResult = Process::path($this->workspace)->pipe([
            "git add -N .",
            "git diff main..{$branchName}"
        ]);

        if ($patchResult->failed()) {
            throw new \Exception("Could not generate patch: " . $patchResult->errorOutput());
        }

        $patchContent = $patchResult->output();

        // 2. Cleanup: Move back to main and delete the branch
        $cleanup = Process::path($this->workspace)->pipe([
            "git checkout main",
            "git branch -D {$branchName}",
            "git clean -fd",
            "git reset"
        ]);

        if ($cleanup->failed()) {
            Log::warning("Cleanup failed for {$branchName}, manual intervention may be needed.");
        }

        return $patchContent;
    }
}
