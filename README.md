# imagick-layout-engine

[![Latest Version on Packagist](https://img.shields.io/packagist/v/kehet/imagick-layout-engine.svg?style=flat-square)](https://packagist.org/packages/kehet/imagick-layout-engine)
[![Tests](https://img.shields.io/github/actions/workflow/status/kehet/imagick-layout-engine/run-tests.yml?branch=master&label=tests&style=flat-square)](https://github.com/kehet/imagick-layout-engine/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/kehet/imagick-layout-engine.svg?style=flat-square)](https://packagist.org/packages/kehet/imagick-layout-engine)

A PHP library for creating complex image layouts with automatic positioning and sizing. This library provides a flexbox-like approach to image composition, making it easy to create structured layouts with text, images, and shapes.

## Requirements

- PHP 8.3 or higher
- Linux (Windows not tested)
- Imagick PHP extension

## Installation

You can install the package via composer:

```bash
composer require kehet/imagick-layout-engine
```

## Basic Usage

```php
// Create a new image
$width = 1500;
$height = 1000;
$imagick = new Imagick();
$imagick->newImage($width, $height, new ImagickPixel('white'));

// Create a row container with rectangles
$frame = new RowContainer();
$frame->addItem(new Rectangle(draw(fill: '#fee2e2')));
$frame->addItem(new Rectangle(draw(fill: '#fca5a5')));
$frame->addItem(new Rectangle(draw(fill: '#dc2626')));
$frame->addItem(new Rectangle(draw(fill: '#450a0a')));

// Draw container onto image
$frame->draw($imagick, 0, 0, $width, $height);

// Output image as PNG
$imagick->setImageFormat('png');
$imagick->writeImage('output.png');
```

For more examples, see [documentation](https://kehet.github.io/imagick-layout-engine-docs).

## Testing

```bash
composer test
```

### SAVE_SNAPSHOT
When set (to any value), snapshot images will be automatically written to `tests/__snapshots__/` if snapshot file is missing.
```bash
SAVE_SNAPSHOT=1 composer test -- --filter=YourTest
```

### SAVE_IMAGE_DIFF
When set (to any non-empty value), a visual diff image will be saved to `tests/temp/` whenever an image comparison is performed, regardless of pass/fail.
```bash
SAVE_IMAGE_DIFF=1 composer test -- --filter=YourTest
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Roadmap

- [x] Margin / padding
- [x] Borders
- [x] Item background
- [x] Extract helper function

## Credits

- [Kehet](https://github.com/Kehet)
- [All Contributors](../../contributors)

## License

GNU GENERAL PUBLIC LICENSE version 3. Please see [License File](LICENSE) for more information.
