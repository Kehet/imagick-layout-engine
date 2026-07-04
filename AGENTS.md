# AGENTS.md

This file provides guidance to AI coding agents when working with code in this repository.

## What this is

A PHP 8.3+ library that provides a flexbox-like layout engine on top of Imagick, for composing images from
rows/columns/stacks of text, images, and rectangles with margin/padding/border support.

## Commands

```bash
composer install          # install dependencies
composer test              # run the full PHPUnit suite (vendor/bin/phpunit)
composer test -- --filter=TestName   # run a single test
composer format             # run Laravel Pint code formatter
```

Snapshot tests compare rendered PNGs against files in `tests/__snapshots__/`. Env vars control snapshot behavior:

```bash
SAVE_SNAPSHOT=1 composer test -- --filter=YourTest    # write snapshot if missing (first run for a new test)
SAVE_IMAGE_DIFF=1 composer test -- --filter=YourTest  # write a diff image to tests/temp/ regardless of pass/fail
```

Requires the Imagick PHP extension and the `DejaVu-Sans` font (used in tests; available on GH runners and most
Linux distros — check with `\Imagick::queryFonts('*')`).

## Architecture

Everything that can be drawn implements `DrawableInterface` (`src/Items/DrawableInterface.php`):

```php
public function draw(Imagick $imagick, int $x, int $y, int $width, int $height): void;
```

This single-method contract is the entire integration point — containers call `draw()` on their children with a
computed bounding box, and items call `draw()` on the underlying `Imagick` instance. There is no separate
measure/layout pass; sizing decisions happen inline during `draw()`.

**Containers** (`src/Containers/`) are also `DrawableInterface` implementations that hold child items and
distribute space among them:
- `Container` (abstract base) — holds the `items` array (`{item, size}` pairs) and `addItem($item, ?$forceSize)`.
  `$forceSize` pins an item to an exact width (RowContainer) or height (ColumnContainer) in pixels; unforced
  items split the remaining space evenly, with leftover rounding pixels dumped onto the last unforced item.
- `RowContainer` — lays children out horizontally, distributing `width`.
- `ColumnContainer` — lays children out vertically, distributing `height`.
- `StackContainer` — draws all children on top of each other in the same content box (e.g. background + overlay
  text). Ignores `size` (all children get the full content area).

Containers nest arbitrarily (a `RowContainer` can contain `ColumnContainer`s, etc.), which is how complex layouts
are built.

**Items** (`src/Items/`) are the leaf nodes: `Rectangle`, `Text`, `TextWrap`, `Image`. `Text` shrinks font size
from `initialFontSize` down to `minFontSize` until it fits the box (single line, no wrapping — truncates by
measuring, doesn't chop the string). `TextWrap` does the same but wraps onto multiple lines first, then shrinks
font size if the wrapped block still doesn't fit vertically. `Image` supports three `ImageMode`s: `NONE`
(crop only if larger), `FIT` (scale to fit, preserving aspect ratio, no crop), `FILL` (scale + crop to exactly
fill the box). `Gravity` (`src/Enums/Gravity.php`) controls alignment/crop-anchor for both `Image` and text items.

**Shared box-model traits** (`src/Traits/`) — `MarginTrait`, `PaddingTrait`, `BorderTrait` — are mixed into both
containers and items, giving every drawable a consistent CSS-like box model. Each `draw()` implementation applies
them in the same fixed order: shrink the box by margin → note border-box edges → shrink by border width → shrink
by padding → draw content in the remaining box → draw borders last (using the border-box edges, drawn as
1px-inset lines rather than filled rectangles, hence the half-stroke-width insets in `BorderTrait`). When adding
a new item or container, follow this same margin→border→padding→content→border-draw sequence for consistency.

`setBorder()`, `setMargin()`, `setPadding()` all support CSS shorthand argument counts (1 = all sides, 2 =
vertical/horizontal, 3 = top/horizontal/bottom, 4 = top/right/bottom/left).

`src/helpers.php` provides a global `draw(?fill, ?stroke, ?strokeWidth)` helper that builds an `ImagickDraw` —
used pervasively in tests and the README example for terse Rectangle/shape construction.

## Testing conventions

- Tests extend `Tests\TestCase`, which provides `createImage()` (1500x1000 white canvas), `draw()`/`stroke()`
  helpers for building `ImagickDraw` fill/stroke styles, and text fixtures (`TINY_TEXT`/`SHORT_TEXT`/`LONG_TEXT`)
  and image fixtures (`TINY_IMAGE`/`SMALL_IMAGE`/`LARGE_IMAGE`, in `tests/assets/`).
- `saveImage($imagick, $container, $filename)` draws the container onto the full canvas, writes to
  `tests/temp/`, and asserts pixel-level equality against `tests/__snapshots__/$filename` (5% MAE tolerance via
  `assertImageEquals` in `tests/Traits/HasImageAssertion.php`).
- To add a new snapshot test: write the test calling `saveImage()`, run once with `SAVE_SNAPSHOT=1` to generate
  the baseline (test will fail/incomplete on that run), then run again normally to verify it passes.
- To intentionally update a snapshot, delete the old file under `tests/__snapshots__/` and regenerate with
  `SAVE_SNAPSHOT=1`.
- Test file naming mirrors the feature under test (`BorderTest`, `MarginTest`, `PaddingTest`, `ContainerTest`,
  `ImageTest`, `TextTest`, `TextWrapTest`, `StackContainerTest`), not one-to-one with `src/` classes — most
  suites exercise combinations of containers/items/traits together, not a single class in isolation.
