# Imagick Layout Engine Development Guidelines

This document provides guidelines and instructions for developing and testing the Imagick Layout Engine project.

## Build and Configuration Instructions

### Requirements

- PHP 8.3 or higher
- Linux environment (Windows is not officially supported)
- Imagick PHP extension
- Composer for dependency management
- Node.js and npm for image comparison in tests

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/kehet/imagick-layout-engine.git
   cd imagick-layout-engine
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install Node.js dependencies:
   ```bash
   npm ci
   ```

### Environment Setup

The project requires the Imagick PHP extension with font support. The test suite uses the "DejaVu-Sans" font, which should be available on most Linux distributions. You can check available fonts with:

```php
print_r(\Imagick::queryFonts('*'));
```

## Testing Information

### Test Framework

The project uses PHPUnit for testing, with the following additional components:
- Spatie's PHPUnit snapshot assertions for image comparison
- Pixelmatch (Node.js library) for pixel-by-pixel image comparison

### Running Tests

Run all tests:
```bash
composer test
```

Run a specific test:
```bash
composer test -- --filter=TestName
```

### Test Structure

Tests are located in the `tests` directory and follow the standard PHPUnit structure:
- `TestCase.php` - Base test class with utility methods
- Test files named after the component they test (e.g., `ContainerTest.php`)

### Creating New Tests

1. Create a new test file in the `tests` directory that extends `TestCase`:
   ```php
   <?php

   namespace Kehet\ImagickLayoutEngine\Tests;

   use Kehet\ImagickLayoutEngine\Containers\RowContainer;
   use Kehet\ImagickLayoutEngine\Items\Rectangle;

   class YourTest extends TestCase
   {
       public function test_your_feature(): void
       {
           $imagick = $this->createImage();

           // Set up your test scenario
           $container = new RowContainer();
           $container->addItem(new Rectangle($this->draw('#4ade80')));

           // Save and compare with snapshot
           $this->saveImage($imagick, $container, __FUNCTION__.'.png');
       }
   }
   ```

2. Run the test to generate a snapshot:
   ```bash
   composer test -- --filter=YourTest
   ```

3. The first run will mark the test as incomplete and create a snapshot in `tests/__snapshots__`.

4. Run the test again to verify it passes:
   ```bash
   composer test -- --filter=YourTest
   ```

### Snapshot Testing

The project uses snapshot testing to verify that the generated images match the expected output:

- Snapshots are stored in the `tests/__snapshots__` directory
- The `saveImage()` method in `TestCase` saves the image to `tests/temp/` and compares it with the snapshot
- The comparison allows for a 10% difference (0.1 tolerance)
- If the image doesn't match the snapshot, the test will fail

To update snapshots when you intentionally change the output:
```bash
composer test -- --update-snapshots
```

## Development Guidelines

### Code Organization

The project is organized into the following directories:
- `src/Containers` - Container classes for layout (RowContainer, ColumnContainer)
- `src/Items` - Drawable items (Rectangle, Text, TextWrap, Image)

### Class Structure

All drawable elements implement the `DrawableInterface`, which requires a `draw()` method:
```php
public function draw(Imagick $imagick, int $x, int $y, int $width, int $height): void;
```

### Containers

Containers are responsible for arranging items in a layout:
- `RowContainer` - Arranges items horizontally
- `ColumnContainer` - Arranges items vertically

When adding items to containers, you can specify an optional weight parameter to control the item's size:
```php
$container->addItem($item, $weight);
```

### Code Style

The project uses Laravel Pint for code formatting:
```bash
composer format
```

### Debugging Tips

1. To debug image generation, examine the temporary images in `tests/temp/`.
2. For font issues, check available fonts with `\Imagick::queryFonts('*')`.
3. If tests fail due to image differences, compare the generated image with the snapshot manually.

## Continuous Integration

The project uses GitHub Actions for CI testing:
- Tests run on PHP 8.3 and 8.4
- Tests run with both prefer-lowest and prefer-stable dependency configurations
- The workflow is defined in `.github/workflows/run-tests.yml`
