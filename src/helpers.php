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

if (! function_exists('draw')) {
    function draw(?string $fill = null, ?string $stroke = null, int $strokeWidth = 1): \ImagickDraw
    {
        $draw = new \ImagickDraw;

        if ($fill !== null) {
            $draw->setFillColor(new \ImagickPixel($fill));
        }

        if ($stroke !== null) {
            $draw->setStrokeColor(new \ImagickPixel($stroke));
            $draw->setStrokeWidth($strokeWidth);
        }

        return $draw;
    }
}
