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

/**
 * A container class that arranges its child elements in a vertical column layout.
 *
 * This class is responsible for distributing the available height among its child elements
 * and rendering them accordingly.
 */
class ColumnContainer extends Container
{
    public function draw(Imagick $imagick, int $x, int $y, int $width, int $height): void
    {
        $itemX = 0;
        $itemY = 0;
        $itemWidth = $width;

        $totalForcedHeight = 0;
        $countForcedHeight = 0;

        $lastKeyWithoutForcing = null;

        foreach ($this->items as $key => $item) {
            if ($item['size'] !== null) {
                $totalForcedHeight += $item['size'];
                $countForcedHeight++;
            } else {
                $lastKeyWithoutForcing = $key;
            }
        }

        $notForcedHeight = 10;
        if (count($this->items) > $countForcedHeight) {
            $notForcedHeight = round(($height - $totalForcedHeight) / (count($this->items) - $countForcedHeight));
        }

        // since there can't be sub-pixel sizes, dump all remaining width to last item without forced height
        // (no-one will notice (or if they do, they should use size forcing))
        $cheat = 0;
        if (($totalForcedHeight + count($this->items) * $notForcedHeight) < $height) {
            $cheat = $height - ($totalForcedHeight + count($this->items) * $notForcedHeight);
        }

        foreach ($this->items as $key => $item) {
            $currentHeight = $item['size'] ?? $notForcedHeight;

            $paddingTop = $item['padding'][0] ?? 0;
            $paddingRight = $item['padding'][1] ?? 0;
            $paddingBottom = $item['padding'][2] ?? 0;
            $paddingLeft = $item['padding'][3] ?? 0;

            if ($key === $lastKeyWithoutForcing) {
                $currentHeight += $cheat;
            }

            // Calculate content area with padding
            $contentX = $itemX + $paddingLeft;
            $contentY = $itemY + $paddingTop;
            $contentWidth = $itemWidth - $paddingLeft - $paddingRight;
            $contentHeight = $currentHeight - $paddingTop - $paddingBottom;

            // Ensure content dimensions are at least 1 pixel
            $contentWidth = max(1, $contentWidth);
            $contentHeight = max(1, $contentHeight);

            $item['item']->draw($imagick, $x + $contentX, $y + $contentY, $contentWidth, $contentHeight);

            // Move to next item position, including bottom margin
            $itemY += $currentHeight;
        }
    }
}
