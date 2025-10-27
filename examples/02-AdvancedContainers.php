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
use Kehet\ImagickLayoutEngine\Containers\RowContainer;
use Kehet\ImagickLayoutEngine\Items\Rectangle;

require __DIR__.'/../vendor/autoload.php';

function fill(string $fill): ImagickDraw
{
    $return = new \ImagickDraw;
    $return->setFillColor(new \ImagickPixel($fill));

    return $return;
}

$width = 1500;
$height = 1000;

// Create new image

$imagick = new Imagick;
$imagick->newImage($width, $height, new ImagickPixel('white'));

// Create root container

$frame = new ColumnContainer;
$frame->setPadding(30); // container-level padding on the whole frame

// Add subcontainers into root container

// Row 1: container padding and items with individual padding; mixed forced sizes
$row = new RowContainer;
$row->setPadding(20); // equal padding on all sides
$row->addItem((new Rectangle(fill('#fee2e2')))->setPadding(10), 50);
$row->addItem((new Rectangle(fill('#fca5a5')))->setPadding(10, 20), 100);
$row->addItem((new Rectangle(fill('#dc2626')))->setPadding(10, 15, 20), 150);
$row->addItem((new Rectangle(fill('#450a0a')))->setPadding(5, 10, 15, 20));
$frame->addItem($row);

// Row 2: asymmetric container padding (vertical | horizontal)
$row = new RowContainer;
$row->setPadding(10, 40); // top/bottom = 10, left/right = 40
$row->addItem((new Rectangle(fill('#ecfccb')))->setPadding(10));
$row->addItem((new Rectangle(fill('#bef264')))->setPadding(5, 15), 50);
$row->addItem((new Rectangle(fill('#65a30d')))->setPadding(5, 10, 15), 100);
$row->addItem((new Rectangle(fill('#1a2e05')))->setPadding(20, 10, 5, 0), 150);
$frame->addItem($row);

// Row 3: four-value container padding
$row = new RowContainer;
$row->setPadding(5, 10, 15, 20); // top, right, bottom, left
$row->addItem((new Rectangle(fill('#cffafe')))->setPadding(8));
$row->addItem((new Rectangle(fill('#67e8f9')))->setPadding(6, 12));
$row->addItem((new Rectangle(fill('#0891b2')))->setPadding(4, 8, 12));
$row->addItem((new Rectangle(fill('#164e63')))->setPadding(2, 4, 6, 8));
$frame->addItem($row);

// Draw container onto image

$frame->draw($imagick, 0, 0, $width, $height);

// Output image as png to file

$imagick->setImageFormat('png');
$imagick->writeImage(__DIR__.'/02.png');
