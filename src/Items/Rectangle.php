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
use Kehet\ImagickLayoutEngine\Traits\BorderTrait;
use Kehet\ImagickLayoutEngine\Traits\MarginTrait;
use Kehet\ImagickLayoutEngine\Traits\PaddingTrait;

/**
 * Represents a drawable rectangle that can be drawn onto container grid.
 */
class Rectangle implements DrawableInterface
{
    use BorderTrait;
    use MarginTrait;
    use PaddingTrait;

    public function __construct(
        protected ImagickDraw $draw,
    ) {}

    public function draw(Imagick $imagick, int $x, int $y, int $width, int $height): void
    {
        [$x, $y, $width, $height] = $this->getBoundingBoxInsideMargin($x, $y, $width, $height);

        $borderX = $x;
        $borderY = $y;
        $borderWidth = $width;
        $borderHeight = $height;

        [$x, $y, $width, $height] = $this->getBoundingBoxInsideBorder($x, $y, $width, $height);
        [$x, $y, $width, $height] = $this->getBoundingBoxInsidePadding($x, $y, $width, $height);

        $this->draw->rectangle($x, $y, $x + $width, $y + $height);

        $imagick->drawImage($this->draw);

        $this->drawBorders($imagick, $borderX, $borderY, $borderWidth, $borderHeight);
    }
}
