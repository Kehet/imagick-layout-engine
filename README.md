<!--suppress HtmlDeprecatedAttribute -->
<p align="center"><img src="/art/logo.svg" width="50%" alt="Logo Imagick Layout Engine"></p>

<p align="center">
    <a href="https://github.com/Kehet/imagick-layout-engine/actions"><img src="https://github.com/Kehet/imagick-layout-engine/actions/workflows/run-tests.yml/badge.svg" alt="Build Status"></a>
    <a href="https://packagist.org/packages/Kehet/imagick-layout-engine"><img src="https://img.shields.io/packagist/dt/Kehet/imagick-layout-engine" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/Kehet/imagick-layout-engine"><img src="https://img.shields.io/packagist/v/Kehet/imagick-layout-engine" alt="Latest Stable Version"></a>
    <a href="https://packagist.org/packages/Kehet/imagick-layout-engine"><img src="https://img.shields.io/packagist/l/Kehet/imagick-layout-engine" alt="License"></a>
</p>

A PHP library for creating complex image layouts with automatic positioning and sizing. This library provides a flexbox-like approach to image composition, making it easy to create structured layouts with text, images, and shapes.

## Requirements

- PHP 8.3 or later
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
When set, if snapshot file is missing, snapshot image will be automatically written to `tests/__snapshots__/`.
```bash
SAVE_SNAPSHOT=1 composer test -- --filter=YourTest
```

### SAVE_IMAGE_DIFF
When set, a visual diff image will be saved to `tests/temp/` whenever an image comparison is performed, regardless of pass/fail.
```bash
SAVE_IMAGE_DIFF=1 composer test -- --filter=YourTest
```

### Running tests in Docker

If you'd rather not use your host PHP install, a `Dockerfile` is provided to run the test suite in a container
instead. It doesn't copy the source or install dependencies at build time — the project directory is
bind-mounted at run time, so code changes are picked up without rebuilding. Build with `USER_ID`/`GROUP_ID` set
to your host user so bind-mounted output (`tests/temp/`, `vendor/`) is written back with your own permissions
instead of the container's:

```bash
docker build --build-arg USER_ID="$(id -u)" --build-arg GROUP_ID="$(id -g)" -t imagick-layout-engine-test .
```

Install dependencies once (and again whenever `composer.json`/`composer.lock` change) by running Composer
inside the container — it writes `vendor/` straight to the host through the bind mount:
```bash
docker run --rm -v "$(pwd):/var/www/html" imagick-layout-engine-test composer install --no-interaction --no-progress --prefer-dist
```

Run the suite:
```bash
docker run --rm -v "$(pwd):/var/www/html" imagick-layout-engine-test
```

To inspect snapshot diffs on the host, set `SAVE_IMAGE_DIFF` (they land in `tests/temp/` since the project is
already mounted):
```bash
docker run --rm -e SAVE_IMAGE_DIFF=1 -v "$(pwd):/var/www/html" imagick-layout-engine-test
```

To write a missing snapshot, set `SAVE_SNAPSHOT`:
```bash
docker run --rm -e SAVE_SNAPSHOT=1 -v "$(pwd):/var/www/html" \
  imagick-layout-engine-test composer test -- --filter=YourTest
```

## Roadmap

Please see [ROADMAP](ROADMAP.md) for more information on the future of this project.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Kehet](https://github.com/Kehet)
- [All Contributors](../../contributors)

## License

GNU GENERAL PUBLIC LICENSE version 3 or later. Please see [License File](LICENSE) for more information.
