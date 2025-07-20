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
use Kehet\ImagickLayoutEngine\Items\Image;
use Kehet\ImagickLayoutEngine\Items\Text;

require __DIR__ . '/../vendor/autoload.php';

function fill(string $fill): ImagickDraw
{
    $return = new \ImagickDraw();
    $return->setFillColor(new \ImagickPixel($fill));
    return $return;
}

$width = 1500;
$height = 1000;

$smallImage = __DIR__ . '/example-image-small.jpeg';

// Create new image
$imagick = new Imagick();
$imagick->newImage($width, $height, new ImagickPixel('white'));

// Create a 3x3 grid to demonstrate all gravity options
$mainContainer = new ColumnContainer();

$rowFit = new RowContainer();
$rowFit->addItem(createGravityDemoContainer($smallImage, Image::GRAVITY_LEFT, 'LEFT (fit)'));
$rowFit->addItem(createGravityDemoContainer($smallImage, Image::GRAVITY_CENTER, 'CENTER (fit)'));
$rowFit->addItem(createGravityDemoContainer($smallImage, Image::GRAVITY_RIGHT, 'RIGHT (fit)'));
$mainContainer->addItem($rowFit);

$rowFill = new RowContainer();
$rowFill->addItem(createGravityDemoContainer($smallImage, Image::GRAVITY_TOP, 'TOP (fill)', true));
$rowFill->addItem(createGravityDemoContainer($smallImage, Image::GRAVITY_CENTER, 'CENTER (fill)', true));
$rowFill->addItem(createGravityDemoContainer($smallImage, Image::GRAVITY_BOTTOM, 'BOTTOM (fill)', true));
$mainContainer->addItem($rowFill);

// Draw container onto image
$mainContainer->draw($imagick, 0, 0, $width, $height);

// Output image as png to file
$imagick->setImageFormat('png');
$imagick->writeImage(__DIR__ . '/06.png');

function createGravityDemoContainer(string $imagePath, string $gravity, string $label, bool $fill = false): ColumnContainer
{
    $container = new ColumnContainer();
    $container->addItem(new Image($imagePath, $fill, $gravity));
    $container->addItem(new Text(fill('black'), $label));

    return $container;
}
