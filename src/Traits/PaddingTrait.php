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

trait PaddingTrait
{
    public ?int $paddingTop = null;

    public ?int $paddingRight = null;

    public ?int $paddingBottom = null;

    public ?int $paddingLeft = null;

    protected function getBoundingBoxInsidePadding(int $x, int $y, int $width, int $height): array
    {
        $newX = $x + $this->paddingLeft;
        $newY = $y + $this->paddingTop;
        $newWidth = $width - $this->paddingLeft - $this->paddingRight;
        $newHeight = $height - $this->paddingTop - $this->paddingBottom;

        // Prevent negative sizes
        if ($newWidth < 0) {
            $newWidth = 0;
        }
        if ($newHeight < 0) {
            $newHeight = 0;
        }

        return [$newX, $newY, $newWidth, $newHeight];
    }

    public function setPadding(?int $arg1 = null, ?int $arg2 = null, ?int $arg3 = null, ?int $arg4 = null): self
    {
        if ($arg4 !== null) {
            $this->paddingTop = $arg1;
            $this->paddingRight = $arg2;
            $this->paddingBottom = $arg3;
            $this->paddingLeft = $arg4;
        } elseif ($arg3 !== null) {
            $this->paddingTop = $arg1;
            $this->paddingRight = $arg2;
            $this->paddingBottom = $arg3;
            $this->paddingLeft = $arg1;
        } elseif ($arg2 !== null) {
            $this->paddingTop = $arg1;
            $this->paddingRight = $arg2;
            $this->paddingBottom = $arg1;
            $this->paddingLeft = $arg2;
        } else {
            $this->paddingTop = $arg1;
            $this->paddingRight = $arg1;
            $this->paddingBottom = $arg1;
            $this->paddingLeft = $arg1;
        }

        return $this;
    }

    public function setPaddingTop(?int $draw = null): self
    {
        $this->paddingTop = $draw;

        return $this;
    }

    public function setPaddingRight(?int $draw = null): self
    {
        $this->paddingRight = $draw;

        return $this;
    }

    public function setPaddingBottom(?int $draw = null): self
    {
        $this->paddingBottom = $draw;

        return $this;
    }

    public function setPaddingLeft(?int $draw = null): self
    {
        $this->paddingLeft = $draw;

        return $this;
    }
}
