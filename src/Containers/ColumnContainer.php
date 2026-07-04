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
    protected int $gap = 0;

    /**
     * Sets the vertical space, in pixels, inserted between adjacent items.
     */
    public function setGap(int $gap): self
    {
        $this->gap = $gap;

        return $this;
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

        $itemX = 0;
        $itemY = 0;
        $itemWidth = $width;

        $itemCount = count($this->items);
        $availableHeight = $height - $this->gap * max(0, $itemCount - 1);

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
        if ($itemCount > $countForcedHeight) {
            $notForcedHeight = round(($availableHeight - $totalForcedHeight) / ($itemCount - $countForcedHeight));
        }

        // since there can't be sub-pixel sizes, dump all remaining width to last item without forced height
        // (no-one will notice (or if they do, they should use size forcing))
        $cheat = 0;
        if (($totalForcedHeight + $itemCount * $notForcedHeight) < $availableHeight) {
            $cheat = $availableHeight - ($totalForcedHeight + $itemCount * $notForcedHeight);
        }

        foreach ($this->items as $key => $item) {
            $currentHeight = $item['size'] ?? $notForcedHeight;

            if ($key === $lastKeyWithoutForcing) {
                $currentHeight += $cheat;
            }

            // Calculate content area with padding
            $contentX = $itemX;
            $contentY = $itemY;
            $contentWidth = $itemWidth;
            $contentHeight = $currentHeight;

            // Ensure content dimensions are at least 1 pixel
            $contentWidth = max(1, $contentWidth);
            $contentHeight = max(1, $contentHeight);

            $item['item']->draw($imagick, $x + $contentX, $y + $contentY, $contentWidth, $contentHeight);

            // Move to next item position, including bottom margin and gap
            $itemY += $currentHeight + $this->gap;
        }

        $this->drawBorders($imagick, $borderX, $borderY, $borderWidth, $borderHeight);
    }
}
