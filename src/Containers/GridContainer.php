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

namespace Kehet\ImagickLayoutEngine\Containers;

use Imagick;
use Kehet\ImagickLayoutEngine\Items\DrawableInterface;

/**
 * A container that arranges its child elements onto a two-dimensional grid,
 * similar to CSS `display: grid`.
 *
 * Columns and rows are defined as tracks via setTemplateColumns()/setTemplateRows():
 * a fixed pixel size, or `null` for a track that shares the remaining space evenly
 * with the other `null` tracks on that axis (comparable to an unforced item in
 * RowContainer/ColumnContainer). Items are placed onto explicit column/row
 * coordinates (both given together), or auto-placed in row-major order into the
 * next free cell when neither is given.
 */
class GridContainer extends Container
{
    /** @var array<int, int|null> */
    protected array $templateColumns = [null];

    /** @var array<int, int|null> */
    protected array $templateRows = [];

    protected int $rowGap = 0;

    protected int $columnGap = 0;

    public function setTemplateColumns(?int ...$columns): self
    {
        $this->templateColumns = $columns !== [] ? array_values($columns) : [null];

        return $this;
    }

    public function setTemplateRows(?int ...$rows): self
    {
        $this->templateRows = array_values($rows);

        return $this;
    }

    /**
     * CSS `gap` shorthand: one argument sets both axes, two arguments set
     * row-gap and column-gap respectively.
     */
    public function setGap(int $rowGap, ?int $columnGap = null): self
    {
        $this->rowGap = $rowGap;
        $this->columnGap = $columnGap ?? $rowGap;

        return $this;
    }

    public function addItem(
        DrawableInterface $item,
        ?int $column = null,
        ?int $row = null,
        int $columnSpan = 1,
        int $rowSpan = 1
    ): void {
        $this->items[] = [
            'item' => $item,
            'column' => $column,
            'row' => $row,
            'columnSpan' => max(1, $columnSpan),
            'rowSpan' => max(1, $rowSpan),
        ];
    }

    public function draw(Imagick $imagick, int $x, int $y, int $width, int $height): void
    {
        [$x, $y, $width, $height] = $this->getBoundingBoxInsideMargin($x, $y, $width, $height);

        $borderX = $x;
        $borderY = $y;
        $borderWidth = $width;
        $borderHeight = $height;

        [$x, $y, $width, $height] = $this->getBoundingBoxInsideBorder($x, $y, $width, $height);
        [$x, $y, $width, $height] = $this->getBoundingBoxInsidePadding($x, $y, $width, $height);

        $columnCount = count($this->templateColumns);

        $placements = $this->resolvePlacements($columnCount);

        $rowCount = max(count($this->templateRows), $this->highestOccupiedRow($placements) + 1, 1);

        $columnWidths = $this->resolveTrackSizes($this->templateColumns, $columnCount, $width, $this->columnGap);
        $rowHeights = $this->resolveTrackSizes($this->padTracks($this->templateRows, $rowCount), $rowCount, $height, $this->rowGap);

        $columnOffsets = $this->resolveTrackOffsets($columnWidths, $this->columnGap);
        $rowOffsets = $this->resolveTrackOffsets($rowHeights, $this->rowGap);

        foreach ($placements as $key => [$column, $row]) {
            $item = $this->items[$key];

            $columnSpan = min($item['columnSpan'], $columnCount - $column);
            $rowSpan = min($item['rowSpan'], $rowCount - $row);

            $contentX = $columnOffsets[$column];
            $contentY = $rowOffsets[$row];
            $contentWidth = array_sum(array_slice($columnWidths, $column, $columnSpan)) + $this->columnGap * ($columnSpan - 1);
            $contentHeight = array_sum(array_slice($rowHeights, $row, $rowSpan)) + $this->rowGap * ($rowSpan - 1);

            $contentWidth = max(1, $contentWidth);
            $contentHeight = max(1, $contentHeight);

            $item['item']->draw(
                $imagick,
                $x + $contentX,
                $y + $contentY,
                $contentWidth,
                $contentHeight
            );
        }

        $this->drawBorders($imagick, $borderX, $borderY, $borderWidth, $borderHeight);
    }

    /**
     * Resolves the final [column, row] for every item. Items with both a
     * column and a row explicitly set keep that placement (clamped onto the
     * declared column tracks); every other item is auto-placed into the next
     * free cell in row-major order (left to right, top to bottom), skipping
     * cells already occupied by another item's span. Explicit rows beyond
     * the declared row tracks are allowed and simply grow the grid, the same
     * way CSS grid's implicit rows work.
     *
     * @return array<int, array{0: int, 1: int}>
     */
    private function resolvePlacements(int $columnCount): array
    {
        $occupied = [];

        $markOccupied = static function (int $column, int $row, int $columnSpan, int $rowSpan) use (&$occupied): void {
            for ($r = $row; $r < $row + $rowSpan; $r++) {
                for ($c = $column; $c < $column + $columnSpan; $c++) {
                    $cell = $r.':'.$c;
                    $occupied[$cell] = true;
                }
            }
        };

        $isFree = static function (int $column, int $row, int $columnSpan) use (&$occupied, $columnCount): bool {
            if ($column + $columnSpan > $columnCount) {
                return false;
            }

            for ($c = $column; $c < $column + $columnSpan; $c++) {
                $cell = $row.':'.$c;
                if (isset($occupied[$cell])) {
                    return false;
                }
            }

            return true;
        };

        $explicitColumns = [];

        // Reserve explicit placements first, so auto-placed items flow around them.
        foreach ($this->items as $key => $item) {
            if ($item['column'] !== null && $item['row'] !== null) {
                $column = min($item['column'], $columnCount - 1);
                $explicitColumns[$key] = $column;
                $markOccupied($column, $item['row'], min($item['columnSpan'], $columnCount - $column), $item['rowSpan']);
            }
        }

        $placements = [];
        $cursorColumn = 0;
        $cursorRow = 0;

        foreach ($this->items as $key => $item) {
            if (isset($explicitColumns[$key])) {
                $placements[$key] = [$explicitColumns[$key], $item['row']];

                continue;
            }

            $columnSpan = min($item['columnSpan'], $columnCount);

            while (! $isFree($cursorColumn, $cursorRow, $columnSpan)) {
                $cursorColumn++;
                if ($cursorColumn >= $columnCount) {
                    $cursorColumn = 0;
                    $cursorRow++;
                }
            }

            $placements[$key] = [$cursorColumn, $cursorRow];
            $markOccupied($cursorColumn, $cursorRow, $columnSpan, $item['rowSpan']);

            $cursorColumn += $columnSpan;
            if ($cursorColumn >= $columnCount) {
                $cursorColumn = 0;
                $cursorRow++;
            }
        }

        return $placements;
    }

    /**
     * @param  array<int, array{0: int, 1: int}>  $placements
     */
    private function highestOccupiedRow(array $placements): int
    {
        $highest = 0;

        foreach ($placements as $key => [$column, $row]) {
            $highest = max($highest, $row + $this->items[$key]['rowSpan'] - 1);
        }

        return $highest;
    }

    /**
     * @param  array<int, int|null>  $tracks
     * @return array<int, int|null>
     */
    private function padTracks(array $tracks, int $count): array
    {
        while (count($tracks) < $count) {
            $tracks[] = null;
        }

        return $tracks;
    }

    /**
     * Distributes $totalSize across $count tracks: fixed (non-null) tracks
     * keep their pixel size, `null` tracks split what's left evenly, and any
     * leftover rounding pixels are dumped onto the last `null` track (or the
     * last track, if every track is fixed) — mirroring RowContainer's/
     * ColumnContainer's unforced-item distribution.
     *
     * @param  array<int, int|null>  $tracks
     * @return array<int, int>
     */
    private function resolveTrackSizes(array $tracks, int $count, int $totalSize, int $gap): array
    {
        $availableSize = $totalSize - $gap * max(0, $count - 1);

        $totalFixed = 0;
        $countFixed = 0;
        $lastAutoKey = null;

        foreach ($tracks as $key => $track) {
            if ($track !== null) {
                $totalFixed += $track;
                $countFixed++;
            } else {
                $lastAutoKey = $key;
            }
        }

        $autoSize = 10;
        if ($count > $countFixed) {
            $autoSize = (int) round(($availableSize - $totalFixed) / ($count - $countFixed));
        }

        $lastKey = $lastAutoKey ?? $count - 1;

        $sizes = [];
        $runningTotal = 0;
        foreach (range(0, $count - 1) as $key) {
            $size = $tracks[$key] ?? $autoSize;
            $sizes[$key] = $size;
            $runningTotal += $size;
        }

        $cheat = $availableSize - $runningTotal;
        if ($cheat !== 0) {
            $sizes[$lastKey] += $cheat;
        }

        return array_map(static fn ($size) => max(1, $size), $sizes);
    }

    /**
     * @param  array<int, int>  $sizes
     * @return array<int, int>
     */
    private function resolveTrackOffsets(array $sizes, int $gap): array
    {
        $offsets = [];
        $offset = 0;

        foreach ($sizes as $key => $size) {
            $offsets[$key] = $offset;
            $offset += $size + $gap;
        }

        return $offsets;
    }
}
