<?php


namespace Stoyko\SandboxDeployer\Services;


use InvalidArgumentException;

class CodeExtractor
{

    private function extractAndSplitDiffs($text)
    {
        preg_match_all('/```diff\s*(.*?)```/s', $text, $matches);

        return collect($matches[1])
            ->flatMap(function ($diffBlock) {
                return preg_split('/(?=^diff --git)/m', $diffBlock);
            })
            ->map(fn($part) => trim($part))
            ->filter()
            ->values()
            ->all();
    }

    public function extractDefinitionName($code)
    {
        // This regex looks for:
        // 1. Optional 'abstract' keyword
        // 2. Either 'class' or 'interface'
        // 3. One or more whitespace characters
        // 4. The name (captured group)
        $pattern = '/(?:abstract\s+)?(?:class|interface)\s+([a-zA-Z0-9_]+)/i';

        if (preg_match($pattern, $code, $matches)) {
            return $matches[1]; // Returns the captured name
        }

        return null;
    }

    public function extractUsefulCodeBlocks($text)
    {
        // Normalize weird spaces
        $text = preg_replace('/\x{00A0}/u', ' ', $text);

        preg_match_all('/```(?:\w+)?\R([\s\S]*?)```/', $text, $matches);

        return collect($matches[1])
            ->map(fn($code) => trim($code))
            ->filter(fn($code) => !str_contains($code, 'artisan'))
            ->values()
            ->all();
    }

    public function getFilePathFromCode($code) {
        // 1. Извличаме Namespace
        preg_match('/namespace\s+(.*?);/', $code, $nsMatches);
        $namespace = $nsMatches[1] ?? null;

        // 2. Извличаме Class Name
        preg_match('/class\s+(\w+)/', $code, $classMatches);
        $className = $classMatches[1] ?? null;

        if ($namespace && $className) {
            // Превръщаме "App\Http\Controllers" в "app/Http/Controllers"
            $path = str_replace(['App\\', '\\'], ['app/', '/'], $namespace);
            return $path . '/' . $className . '.php';
        }

        return null;
    }

    public function getMigrationPath($code) {
        if (preg_match("/Schema::create\(['\"](.*?)['\"]/", $code, $matches)) {
            $tableName = $matches[1]; // връща 'vehicles'
            $timestamp = date('Y_m_d_His');
            return "database/migrations/{$timestamp}_create_{$tableName}_table.php";
        }

        return null;
    }
}
