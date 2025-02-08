# `flysystem-twig-loader`

[![PHP Version Support](https://img.shields.io/static/v1?label=php&message=%3E=%208.2.0&color=blue)](https://packagist.org/packages/elazar/flysystem-twig-loader)
[![Packagist Version](https://img.shields.io/static/v1?label=packagist&message=1.0.0&color=blue)](https://packagist.org/packages/elazar/flysystem-twig-loader)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.md)
[![Buy Me a Cofee](https://img.shields.io/badge/buy%20me%20a%20coffee-donate-blue.svg)](https://ko-fi.com/elazar)

A [custom loader](https://twig.symfony.com/doc/3.x/api.html#create-your-own-loader) for [Twig v3](https://twig.symfony.com/doc/3.x/) templates backed by a [Flysystem v2/3](https://flysystem.thephpleague.com/v2/docs/) filesystem.

Released under the [MIT License](https://en.wikipedia.org/wiki/MIT_License).

## Supported Use Cases

This library is intended to be used in projects that meet the following criteria.

1. Uses the Twig template engine.
2. Uses the Flysystem filesystem access library.
3. Needs to load Twig templates from a Flysystem filesystem.

## Unsupported Use Cases

This library doesn't and won't support Flysystem v1 or Twig v1 or v2. If you want a library similar to this one that is compatible with those versions, see [cedricziel/twig-loader-flysystem](https://packagist.org/packages/cedricziel/twig-loader-flysystem).

## Requirements

* PHP 8.2+
* Flysystem 2.1+
* Twig 3

## Installation

Use [Composer](https://getcomposer.org/).

```sh
composer require elazar/flysystem-twig-loader
```

**Note**: This will automatically install the latest version of the Flysystem core library that is available for your environment. However, you must handle installing adapters yourself. See [the Flysystem documentation](https://flysystem.thephpleague.com/docs/) for a list of official adapters.

## Usage

The example below uses the [`league/flysystem-local`](https://packagist.org/packages/league/flysystem-local) adapter that is automatically installed with the Flysystem core library.

```php
<?php

/**
 * 1. Instantiate the Flysystem filesystem reader with an appropriate adapter.
 *
 * Note: Because the Twig loader only executes read operations, it technically only
 * requires an instance of a class that implements the Flysystem FilesystemReader
 * interface (e.g. League\Flysystem\Filesystem).
 *
 * @see https://flysystem.thephpleague.com/docs/getting-started/
 */

$filesystemAdapter = new League\Flysystem\Local\LocalFilesystemAdapter('/path/to/templates');

$filesystemReader = new League\Flysystem\Filesystem($filesystemAdapter);

/**
 * 2. Instantiate the Twig template loader and pass the Flysystem filesystem
 *    reader to it.
 *
 * @see https://twig.symfony.com/doc/3.x/api.html#loaders
 */

$templateLoader = new Elazar\FlysystemTwigLoader\FlysystemLoader($filesystemReader);

/**
 * 3. Instantiate the Twig environment and pass the Twig template loader to it.
 *
 * @see https://twig.symfony.com/doc/3.x/api.html#basics
 */

$twig = new Twig\Environment($templateLoader);

/**
 * 4. Load or render templates as normal with Twig; they will be sourced from
 *    the Flysystem filesystem object.
 *
 * @see https://twig.symfony.com/doc/3.x/api.html#loading-templates
 * @see https://twig.symfony.com/doc/3.x/api.html#rendering-templates
 */

$path = '/path/to/template.html.twig';
$data = ['the' => 'variables', 'go' => 'here'];

$template = $twig->load($path);
echo $template->render($data);

// or

echo $twig->render($path, $data);
```
