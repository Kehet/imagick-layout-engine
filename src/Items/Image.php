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

/**
 * Represents an image that can be drawn onto container grid.
 */
class Image implements DrawableInterface
{

    public const string GRAVITY_TOP_LEFT = 'top-left';
    public const string GRAVITY_TOP = 'top';
    public const string GRAVITY_TOP_RIGHT = 'top-right';
    public const string GRAVITY_LEFT = 'left';
    public const string GRAVITY_CENTER = 'center';
    public const string GRAVITY_RIGHT = 'right';
    public const string GRAVITY_BOTTOM_LEFT = 'bottom-left';
    public const string GRAVITY_BOTTOM = 'bottom';
    public const string GRAVITY_BOTTOM_RIGHT = 'bottom-right';

    public function __construct(
        protected string $file,
        protected bool $fill = false,
        protected string $gravity = self::GRAVITY_CENTER,
    ) {
    }

    public function draw(Imagick $imagick, int $x, int $y, int $width, int $height): void
    {
        $image = new Imagick($this->file);

        $originalWidth = $image->getImageWidth();
        $originalHeight = $image->getImageHeight();

        $originalRatio = $originalWidth / $originalHeight;
        $targetRatio = $width / $height;

        if ($this->fill) {
            if ($originalRatio > $targetRatio) {
                // Image is wider than target area
                $newWidth = $originalWidth * ($height / $originalHeight);
                $newHeight = $height;
                $image->resizeImage($newWidth, $newHeight, Imagick::FILTER_LANCZOS, 1);

                // Calculate crop X position based on gravity
                $cropX = 0;
                if (str_contains($this->gravity, 'right')) {
                    $cropX = $newWidth - $width;
                } elseif (str_contains($this->gravity, 'left')) {
                    $cropX = 0;
                } else {
                    // Center horizontally for top, center, bottom
                    $cropX = ($newWidth - $width) / 2;
                }

                $image->cropImage($width, $height, $cropX, 0);
            } else {
                // Image is taller than target area
                $newWidth = $width;
                $newHeight = $originalHeight * ($width / $originalWidth);
                $image->resizeImage($newWidth, $newHeight, Imagick::FILTER_LANCZOS, 1);

                // Calculate crop Y position based on gravity
                $cropY = 0;
                if (str_contains($this->gravity, 'bottom')) {
                    $cropY = $newHeight - $height;
                } elseif (str_contains($this->gravity, 'top')) {
                    $cropY = 0;
                } else {
                    // Center vertically for left, center, right
                    $cropY = ($newHeight - $height) / 2;
                }

                $image->cropImage($width, $height, 0, $cropY);
            }
        } else {
            // fit

            if ($originalRatio > $targetRatio) {
                // Image is wider than target area
                $newWidth = $width;
                $newHeight = $width / $originalRatio;
            } else {
                // Image is taller than target area
                $newWidth = $height * $originalRatio;
                $newHeight = $height;
            }

            $image->resizeImage($newWidth, $newHeight, Imagick::FILTER_LANCZOS, 1);
        }

        $posX = $x;
        $posY = $y;

        if (!$this->fill) {
            $imageWidth = $image->getImageWidth();
            $imageHeight = $image->getImageHeight();

            // Calculate position based on gravity
            switch ($this->gravity) {
                case self::GRAVITY_TOP_LEFT:
                    $posX = $x;
                    $posY = $y;
                    break;

                case self::GRAVITY_TOP:
                    $posX = $x + ($width - $imageWidth) / 2;
                    $posY = $y;
                    break;

                case self::GRAVITY_TOP_RIGHT:
                    $posX = $x + ($width - $imageWidth);
                    $posY = $y;
                    break;

                case self::GRAVITY_LEFT:
                    $posX = $x;
                    $posY = $y + ($height - $imageHeight) / 2;
                    break;

                case self::GRAVITY_CENTER:
                    $posX = $x + ($width - $imageWidth) / 2;
                    $posY = $y + ($height - $imageHeight) / 2;
                    break;

                case self::GRAVITY_RIGHT:
                    $posX = $x + ($width - $imageWidth);
                    $posY = $y + ($height - $imageHeight) / 2;
                    break;

                case self::GRAVITY_BOTTOM_LEFT:
                    $posX = $x;
                    $posY = $y + ($height - $imageHeight);
                    break;

                case self::GRAVITY_BOTTOM:
                    $posX = $x + ($width - $imageWidth) / 2;
                    $posY = $y + ($height - $imageHeight);
                    break;

                case self::GRAVITY_BOTTOM_RIGHT:
                    $posX = $x + ($width - $imageWidth);
                    $posY = $y + ($height - $imageHeight);
                    break;

                default:
                    // Default to center if an invalid gravity is specified
                    $posX = $x + ($width - $imageWidth) / 2;
                    $posY = $y + ($height - $imageHeight) / 2;
                    break;
            }
        }

        $imagick->compositeImage($image, Imagick::COMPOSITE_DEFAULT, $posX, $posY);
    }
}
