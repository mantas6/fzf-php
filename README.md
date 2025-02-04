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
This package allows you to create [`fzf`](https://github.com/junegunn/fzf) powered menus straight from your PHP code.

<img src="https://raw.githubusercontent.com/mantas6/fzf-php/main/docs/main.png" alt="Demo">

## Features

- Automatic `fzf` binary download
- Inline `fzf` configuration
- Option list styling
- Selected option preview
- Laravel support

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
- Returns `null` if user cancels the prompt

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

```php
<?php

$selected = fzf(
    options: [
        new Model('Apple'), // must implement toArray or PresentsForFinder interface
        new Model('Orange'),
        new Model('Grapefruit'),
    ]
);

// new Model('Apple')
```

To use objects as options, at least one is required:

- Implement `toArray`
- Implement `PresentsForFinder` interface
- Provide presenter callback

### Options presentation

Callback can be provided for presentation. This will work for any options type.

```php
<?php

$selected = fzf(
    options: [
        'Apple',
        'Orange',
        'Grapefruits',
    ],

    present: fn (string $item): string => strtoupper($item),
);
```

Multiple columns can be present by returning an array.

```php
<?php

$selected = fzf(
    // ...

    present: fn (string $item): array => [
        $item,
        strtoupper($item),
    ],
);
```

#### Implementing `PresentsForFinder` interface

Option objects can implement a special interface, that would work the same as providing a presenter callback.

```php
<?php

use Mantas6\FzfPhp\Concerns\PresentsForFinder;

class Model implements PresentsForFinder
{
    protected string $name;

    public function presentForFinder(): array|string
    {
        return $this->name;
    }
}
```

#### Styling

Option columns can be styling using `cell()` helper function in the presenter callback.

```php
<?php

use function Mantas6\FzfPhp\cell;

$selected = fzf(
    options: [
        ['name' => 'Apples', 'weight' => 1000],
        ['name' => 'Oranges', 'weight' => 2000],
        ['name' => 'Grapefruits', 'weight' => 3000],
    ],

    // Styling individual items
    present: fn (array $item): array => [
        $item['name'],

        cell($item['weight'], fg: $item['weight'] > 2000 ? 'red' : 'green'),
    ],
);
```

Formatting options are:

```php
<?php

cell(
    // Text of the cell
    value: 'Text displayed',

    // Alignment in the table (left, right, center)
    align: 'right',

    // Foreground color
    fg: 'white',

    // Background color
    bg: 'red',

    // Column span
    colspan: 2,
);
```

- Available colors: `red, green, yellow, blue, magenta, cyan, white, default, gray, bright-red, bright-green, bright-yellow, bright-blue, bright-magenta, bright-cyan, bright-white`

More information can be found at [Symfony Docs: Table](https://symfony.com/doc/current/components/console/helpers/table.html)

### Preview

Preview window can be enabled for each selected option.

```php
<?php

$selected = fzf(
    options: ['Apple', 'Orange', 'Grapefruit'],

    preview: fn (string $item) => strtoupper($item),
);
```

If more advanced styling is needed, `style()` helper can be used.

```php
<?php
use function Mantas6\FzfPhp\style;

$selected = fzf(
    // ...
    preview: function (string $item) {
        return style()
            ->table([
                ['Original', $item],
                ['Uppercase', strtoupper($item)],
            ]);
    }
);
```

- Use `->table()` for creating compact tables
- Use `->block()` for creating text blocks

#### Additional variables

`fzf` provides additional variables to the preview (and other) child processes.

```php
<?php
use Mantas6\FzfPhp\ValueObjects\FinderEnv;

$selected = fzf(
    // ...
    preview: function (string $item, FinderEnv $env) {
        // ...
        $env->key, // The name of the last key pressed
        $env->action, // The name of the last action performed
        // ...
    }
);
```

Full set of variables are available at [`fzf` Reference - Environment variables exported to child processes](https://junegunn.github.io/fzf/reference/#environment-variables-exported-to-child-processes)

### Headers

Fixed header will be displayed if header list is passed.

```php
<?php

$selected = fzf(
    options: ['Apple', 'Orange', 'Grapefruit'],
    headers: ['Fruit'],
);
```

### Options as object

Instead of passing options as array, object can be used.

```php
<?php

$selected = fzf(
    options: new MyCustomCollection,
);
```

The class needs to meet one of the following requirements:

- Must implement the native `Traversable` interface
- Needs to implement `toArray()` method

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
- Returns `[]` if user cancels the prompt

### Arguments

Pass any other `fzf` configuration arguments:

```php
<?php

$selected = fzf(
    options: ['Apple', 'Orange', 'Grapefruit'],
    arguments: [
        'height' => '40%',
        'cycle' => true,
    ],
);
```

- Arguments `delimiter` (or `d`), `with-nth` are used internally, and will be overridden if specified
- Arguments that transform output may not be supported
- Consult [`fzf` Reference](https://junegunn.github.io/fzf/reference) for all available options

### Reusable object approach

Class instance can be created directly for more reusable approach.

```php
<?php

use Mantas6\FzfPhp\FuzzyFinder;

$finder = new FuzzyFinder;

$fruit = $finder->ask(['Apple', 'Orange', 'Grapefruit']);

// ...

$weight = $finder->ask(['250g', '500g', '1kg']);
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

#### Automatic update

To optimize the initial start, especially when CI/CD is used, it is recommended to add this to your `composer.json` file.

```json
{
    "scripts": {
        "post-update-cmd": [
            "./vendor/bin/fzf-php-install"
        ],
        "post-install-cmd": [
            "./vendor/bin/fzf-php-install"
        ]
    }
}
```

See also:
- [`fzf` GitHub page](https://github.com/junegunn/fzf)
- [`fzf` Documentation](https://junegunn.github.io/fzf)
- [Symfony Docs: Table](https://symfony.com/doc/current/components/console/helpers/table.html)
- [Symfony Docs: How to Style a Console Command](https://symfony.com/doc/current/console/style.html)
