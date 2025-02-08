<?php

declare(strict_types=1);

namespace Elazar\FlysystemTwigLoader;

use League\Flysystem\FilesystemReader;
use Twig\Error\LoaderError;
use Twig\Loader\LoaderInterface;
use Twig\Source;

class FlysystemLoader implements LoaderInterface
{
    /** @var array<string, Source> */
    private array $cache = [];

    public function __construct(
        private FilesystemReader $filesystemReader,
    ) { }

    public function getSourceContext(string $name): Source
    {
        $path = $this->findTemplateOrThrow($name);
        return $this->cache[$path] ??= new Source(
            $this->filesystemReader->read($path),
            $name,
            $path,
        );
    }

    public function getCacheKey(string $name): string
    {
        return $this->findTemplateOrThrow($name);
    }

    public function isFresh(string $name, int $time): bool
    {
        return $this->filesystemReader->lastModified(
            $this->findTemplateOrThrow($name),
        ) < $time;
    }

    /** @return bool */
    public function exists(string $name)
    {
        return $this->filesystemReader->fileExists($name);
    }

    private function findTemplateOrThrow(string $name): string
    {
        return $this->filesystemReader->fileExists($name)
            ? $name
            : throw new LoaderError("Template not found: {$name}");
    }
}
