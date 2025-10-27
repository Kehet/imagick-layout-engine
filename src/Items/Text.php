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
use Kehet\ImagickLayoutEngine\Enums\Gravity;
use Kehet\ImagickLayoutEngine\Traits\BorderTrait;
use Kehet\ImagickLayoutEngine\Traits\MarginTrait;
use Kehet\ImagickLayoutEngine\Traits\PaddingTrait;

/**
 * Represents a simple text that can be drawn onto container grid. Scales font size down as needed.
 */
class Text implements DrawableInterface
{
    use BorderTrait;
    use MarginTrait;
    use PaddingTrait;

    public function __construct(
        protected ImagickDraw $draw,
        protected string $text,
        protected int $initialFontSize = 60,
        protected int $minFontSize = 10,
        protected Gravity $gravity = Gravity::TOP_LEFT,
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

        $this->draw->setGravity(Imagick::GRAVITY_NORTHWEST);

        $textMetrics = $this->calculateOptimalFontSize($imagick, $width, $height);

        $tx = $this->calculateHorizontalPosition($x, $width, $textMetrics['textWidth']);
        $ty = $this->calculateVerticalPosition($y, $height, $textMetrics['textHeight']);

        $this->draw->annotation($tx, $ty, $this->text);
        $imagick->drawImage($this->draw);

        $this->drawBorders($imagick, $borderX, $borderY, $borderWidth, $borderHeight);
    }

    private function calculateOptimalFontSize(Imagick $imagick, int $width, int $height): array
    {
        $fontSize = $this->initialFontSize;

        do {
            $this->draw->setFontSize($fontSize);
            $metrics = $imagick->queryFontMetrics($this->draw, $this->text);
            $fontSize--;
        } while (($metrics['textHeight'] > $height || $metrics['textWidth'] > $width) &&
        $fontSize > $this->minFontSize);

        return $metrics;
    }

    private function calculateHorizontalPosition(int $x, int $width, int $textWidth): int
    {
        return match ($this->gravity) {
            Gravity::TOP_LEFT, Gravity::LEFT, Gravity::BOTTOM_LEFT => $x,
            Gravity::TOP, Gravity::CENTER, Gravity::BOTTOM => $x + ($width - $textWidth) / 2,
            Gravity::TOP_RIGHT, Gravity::RIGHT, Gravity::BOTTOM_RIGHT => $x + ($width - $textWidth),
        };
    }

    private function calculateVerticalPosition(int $y, int $height, int $totalHeight): int
    {
        return match ($this->gravity) {
            Gravity::TOP_LEFT, Gravity::TOP, Gravity::TOP_RIGHT => $y,
            Gravity::LEFT, Gravity::CENTER, Gravity::RIGHT => $y + ($height - $totalHeight) / 2,
            Gravity::BOTTOM_LEFT, Gravity::BOTTOM, Gravity::BOTTOM_RIGHT => $y + ($height - $totalHeight),
        };
    }
}
