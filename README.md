<p align="center">
    <img src="https://raw.githubusercontent.com/mantas6/fzf-php/main/docs/logo.png" height="122" alt="Fzf Php">
    <p align="center">
        <a href="https://github.com/mantas6/fzf-php/actions"><img alt="GitHub Workflow Status (main)" src="https://github.com/mantas6/fzf-php/actions/workflows/tests.yml/badge.svg"></a>
        <a href="https://packagist.org/packages/mantas6/fzf-php"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/mantas6/fzf-php"></a>
        <a href="https://packagist.org/packages/mantas6/fzf-php"><img alt="Latest Version" src="https://img.shields.io/packagist/v/mantas6/fzf-php"></a>
        <a href="https://packagist.org/packages/mantas6/fzf-php"><img alt="License" src="https://img.shields.io/packagist/l/mantas6/fzf-php"></a>
    </p>
</p>

------
This package allows you to create [`fzf`](https://github.com/junegunn/fzf) powered menus straight from PHP code.

## Features

- Automatic `fzf` binary download
- Inline `fzf` configuration

## Installation

Install the package:

```sh
composer require mantas6/fzf-php
```

## Usage

### Options

Options can be provided in a multitude of ways.

#### Strings

```php
<?php
use function Mantas6\FzfPhp\fzf;

$selected = fzf(['Apple', 'Orange', 'Grapefruit']);

// 'Apple'
```
- Returns `null` if user cancels (^C) the prompt

#### Arrays

```php
<?php

$selected = fzf(
    options: [
        ['Apples', '1kg'],
        ['Oranges', '2kg'],
        ['Grapefruits', '3kg'],
    ]
);

// ['Apples', '1kg']
```

- Each array element represents different column in the finder

#### Objects

##### Using `toArray`

```php
<?php

$selected = fzf(
    options: [
        new Model('Apple'),
        new Model('Orange'),
        new Model('Grapefruits'),
    ]
);

// new Model('Apple')
```

- The object class must implement `toArray`

##### Implementing `PresentsForFinder` interface

If using `toArray` method is not feasible, an interface can be implemented instead.

```php
<?php

use Mantas6\FzfPhp\Concerns\PresentsForFinder;

class Model implements PresentsForFinder
{
    protected string $name;

    public function presentForFinder(): array
    {
        return [$this->name];
    }
}
```

##### Providing a presenter

To untie the presentation from the model, more reusable approach can be used.

```php
<?php

$selected = fzf(
    options: [
        new Model('Apple'),
        new Model('Orange'),
        new Model('Grapefruits'),
    ],

    present: fn (Model $item): array => [$item->name],
);

// or

$selected = fzf(
    // ...
    present: new ModelPresenterInvokable,
);
```

- The callable must always return an `array`

### Multi mode

Retrieve multiple options from a list.

```php
<?php

$selected = fzf(
    options: ['Apple', 'Orange', 'Grapefruit'],
    arguments: ['multi' => true],
);

// ['Apple', 'Orange']
```
- Returns `[]` if user cancels (^C) the prompt

### Arguments

Pass any other `fzf` configuration arguments:

```php
<?php

$selected = fzf(
    options: ['Apple', 'Orange', 'Grapefruit'],
    arguments: [
        'height' => '40%',
        'ansi' => true,
        'cycle' => true,
    ],
);
```

- Arguments `delimiter` (or `d`), `with-nth` are used internally, and will be overridden if specified
- Arguments that transform output may not be supported
- Preview and reload are not currently supported

### Reusable object approach

If `options` are not provided, the object is returned.

```php
<?php

$finder = fzf();

$fruit = $finder->ask(['Apple', 'Orange', 'Grapefruit']);

// ...

$weight = $finder->ask(['250g', '500g', '1kg']);
```

Configuration can be passed to pre-configure the instance.

```php
<?php

$finder = fzf(arguments: ['height' => '50%']);

$finder->ask(['apple', 'orange', 'grapefruit']);
```

Additional configuration is available through the class methods.

```php
<?php

$finder->present(...);

$finder->arguments(...);
```

## Configuration

Use system `fzf` binary instead of fetching it.

```php
<?php
// YourAppServiceProvider.php

use Mantas6\FzfPhp\FuzzyFinder;

FuzzyFinder::usingCommand(['/usr/bin/env', 'fzf']);
```

- Automatic binary download will be disabled when custom command is set

Set global arguments for all prompts.

```php
<?php
// YourAppServiceProvider.php

FuzzyFinder::usingDefaultArguments(['pointer' => '->']);
```

### Binary download

The `fzf` binary is downloaded automatically on the first use, however you can initiate the download manually.

```sh
./vendor/bin/fzf-php-install
```

See also:
- [`fzf` GitHub page](https://github.com/junegunn/fzf)
