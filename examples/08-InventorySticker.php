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
use Kehet\ImagickLayoutEngine\Enums\Gravity;
use Kehet\ImagickLayoutEngine\Enums\ImageMode;
use Kehet\ImagickLayoutEngine\Items\Image;
use Kehet\ImagickLayoutEngine\Items\Text;
use Kehet\ImagickLayoutEngine\Items\TextWrap;

require __DIR__.'/../vendor/autoload.php';

function fill(string $fill = 'black'): ImagickDraw
{
    $return = new \ImagickDraw;
    $return->setFillColor(new \ImagickPixel($fill));

    return $return;
}

function stroke(string $stroke = 'black', int $width = 10): ImagickDraw
{
    $return = new \ImagickDraw;
    $return->setStrokeColor(new \ImagickPixel($stroke));
    $return->setStrokeWidth($width);

    return $return;
}

$width = 1000;
$height = 1500;

// Create new image
$imagick = new Imagick;
$imagick->newImage($width, $height, new ImagickPixel('white'));

$frame = new ColumnContainer;

// row 1
$title = new Text(fill(), 'Acme Corporation');
$title->setBorder(stroke());
$frame->addItem($title);

// row 2
$row2 = new RowContainer;

$cell1 = new ColumnContainer;
$cell1->setBorder(stroke());
$cell1->addItem(new Text(fill(), 'Item No.', gravity: Gravity::CENTER));
$cell1->addItem(new Text(fill(), 'SD6358', gravity: Gravity::CENTER));
$row2->addItem($cell1);

$cell2 = new ColumnContainer;
$cell2->setBorder(stroke());
$cell2->addItem(new Text(fill(), 'Stock No.', gravity: Gravity::CENTER));
$cell2->addItem(new Text(fill(), 'T75489E3', gravity: Gravity::CENTER));
$row2->addItem($cell2);

$frame->addItem($row2);

// row 3
$row3 = new RowContainer;
$row3->setBorder(stroke());
$row3->addItem(new Text(fill(), 'From', gravity: Gravity::CENTER), forceSize: ceil($width / 4));
$row3->addItem(new TextWrap(fill(), 'ABC Company Mannerheimintie 13 00100 Helsinki FINLAND', gravity: Gravity::LEFT));

$frame->addItem($row3);

// row 4
$row4 = new RowContainer;
$row4->setBorder(stroke());
$row4->addItem(new Text(fill(), 'To', gravity: Gravity::CENTER), forceSize: ceil($width / 4));
$row4->addItem(new TextWrap(fill(), 'XYZ Company Mannerheimintie 13 00100 Helsinki FINLAND', gravity: Gravity::LEFT));

$frame->addItem($row4);

// row 5
$row5 = new RowContainer;

$cell1 = new ColumnContainer;
$cell1->setBorder(stroke());
$cell1->addItem(new Text(fill(), 'Ref Code', gravity: Gravity::CENTER));
$cell1->addItem(new Text(fill(), '5544060', gravity: Gravity::CENTER));
$row5->addItem($cell1);

$cell2 = new ColumnContainer;
$cell2->setBorder(stroke());
$cell2->addItem(new Text(fill(), 'Batch No.', gravity: Gravity::CENTER));
$cell2->addItem(new Text(fill(), 'TH/23015D', gravity: Gravity::CENTER));
$row5->addItem($cell2);

$frame->addItem($row5);

// row 6
$barcode = new Image(__DIR__.'/example-code-128.png', ImageMode::FIT);
$barcode->setBorder(stroke());
$frame->addItem($barcode, 300, 25);

// Draw container onto image
$frame->draw($imagick, 0, 0, $width, $height);

// Output image as png to file
$imagick->setImageFormat('png');
$imagick->writeImage(__DIR__.'/08.png');
