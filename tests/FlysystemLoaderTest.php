<?php

declare(strict_types=1);

use Elazar\FlysystemTwigLoader\FlysystemLoader;
use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use Twig\Error\LoaderError;

expect()->extend(
    'toThrowLoaderError',
    fn(string $name) =>
        expect($this->value)->toThrow(
            LoaderError::class,
            "Template not found: {$name}",
        ),
);

beforeEach(function () {
    $this->existentName = 'foo';
    $this->existentCode = 'content';

    $this->nonexistentName = 'bar';

    $this->time = time();

    $this->filesystem = new Filesystem(
        adapter: new InMemoryFilesystemAdapter,
    );
    $this->filesystem->write(
        location: $this->existentName,
        contents: $this->existentCode,
    );

    $this->loader = new FlysystemLoader(
        filesystemReader: $this->filesystem,
    );
});

it('returns source context for an existent file', function () {
    $context = $this->loader->getSourceContext($this->existentName);
    $this->assertSame($this->existentCode, $context->getCode());
    $this->assertSame($this->existentName, $context->getName());
    $this->assertSame($this->existentName, $context->getPath());
});

it('returns source context for a nonexistent file', function () {
    expect(fn() => $this->loader->getSourceContext($this->nonexistentName))
        ->toThrowLoaderError($this->nonexistentName, $this->nonexistentName);
});

it('returns cache key for an existent file', function () {
    $key = $this->loader->getCacheKey($this->existentName);
    $this->assertSame($this->existentName, $key);
});

it('returns cache key for a nonexistent file', function () {
    expect(fn() => $this->loader->getCacheKey($this->nonexistentName))
        ->toThrowLoaderError($this->nonexistentName, $this->nonexistentName);
});

it('returns is fresh for an existent file', function () {
    expect($this->loader->isFresh($this->existentName, $this->time + 1))->toBeTrue();
    expect($this->loader->isFresh($this->existentName, $this->time - 1))->toBeFalse();
});

it('returns is fresh for a nonexistent file', function () {
    expect(fn() => $this->loader->isFresh($this->nonexistentName, $this->time))
        ->toThrowLoaderError($this->nonexistentName, $this->nonexistentName);
});

it('returns exists for an existent file', function () {
    expect($this->loader->exists($this->existentName))->toBeTrue();
});

it('returns exists for a nonexistent file', function () {
    expect($this->loader->exists($this->nonexistentName))->toBeFalse();
});
