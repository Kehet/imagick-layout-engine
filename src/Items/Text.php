<?php

/*
 * The Imagick Layout Engine
 * Copyright (C) 2025
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
 * Represents an simple text that can be drawn onto container grid. Scales font size down as needed.
 */
class Text implements DrawableInterface
{
    public function __construct(
        protected ImagickDraw $draw,
        protected string $text,
        protected int $initialFontSize = 60,
        protected int $minFontSize = 10,
    ) {}

    public function draw(Imagick $imagick, int $x, int $y, int $width, int $height): void
    {
        $this->draw->setGravity(Imagick::GRAVITY_NORTHWEST);

        $fontSize = $this->initialFontSize;

        do {
            $this->draw->setFontSize($fontSize);

            $metrics = $imagick->queryFontMetrics($this->draw, $this->text);

            $fontSize--;
        } while (($metrics['textHeight'] > $height || $metrics['textWidth'] > $width) &&
        $fontSize > $this->minFontSize);

        $this->draw->annotation($x, $y, $this->text);

        $imagick->drawImage($this->draw);
    }
}
