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

use Kehet\ImagickLayoutEngine\Containers\ColumnContainer;
use Kehet\ImagickLayoutEngine\Enums\ImageMode;
use Kehet\ImagickLayoutEngine\Items\Image;

require __DIR__.'/../vendor/autoload.php';

function fill(string $fill): ImagickDraw
{
    $return = new \ImagickDraw;
    $return->setFillColor(new \ImagickPixel($fill));

    return $return;
}

$width = 1500;
$height = 1000;

$largeImage = __DIR__ . '/example-image-large.jpeg';
$smallImage = __DIR__ . '/example-image-small.jpeg';

// Create new image

$imagick = new Imagick;
$imagick->newImage($width, $height, new ImagickPixel('white'));

// TextWrap shrinks and wraps text as needed

$frame = new ColumnContainer;

// Default behavior doesn't scale image
$frame->addItem(new Image($largeImage));

// Large image will be cropped to fit
$frame->addItem(new Image($largeImage, ImageMode::FIT));

// Small image will be stretched
$frame->addItem(new Image($smallImage, ImageMode::FIT));

// Draw container onto image

$frame->draw($imagick, 0, 0, $width, $height);

// Output image as png to file

$imagick->setImageFormat('png');
$imagick->writeImage(__DIR__.'/05.png');
