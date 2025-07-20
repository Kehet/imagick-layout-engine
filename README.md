# This is my package imagick-layout-engine

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
// Helper function to create ImagickDraw objects with fill color
function fill(string $fill): ImagickDraw
{
    $return = new \ImagickDraw();
    $return->setFillColor(new \ImagickPixel($fill));
    return $return;
}

// Create a new image
$width = 1500;
$height = 1000;
$imagick = new Imagick();
$imagick->newImage($width, $height, new ImagickPixel('white'));

// Create a row container with rectangles
$frame = new RowContainer();
$frame->addItem(new Rectangle(fill('#fee2e2')));
$frame->addItem(new Rectangle(fill('#fca5a5')));
$frame->addItem(new Rectangle(fill('#dc2626')));
$frame->addItem(new Rectangle(fill('#450a0a')));

// Draw container onto image
$frame->draw($imagick, 0, 0, $width, $height);

// Output image as PNG
$imagick->setImageFormat('png');
$imagick->writeImage('output.png');
```

For more examples, see [examples/](examples/)

## Features

### Containers

The library provides two types of containers for layout:

- **RowContainer**: Arranges items horizontally
- **ColumnContainer**: Arranges items vertically

Containers can be nested to create complex layouts. When adding items to containers, you can specify an optional weight parameter to control the item's size relative to other items.

```php
$row = new RowContainer();
$row->addItem(new Rectangle(fill('#fee2e2')), 50);  // 50 px wide
$row->addItem(new Rectangle(fill('#fca5a5')), 100); // 100 px wide
$row->addItem(new Rectangle(fill('#dc2626')));      // Takes remaining space
```

### Text Handling

The library provides two classes for text handling:

- **Text**: Renders text with automatic font size adjustment to fit within the container
- **TextWrap**: Renders text with automatic wrapping and font size adjustment

```php
// Text with auto-sizing
$container->addItem(
    new Text(
        fill('#000'),
        'Lorem Ipsum Dolor',
        initialFontSize: 120,
        minFontSize: 50
    )
);

// Text with auto-wrapping and sizing
$container->addItem(
    new TextWrap(
        fill('#000'),
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
        initialFontSize: 60,
        minFontSize: 10
    )
);
```

### Image Handling

The library provides an Image class for adding images to layouts with various fitting and positioning options:

```php
// Default behavior (fit) - maintains aspect ratio
$container->addItem(new Image('path/to/image.jpg'));

// Fill mode - image will be cropped to fill the container
$container->addItem(new Image('path/to/image.jpg', fill: true));

// With gravity option for positioning
$container->addItem(new Image('path/to/image.jpg', fill: false, gravity: Image::GRAVITY_TOP));
```

Available gravity options:
- `Image::GRAVITY_TOP_LEFT`
- `Image::GRAVITY_TOP`
- `Image::GRAVITY_TOP_RIGHT`
- `Image::GRAVITY_LEFT`
- `Image::GRAVITY_CENTER` (default)
- `Image::GRAVITY_RIGHT`
- `Image::GRAVITY_BOTTOM_LEFT`
- `Image::GRAVITY_BOTTOM`
- `Image::GRAVITY_BOTTOM_RIGHT`

## Advanced Examples

### Nested Containers

```php
// Create a column container with multiple row containers
$frame = new ColumnContainer();

// First row
$row = new RowContainer();
$row->addItem(new Rectangle(fill('#fee2e2')), 50);
$row->addItem(new Rectangle(fill('#fca5a5')), 100);
$row->addItem(new Rectangle(fill('#dc2626')), 150);
$row->addItem(new Rectangle(fill('#450a0a')));
$frame->addItem($row);

// Second row
$row = new RowContainer();
$row->addItem(new Rectangle(fill('#ecfccb')));
$row->addItem(new Rectangle(fill('#bef264')), 50);
$row->addItem(new Rectangle(fill('#65a30d')), 100);
$row->addItem(new Rectangle(fill('#1a2e05')), 150);
$frame->addItem($row);
```

### Text and Images Together

```php
// Create a column container with an image and text
$container = new ColumnContainer();
$container->addItem(new Image('path/to/image.jpg', fill: false, gravity: Image::GRAVITY_CENTER));
$container->addItem(new TextWrap(fill('black'), 'Image Caption'));
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Roadmap

- [ ] Margin / padding
- [ ] Text background
- [ ] Extract helper functions

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Kehet](https://github.com/Kehet)
- [All Contributors](../../contributors)

## License

GNU GENERAL PUBLIC LICENSE version 3. Please see [License File](LICENSE.md) for more information.
