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

namespace Kehet\ImagickLayoutEngine\Items;

use Imagick;
use ImagickDraw;

/**
 * Represents a drawable rectangle that can be drawn onto container grid.
 */
class Rectangle implements DrawableInterface
{
    public function __construct(
        protected ImagickDraw $draw,
    ) {}

    public function draw(Imagick $imagick, int $x, int $y, int $width, int $height): void
    {
        $this->draw->rectangle($x, $y, $x + $width, $y + $height);

        $imagick->drawImage($this->draw);
    }
}
