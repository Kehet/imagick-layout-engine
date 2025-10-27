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

namespace Kehet\ImagickLayoutEngine\Traits;

use Imagick;
use ImagickDraw;

trait BorderTrait
{
    public ?ImagickDraw $borderTop = null;

    public ?ImagickDraw $borderRight = null;

    public ?ImagickDraw $borderBottom = null;

    public ?ImagickDraw $borderLeft = null;

    protected function getBorderInsets(): array
    {
        $halfTop = $this->borderTop?->getStrokeWidth() ? $this->borderTop->getStrokeWidth() : 0;
        $halfRight = $this->borderRight?->getStrokeWidth() ? $this->borderRight->getStrokeWidth() : 0;
        $halfBottom = $this->borderBottom?->getStrokeWidth() ? $this->borderBottom->getStrokeWidth() : 0;
        $halfLeft = $this->borderLeft?->getStrokeWidth() ? $this->borderLeft->getStrokeWidth() : 0;

        return [$halfTop, $halfRight, $halfBottom, $halfLeft];
    }

    protected function getBoundingBoxInsideBorder(int $x, int $y, int $width, int $height): array
    {
        [$halfTop, $halfRight, $halfBottom, $halfLeft] = $this->getBorderInsets();

        $newX = $x + $halfLeft;
        $newY = $y + $halfTop;
        $newWidth = $width - $halfLeft - $halfRight;
        $newHeight = $height - $halfTop - $halfBottom;

        // Prevent negative sizes
        if ($newWidth < 0) {
            $newWidth = 0;
        }
        if ($newHeight < 0) {
            $newHeight = 0;
        }

        return [$newX, $newY, $newWidth, $newHeight];
    }

    public function setBorder(?ImagickDraw $arg1 = null, ?ImagickDraw $arg2 = null, ?ImagickDraw $arg3 = null, ?ImagickDraw $arg4 = null): void
    {
        if ($arg4 !== null) {
            $this->borderTop = $arg1;
            $this->borderRight = $arg2;
            $this->borderBottom = $arg3;
            $this->borderLeft = $arg4;
        } elseif ($arg3 !== null) {
            $this->borderTop = $arg1;
            $this->borderRight = $arg2;
            $this->borderBottom = $arg3;
            $this->borderLeft = $arg1;
        } elseif ($arg2 !== null) {
            $this->borderTop = $arg1;
            $this->borderRight = $arg2;
            $this->borderBottom = $arg1;
            $this->borderLeft = $arg2;
        } else {
            $this->borderTop = $arg1;
            $this->borderRight = $arg1;
            $this->borderBottom = $arg1;
            $this->borderLeft = $arg1;
        }
    }

    public function setBorderTop(ImagickDraw $draw): void
    {
        $this->borderTop = $draw;
    }

    public function setBorderRight(ImagickDraw $draw): void
    {
        $this->borderRight = $draw;
    }

    public function setBorderBottom(ImagickDraw $draw): void
    {
        $this->borderBottom = $draw;
    }

    public function setBorderLeft(ImagickDraw $draw): void
    {
        $this->borderLeft = $draw;
    }

    protected function drawBorders(Imagick $imagick, int $x, int $y, int $width, int $height): void
    {
        [$top, $right, $bottom, $left] = $this->getBorderInsets();

        $halfTop = $top / 2;
        $halfRight = $right / 2;
        $halfBottom = $bottom / 2;
        $halfLeft = $left / 2;

        if ($this->borderTop !== null) {
            $this->borderTop->line($x, $y + $halfTop, $x + $width, $y + $halfTop);
            $imagick->drawImage($this->borderTop);
        }

        if ($this->borderRight !== null) {
            $this->borderRight->line($x + $width - $halfRight, $y, $x + $width - $halfRight, $y + $height);
            $imagick->drawImage($this->borderRight);
        }

        if ($this->borderBottom !== null) {
            $this->borderBottom->line($x, $y + $height - $halfBottom, $x + $width, $y + $height - $halfBottom);
            $imagick->drawImage($this->borderBottom);
        }

        if ($this->borderLeft !== null) {
            $this->borderLeft->line($x + $halfLeft, $y, $x + $halfLeft, $y + $height);
            $imagick->drawImage($this->borderLeft);
        }
    }
}
