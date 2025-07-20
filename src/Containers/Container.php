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

use Kehet\ImagickLayoutEngine\Items\DrawableInterface;

abstract class Container implements DrawableInterface
{
    /** @var array> */
    protected array $items;

    public function addItem(
        DrawableInterface $item,
        ?int $forceSize = null,
        int|array|null $padding = null,
    ): void {

        if ($padding !== null) {

            /*
             * When one value is specified, it applies the same padding to all four sides.
             * When two values are specified, the first padding applies to the top and bottom, the second to the left and right.
             * When three values are specified, the first padding applies to the top, the second to the right and left, the third to the bottom.
             * When four values are specified, the paddings apply to the top, right, bottom, and left in that order (clockwise).
             */

            if (!is_array($padding)) {
                $padding = [$padding];
            }

            $padding = match (count($padding)) {
                1 => [$padding[0], $padding[0], $padding[0], $padding[0]], // apply to all four sides
                2 => [$padding[0], $padding[1], $padding[0], $padding[1]], // top and bottom | left and right
                3 => [$padding[0], $padding[1], $padding[2], $padding[1]], // top | left and right | bottom
                default => $padding,
            };
        }

        $this->items[] = [
            'item'    => $item,
            'size'    => $forceSize,
            'padding' => $padding,
        ];
    }
}
