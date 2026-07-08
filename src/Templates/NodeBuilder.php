<?php

/*
 * The Imagick Layout Engine
 * Copyright (C) 2025 Kehet
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace Kehet\ImagickLayoutEngine\Templates;

use ImagickDraw;
use Kehet\ImagickLayoutEngine\Containers\ColumnContainer;
use Kehet\ImagickLayoutEngine\Containers\GridContainer;
use Kehet\ImagickLayoutEngine\Containers\RowContainer;
use Kehet\ImagickLayoutEngine\Containers\StackContainer;
use Kehet\ImagickLayoutEngine\Enums\Gravity;
use Kehet\ImagickLayoutEngine\Enums\ImageMode;
use Kehet\ImagickLayoutEngine\Items\DrawableInterface;
use Kehet\ImagickLayoutEngine\Items\Image;
use Kehet\ImagickLayoutEngine\Items\Rectangle;
use Kehet\ImagickLayoutEngine\Items\Text;
use Kehet\ImagickLayoutEngine\Items\TextWrap;
use Kehet\ImagickLayoutEngine\Templates\Exceptions\InvalidTemplateException;
use ValueError;

final class NodeBuilder
{
    public static function build(array $node, array $data): DrawableInterface
    {
        if (! isset($node['type']) || ! is_string($node['type'])) {
            throw new InvalidTemplateException('Template node is missing a "type" string.');
        }

        $item = match ($node['type']) {
            'row' => self::buildRow($node, $data),
            'column' => self::buildColumn($node, $data),
            'grid' => self::buildGrid($node, $data),
            'stack' => self::buildStack($node, $data),
            'text' => self::buildText($node, $data),
            'text-wrap' => self::buildTextWrap($node, $data),
            'image' => self::buildImage($node, $data),
            'rectangle' => self::buildRectangle($node),
            default => throw new InvalidTemplateException(sprintf('Unknown template node type "%s".', $node['type'])),
        };

        self::applySpacing($item, $node);

        return $item;
    }

    private static function buildRow(array $node, array $data): RowContainer
    {
        return self::buildLinearContainer(new RowContainer, $node, $data);
    }

    private static function buildColumn(array $node, array $data): ColumnContainer
    {
        return self::buildLinearContainer(new ColumnContainer, $node, $data);
    }

    private static function buildLinearContainer(RowContainer|ColumnContainer $container, array $node, array $data): RowContainer|ColumnContainer
    {
        if (isset($node['gap'])) {
            $container->setGap((int) $node['gap']);
        }

        foreach ($node['children'] ?? [] as $child) {
            $container->addItem(
                self::build($child, $data),
                isset($child['size']) ? (int) $child['size'] : null
            );
        }

        return $container;
    }

    private static function buildGrid(array $node, array $data): GridContainer
    {
        $container = new GridContainer;

        if (isset($node['templateColumns'])) {
            $container->setTemplateColumns(...self::intOrNullList($node['templateColumns']));
        }

        if (isset($node['templateRows'])) {
            $container->setTemplateRows(...self::intOrNullList($node['templateRows']));
        }

        if (isset($node['gap'])) {
            if (is_array($node['gap'])) {
                $rowGap = (int) $node['gap'][0];
                $columnGap = isset($node['gap'][1]) ? (int) $node['gap'][1] : null;
                $container->setGap($rowGap, $columnGap);
            } else {
                $container->setGap((int) $node['gap']);
            }
        }

        foreach ($node['children'] ?? [] as $child) {
            $container->addItem(
                self::build($child, $data),
                isset($child['column']) ? (int) $child['column'] : null,
                isset($child['row']) ? (int) $child['row'] : null,
                isset($child['columnSpan']) ? (int) $child['columnSpan'] : 1,
                isset($child['rowSpan']) ? (int) $child['rowSpan'] : 1,
            );
        }

        return $container;
    }

    private static function buildStack(array $node, array $data): StackContainer
    {
        $container = new StackContainer;

        foreach ($node['children'] ?? [] as $child) {
            $container->addItem(self::build($child, $data));
        }

        return $container;
    }

    /**
     * @param  array<int, int|null>  $values
     * @return array<int, int|null>
     */
    private static function intOrNullList(array $values): array
    {
        return array_map(static fn ($v) => $v === null ? null : (int) $v, $values);
    }

    private static function buildRectangle(array $node): Rectangle
    {
        return new Rectangle(self::buildDraw($node));
    }

    private static function buildText(array $node, array $data): Text
    {
        if (! isset($node['text']) || ! is_string($node['text'])) {
            throw new InvalidTemplateException('Template node of type "text" requires a "text" string.');
        }

        $item = new Text(
            self::buildDraw($node),
            self::substitute($node['text'], $data),
            isset($node['fontSize']) ? (int) $node['fontSize'] : 60,
            isset($node['minFontSize']) ? (int) $node['minFontSize'] : 10,
            self::gravity($node, Gravity::TOP_LEFT),
        );

        self::applyTextSpacing($item, $node);

        return $item;
    }

    private static function buildTextWrap(array $node, array $data): TextWrap
    {
        if (! isset($node['text']) || ! is_string($node['text'])) {
            throw new InvalidTemplateException('Template node of type "text-wrap" requires a "text" string.');
        }

        $item = new TextWrap(
            self::buildDraw($node),
            self::substitute($node['text'], $data),
            isset($node['fontSize']) ? (int) $node['fontSize'] : 60,
            isset($node['minFontSize']) ? (int) $node['minFontSize'] : 10,
            self::gravity($node, Gravity::TOP_LEFT),
        );

        self::applyTextSpacing($item, $node);

        if (isset($node['lineSpacing'])) {
            $item->setLineSpacing((int) $node['lineSpacing']);
        }

        return $item;
    }

    private static function buildImage(array $node, array $data): Image
    {
        if (! isset($node['file']) || ! is_string($node['file'])) {
            throw new InvalidTemplateException('Template node of type "image" requires a "file" string.');
        }

        $mode = ImageMode::NONE;

        if (isset($node['mode'])) {
            if (! is_string($node['mode'])) {
                throw new InvalidTemplateException('"mode" must be a string.');
            }

            try {
                $mode = ImageMode::from($node['mode']);
            } catch (ValueError) {
                throw new InvalidTemplateException(sprintf('Invalid "mode" value "%s".', $node['mode']));
            }
        }

        return new Image(
            self::assertLocalFilePath(self::substitute($node['file'], $data)),
            $mode,
            self::gravity($node, Gravity::CENTER),
        );
    }

    /**
     * Rejects ImageMagick coder/URI-scheme prefixes (e.g. "https:", "msl:",
     * "ephemeral:", "caption:", "pango:") before the path reaches Imagick's
     * file loader. Without this, a {{placeholder}} substituted into "file"
     * from render-time data could trigger SSRF or arbitrary-coder execution
     * (the "ImageTragick" class of vulnerabilities, CVE-2016-3714).
     */
    private static function assertLocalFilePath(string $path): string
    {
        if (preg_match('/^[a-zA-Z][a-zA-Z0-9+.-]*:/', $path) === 1) {
            throw new InvalidTemplateException(sprintf('Invalid "file" value "%s": ImageMagick coder/URI prefixes are not allowed.', $path));
        }

        return $path;
    }

    private static function applyTextSpacing(Text|TextWrap $item, array $node): void
    {
        if (isset($node['letterSpacing'])) {
            $item->setLetterSpacing((float) $node['letterSpacing']);
        }

        if (isset($node['wordSpacing'])) {
            $item->setWordSpacing((float) $node['wordSpacing']);
        }
    }

    private static function gravity(array $node, Gravity $default): Gravity
    {
        if (! isset($node['gravity'])) {
            return $default;
        }

        if (! is_string($node['gravity'])) {
            throw new InvalidTemplateException('"gravity" must be a string.');
        }

        try {
            return Gravity::from($node['gravity']);
        } catch (ValueError) {
            throw new InvalidTemplateException(sprintf('Invalid "gravity" value "%s".', $node['gravity']));
        }
    }

    private static function substitute(string $text, array $data): string
    {
        return preg_replace_callback(
            '/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/',
            static fn (array $matches) => array_key_exists($matches[1], $data) ? (string) $data[$matches[1]] : $matches[0],
            $text
        );
    }

    private static function buildDraw(array $node): ImagickDraw
    {
        $args = [];

        if (array_key_exists('fill', $node)) {
            $args['fill'] = $node['fill'];
        }

        if (array_key_exists('stroke', $node)) {
            $args['stroke'] = $node['stroke'];
        }

        if (array_key_exists('strokeWidth', $node)) {
            $args['strokeWidth'] = (int) $node['strokeWidth'];
        }

        if (array_key_exists('font', $node)) {
            $args['font'] = $node['font'];
        }

        return \draw(...$args);
    }

    private static function applySpacing(DrawableInterface $item, array $node): void
    {
        self::applyMarginOrPadding($item, $node['margin'] ?? null, 'Margin');
        self::applyMarginOrPadding($item, $node['padding'] ?? null, 'Padding');
        self::applyBorder($item, $node['border'] ?? null);
    }

    private static function applyMarginOrPadding(DrawableInterface $item, mixed $spec, string $which): void
    {
        if ($spec === null) {
            return;
        }

        if (is_numeric($spec)) {
            $setAll = 'set'.$which;
            $item->{$setAll}((int) $spec);

            return;
        }

        if (! is_array($spec)) {
            throw new InvalidTemplateException(sprintf('"%s" must be a number or an object.', strtolower($which)));
        }

        foreach (['Top', 'Right', 'Bottom', 'Left'] as $side) {
            $key = lcfirst($side);

            if (isset($spec[$key])) {
                $setter = 'set'.$which.$side;
                $item->{$setter}((int) $spec[$key]);
            }
        }
    }

    private static function applyBorder(DrawableInterface $item, mixed $spec): void
    {
        if ($spec === null) {
            return;
        }

        if (! is_array($spec)) {
            throw new InvalidTemplateException('"border" must be an object.');
        }

        $sideKeys = ['top', 'right', 'bottom', 'left'];
        $hasSideKeys = array_intersect_key($spec, array_flip($sideKeys)) !== [];

        if (! $hasSideKeys) {
            $item->setBorder(self::buildBorderDraw($spec));

            return;
        }

        foreach (['Top', 'Right', 'Bottom', 'Left'] as $side) {
            $key = lcfirst($side);

            if (isset($spec[$key])) {
                $setter = 'setBorder'.$side;
                $item->{$setter}(self::buildBorderDraw($spec[$key]));
            }
        }
    }

    private static function buildBorderDraw(array $spec): ImagickDraw
    {
        return \draw(
            fill: $spec['fill'] ?? null,
            stroke: $spec['stroke'] ?? null,
            strokeWidth: isset($spec['strokeWidth']) ? (int) $spec['strokeWidth'] : 1,
            font: null,
        );
    }
}
