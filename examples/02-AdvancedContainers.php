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

// Add subcontainers into root container

$row = new RowContainer;
$row->addItem(new Rectangle(fill('#fee2e2')), 50);
$row->addItem(new Rectangle(fill('#fca5a5')), 100);
$row->addItem(new Rectangle(fill('#dc2626')), 150);
$row->addItem(new Rectangle(fill('#450a0a')));
$frame->addItem($row);

$row = new RowContainer;
$row->addItem(new Rectangle(fill('#ecfccb')));
$row->addItem(new Rectangle(fill('#bef264')), 50);
$row->addItem(new Rectangle(fill('#65a30d')), 100);
$row->addItem(new Rectangle(fill('#1a2e05')), 150);
$frame->addItem($row);

$row = new RowContainer;
$row->addItem(new Rectangle(fill('#cffafe')));
$row->addItem(new Rectangle(fill('#67e8f9')));
$row->addItem(new Rectangle(fill('#0891b2')));
$row->addItem(new Rectangle(fill('#164e63')));
$frame->addItem($row);

// Draw container onto image

$frame->draw($imagick, 0, 0, $width, $height);

// Output image as png to file

$imagick->setImageFormat('png');
$imagick->writeImage(__DIR__.'/02.png');
