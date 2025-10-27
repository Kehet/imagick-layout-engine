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
use Kehet\ImagickLayoutEngine\Enums\Gravity;
use Kehet\ImagickLayoutEngine\Enums\ImageMode;
use Kehet\ImagickLayoutEngine\Traits\BorderTrait;
use Kehet\ImagickLayoutEngine\Traits\MarginTrait;
use Kehet\ImagickLayoutEngine\Traits\PaddingTrait;

/**
 * Represents an image that can be drawn onto container grid.
 */
class Image implements DrawableInterface
{
    use BorderTrait;
    use MarginTrait;
    use PaddingTrait;

    public function __construct(
        protected string $file,
        protected ImageMode $mode = ImageMode::NONE,
        protected Gravity $gravity = Gravity::CENTER,
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

        $image = new Imagick($this->file);

        $originalWidth = $image->getImageWidth();
        $originalHeight = $image->getImageHeight();

        $originalRatio = $originalWidth / $originalHeight;
        $targetRatio = $width / $height;

        switch ($this->mode) {
            case ImageMode::FILL:
                if ($originalRatio > $targetRatio) {
                    // Image is wider than target area
                    $newWidth = $originalWidth * ($height / $originalHeight);
                    $newHeight = $height;
                    $image->resizeImage($newWidth, $newHeight, Imagick::FILTER_LANCZOS, 1);

                    // Calculate crop X position based on gravity
                    if (str_contains($this->gravity->value, 'right')) {
                        $cropX = $newWidth - $width;
                    } elseif (str_contains($this->gravity->value, 'left')) {
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
                    if (str_contains($this->gravity->value, 'bottom')) {
                        $cropY = $newHeight - $height;
                    } elseif (str_contains($this->gravity->value, 'top')) {
                        $cropY = 0;
                    } else {
                        // Center vertically for left, center, right
                        $cropY = ($newHeight - $height) / 2;
                    }

                    $image->cropImage($width, $height, 0, $cropY);
                }

                break;
            case ImageMode::FIT:
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

                break;
            case ImageMode::NONE:
                // If image is larger than container, crop it to fit
                if ($originalWidth > $width || $originalHeight > $height) {
                    // Determine crop dimensions
                    $cropWidth = min($originalWidth, $width);
                    $cropHeight = min($originalHeight, $height);

                    // Calculate crop position based on gravity
                    $cropX = 0;
                    $cropY = 0;

                    if ($originalWidth > $width) {
                        if (str_contains($this->gravity->value, 'right')) {
                            $cropX = $originalWidth - $width;
                        } elseif (! str_contains($this->gravity->value, 'left')) {
                            // Center horizontally for top, center, bottom
                            $cropX = ($originalWidth - $width) / 2;
                        }
                    }

                    if ($originalHeight > $height) {
                        if (str_contains($this->gravity->value, 'bottom')) {
                            $cropY = $originalHeight - $height;
                        } elseif (! str_contains($this->gravity->value, 'top')) {
                            // Center vertically for left, center, right
                            $cropY = ($originalHeight - $height) / 2;
                        }
                    }

                    $image->cropImage($cropWidth, $cropHeight, $cropX, $cropY);
                }
                break;
        }

        $posX = $x;
        $posY = $y;

        if ($this->mode === ImageMode::NONE || $this->mode === ImageMode::FIT) {
            $imageWidth = $image->getImageWidth();
            $imageHeight = $image->getImageHeight();

            // Calculate position based on gravity
            switch ($this->gravity) {
                case Gravity::TOP_LEFT:
                    $posX = $x;
                    $posY = $y;
                    break;

                case Gravity::TOP:
                    $posX = $x + ($width - $imageWidth) / 2;
                    $posY = $y;
                    break;

                case Gravity::TOP_RIGHT:
                    $posX = $x + ($width - $imageWidth);
                    $posY = $y;
                    break;

                case Gravity::LEFT:
                    $posX = $x;
                    $posY = $y + ($height - $imageHeight) / 2;
                    break;

                case Gravity::CENTER:
                    $posX = $x + ($width - $imageWidth) / 2;
                    $posY = $y + ($height - $imageHeight) / 2;
                    break;

                case Gravity::RIGHT:
                    $posX = $x + ($width - $imageWidth);
                    $posY = $y + ($height - $imageHeight) / 2;
                    break;

                case Gravity::BOTTOM_LEFT:
                    $posX = $x;
                    $posY = $y + ($height - $imageHeight);
                    break;

                case Gravity::BOTTOM:
                    $posX = $x + ($width - $imageWidth) / 2;
                    $posY = $y + ($height - $imageHeight);
                    break;

                case Gravity::BOTTOM_RIGHT:
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

        $this->drawBorders($imagick, $borderX, $borderY, $borderWidth, $borderHeight);
    }
}
