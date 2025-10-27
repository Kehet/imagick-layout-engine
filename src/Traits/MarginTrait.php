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

trait MarginTrait
{

    public ?int $marginTop = null;
    public ?int $marginRight = null;
    public ?int $marginBottom = null;
    public ?int $marginLeft = null;

    protected function getBoundingBoxInsideMargin(int $x, int $y, int $width, int $height): array
    {
        $newX = $x + $this->marginLeft;
        $newY = $y + $this->marginTop;
        $newWidth = $width - $this->marginLeft - $this->marginRight;
        $newHeight = $height - $this->marginTop - $this->marginBottom;

        // Prevent negative sizes
        if ($newWidth < 0) { $newWidth = 0; }
        if ($newHeight < 0) { $newHeight = 0; }

        return [$newX, $newY, $newWidth, $newHeight];
    }

    public function setMargin(?int $arg1 = null, ?int $arg2 = null, ?int $arg3 = null, ?int $arg4 = null): self
    {
        if($arg4 !== null) {
            $this->marginTop = $arg1;
            $this->marginRight = $arg2;
            $this->marginBottom = $arg3;
            $this->marginLeft = $arg4;
        } else if($arg3 !== null) {
            $this->marginTop = $arg1;
            $this->marginRight = $arg2;
            $this->marginBottom = $arg3;
            $this->marginLeft = $arg1;
        } else if($arg2 !== null) {
            $this->marginTop = $arg1;
            $this->marginRight = $arg2;
            $this->marginBottom = $arg1;
            $this->marginLeft = $arg2;
        } else {
            $this->marginTop = $arg1;
            $this->marginRight = $arg1;
            $this->marginBottom = $arg1;
            $this->marginLeft = $arg1;
        }

        return $this;
    }

    public function setMarginTop(?int $draw = null): self
    {
        $this->marginTop = $draw;
        return $this;
    }

    public function setMarginRight(?int $draw = null): self
    {
        $this->marginRight = $draw;
        return $this;
    }

    public function setMarginBottom(?int $draw = null): self
    {
        $this->marginBottom = $draw;
        return $this;
    }

    public function setMarginLeft(?int $draw = null): self
    {
        $this->marginLeft = $draw;
        return $this;
    }

}
