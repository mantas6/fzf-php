<p align="center">
    <img src="https://raw.githubusercontent.com/mantas6/fzf-php/main/docs/example.png" height="300" alt="Skeleton Php">
    <p align="center">
        <a href="https://github.com/mantas6/fzf-php/actions"><img alt="GitHub Workflow Status (main)" src="https://github.com/mantas6/fzf-php/actions/workflows/tests.yml/badge.svg"></a>
        <a href="https://packagist.org/packages/mantas6/fzf-php"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/mantas6/fzf-php"></a>
        <a href="https://packagist.org/packages/mantas6/fzf-php"><img alt="Latest Version" src="https://img.shields.io/packagist/v/mantas6/fzf-php"></a>
        <a href="https://packagist.org/packages/mantas6/fzf-php"><img alt="License" src="https://img.shields.io/packagist/l/mantas6/fzf-php"></a>
    </p>
</p>

------
This package allows you to create `fzf` powered menus straight from PHP code.

### Features
- Automatic `fzf` binary download

### Installation

Install the package:

```sh
composer require mantas6/fzf-php
```

Download the `fzf` binary:

```sh
./vendor/bin/fzf-php-install
```

### Usage

Retrieve selected options out of a given list:
```php
<?php
use function Mantas6\FzfPhp\fzf;

$selected = fzf(['Apple', 'Orange', 'Grapefruit']);
```

Pass arguments to configure `fzf` itself:
```php
<?php
use function Mantas6\FzfPhp\fzf;

$selected = fzf(
    options: ['Apple', 'Orange', 'Grapefruit'],
    arguments: ['height' => '40%'],
);
```

### Configuration

Use system `fzf` binary instead of fetching it:
```php
<?php
// YourAppServiceProvider.php

use Mantas6\FzfPhp\FuzzyFinder;

FuzzyFinder::usingCommand('/usr/bin/env fzf');
```
