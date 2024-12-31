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
This package allows you to create [`fzf` GitHub page](https://github.com/junegunn/fzf) powered menus straight from PHP code.

### Features
- Automatic `fzf` binary download
- Inline `fzf` configuration

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

Retrieve a single option from a list:
```php
<?php
use function Mantas6\FzfPhp\fzf;

$selected = fzf(['Apple', 'Orange', 'Grapefruit']);

// 'Apple'
```

- Returns `null` if user cancels (^C) the prompt

Retrieve multiple options from a list:
```php
<?php
use function Mantas6\FzfPhp\fzf;

$selected = fzf(
    options: ['Apple', 'Orange', 'Grapefruit'],
    arguments: ['multi' => true],
);

// ['Apple', 'Orange']
```

- Returns `[]` if user cancels (^C) the prompt

Or pass any other `fzf` configuration arguments:
```php
<?php
use function Mantas6\FzfPhp\fzf;

$selected = fzf(
    options: ['Apple', 'Orange', 'Grapefruit'],
    arguments: [
        'height' => '40%',
        'ansi' => true,
        'cycle' => true,
    ],
);
```

### Configuration

Use system `fzf` binary instead of fetching it:
```php
<?php
// YourAppServiceProvider.php

use Mantas6\FzfPhp\FuzzyFinder;

FuzzyFinder::usingCommand(['/usr/bin/env', 'fzf']);
```

Set global arguments for all prompts:
```php
<?php
// YourAppServiceProvider.php

use Mantas6\FzfPhp\FuzzyFinder;

FuzzyFinder::usingDefaultArguments(['pointer' => '->']);
```

See also:
- [`fzf` GitHub page](https://github.com/junegunn/fzf)
