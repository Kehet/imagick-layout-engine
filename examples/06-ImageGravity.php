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

require __DIR__.'/../vendor/autoload.php';

function fill(string $fill): ImagickDraw
{
    $return = new \ImagickDraw;
    $return->setFillColor(new \ImagickPixel($fill));

    return $return;
}

$width = 1500;
$height = 1000;

$smallImage = __DIR__.'/example-image-small.jpeg';
$largeImage = __DIR__.'/example-image-large.jpeg';

// Create new image
$imagick = new Imagick;
$imagick->newImage($width, $height, new ImagickPixel('white'));

// Create a 3x3 grid to demonstrate all gravity options
$mainContainer = new ColumnContainer;

$rowFit = new RowContainer;
$rowFit->addItem(createGravityDemoContainer($largeImage, Gravity::TOP, 'TOP (none)', ImageMode::NONE));
$rowFit->addItem(createGravityDemoContainer($largeImage, Gravity::CENTER, 'CENTER (none)', ImageMode::NONE));
$rowFit->addItem(createGravityDemoContainer($largeImage, Gravity::BOTTOM, 'BOTTOM (none)', ImageMode::NONE));
$mainContainer->addItem($rowFit);

$rowFit = new RowContainer;
$rowFit->addItem(createGravityDemoContainer($smallImage, Gravity::LEFT, 'LEFT (fit)', ImageMode::FIT));
$rowFit->addItem(createGravityDemoContainer($smallImage, Gravity::CENTER, 'CENTER (fit)', ImageMode::FIT));
$rowFit->addItem(createGravityDemoContainer($smallImage, Gravity::RIGHT, 'RIGHT (fit)', ImageMode::FIT));
$mainContainer->addItem($rowFit);

$rowFill = new RowContainer;
$rowFill->addItem(createGravityDemoContainer($largeImage, Gravity::TOP, 'TOP (fill)', ImageMode::FILL));
$rowFill->addItem(createGravityDemoContainer($largeImage, Gravity::CENTER, 'CENTER (fill)', ImageMode::FILL));
$rowFill->addItem(createGravityDemoContainer($largeImage, Gravity::BOTTOM, 'BOTTOM (fill)', ImageMode::FILL));
$mainContainer->addItem($rowFill);

// Draw container onto image
$mainContainer->draw($imagick, 0, 0, $width, $height);

// Output image as png to file
$imagick->setImageFormat('png');
$imagick->writeImage(__DIR__.'/06.png');

function createGravityDemoContainer(string $imagePath, Gravity $gravity, string $label, ImageMode $mode): ColumnContainer
{
    $container = new ColumnContainer;
    $container->addItem(new Image($imagePath, $mode, $gravity));
    $container->addItem(new Text(fill('black'), $label));

    return $container;
}
