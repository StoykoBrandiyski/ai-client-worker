<?php

namespace App\Services\Engine;

use App\Exceptions\NoSuchException;
use App\Services\Engine\Type\GeminiEngine;

class EngineFactory
{
    protected static array $mapping = [
        'gemini'       => GeminiEngine::class,
        //'ollama'       => OllamaEngine::class,
        //'hugging-face' => HuggingFaceEngine::class,
    ];

    protected static array $instances = [];

    /**
     * @param string $identifier
     * @return EngineProviderInterface
     * @throws NoSuchException
     */
    public static function make(string $identifier): EngineProviderInterface
    {
        $prefix = strstr($identifier, '-', true) ?: $identifier;

        if (!isset(self::$mapping[$prefix])) {
            throw new NoSuchException("Engine Provider [{$prefix}] not supported.");
        }

        $class = self::$mapping[$prefix];

        if (!isset(self::$instances[$class])) {
            // We still use app($class) so Laravel can inject any
            // dependencies your Engines might need in their __construct
            self::$instances[$class] = app($class);
        }

        return self::$instances[$class];
    }
}
