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
 * A container class that arranges its child elements in a horizontal row layout.
 *
 * This class is responsible for distributing the available width among its child elements
 * and rendering them accordingly.
 */
class RowContainer extends Container
{
    public function draw(Imagick $imagick, int $x, int $y, int $width, int $height): void
    {
        $itemX = 0;
        $itemY = 0;
        $itemHeight = $height;

        $totalForcedWidth = 0;
        $countForcedWidth = 0;

        $lastKeyWithoutForcing = null;

        foreach ($this->items as $key => $item) {
            if ($item['size'] !== null) {
                $totalForcedWidth += $item['size'];
                $countForcedWidth++;
            } else {
                $lastKeyWithoutForcing = $key;
            }
        }

        $notForcedWidth = 10;
        if (count($this->items) > $countForcedWidth) {
            $notForcedWidth = round(($width - $totalForcedWidth) / (count($this->items) - $countForcedWidth));
        }

        // since there can't be sub-pixel sizes, dump all remaining width to last item without forced width
        // (no-one will notice (or if they do, they should use size forcing))
        $cheat = 0;
        if (($totalForcedWidth + count($this->items) * $notForcedWidth) < $width) {
            $cheat = $width - ($totalForcedWidth + count($this->items) * $notForcedWidth);
        }

        foreach ($this->items as $key => $item) {
            $currentWidth = $item['size'] ?? $notForcedWidth;

            if ($key === $lastKeyWithoutForcing) {
                $currentWidth += $cheat;
            }

            $item['item']->draw($imagick, $x + $itemX, $y + $itemY, $currentWidth, $itemHeight);

            $itemX += $currentWidth;
        }
    }
}
