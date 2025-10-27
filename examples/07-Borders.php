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
use Kehet\ImagickLayoutEngine\Enums\ImageMode;
use Kehet\ImagickLayoutEngine\Items\Image;
use Kehet\ImagickLayoutEngine\Items\Rectangle;
use Kehet\ImagickLayoutEngine\Items\Text;

require __DIR__.'/../vendor/autoload.php';

function createDraw(string $fill, float $strokeWidth = 1): ImagickDraw
{
    $draw = new \ImagickDraw;
    $draw->setFillColor(new \ImagickPixel($fill));
    $draw->setStrokeColor(new \ImagickPixel($fill));
    $draw->setStrokeWidth($strokeWidth);

    return $draw;
}

$width = 1500;
$height = 1000;

// Create new image
$imagick = new Imagick;
$imagick->newImage($width, $height, new ImagickPixel('white'));

// Create main container
$mainContainer = new ColumnContainer;

// Example 1: Rectangle with border
$example1 = new RowContainer;
$example1->addItem(new Text(createDraw('#000000'), 'Rectangle with colored borders:'), 400);

$rectangle = new Rectangle(createDraw('#4ade80'));
$rectangle->setBorderTop(createDraw('#ff0000', 10));
$rectangle->setBorderRight(createDraw('#00ff00', 10));
$rectangle->setBorderBottom(createDraw('#0000ff', 10));
$rectangle->setBorderLeft(createDraw('#ffff00', 10));
$example1->addItem($rectangle, 1100);

// Example 2: Text with border
$example2 = new RowContainer;
$example2->addItem(new Text(createDraw('#000000'), 'Text with red border:'), 400);

$text = new Text(createDraw('#4ade80'), 'This text has a red border around it.');
$borderDraw = createDraw('#ff0000', 5);
$text->setBorder($borderDraw, $borderDraw, $borderDraw, $borderDraw);
$example2->addItem($text, 1100);

// Example 3: Image with border
$example3 = new RowContainer;
$example3->addItem(new Text(createDraw('#000000'), 'Image with border:'), 400);

$image = new Image(__DIR__.'/example-image-small.jpeg', ImageMode::FIT);
$borderDraw = createDraw('#0000ff', 5);
$image->setBorder($borderDraw, $borderDraw, $borderDraw, $borderDraw);
$example3->addItem($image, 1100);

// Example 4: Container with border
$example4 = new RowContainer;
$example4->addItem(new Text(createDraw('#000000'), 'Container with border:'), 400);

$container = new RowContainer;
$container->addItem(new Rectangle(createDraw('#4ade80')), 300);
$container->addItem(new Rectangle(createDraw('#f87171')), 300);
$container->addItem(new Rectangle(createDraw('#60a5fa')), 300);
$borderDraw = createDraw('#ff00ff', 10);
$container->setBorder($borderDraw, $borderDraw, $borderDraw, $borderDraw);
$example4->addItem($container, 1100);

// Example 5: Nested containers with borders
$example5 = new RowContainer;
$example5->addItem(new Text(createDraw('#000000'), 'Nested containers with borders:'), 400);

$outerContainer = new RowContainer;
// Create first column
$column1 = new ColumnContainer;
$column1->addItem(new Rectangle(createDraw('#4ade80')), 100);
$column1->addItem(new Rectangle(createDraw('#f87171')), 100);
$column1->setBorderTop(createDraw('#ff0000', 5));
$column1->setBorderRight(createDraw('#00ff00', 5));
$column1->setBorderBottom(createDraw('#0000ff', 5));
$column1->setBorderLeft(createDraw('#ffff00', 5));

// Create second column
$column2 = new ColumnContainer;
$column2->addItem(new Rectangle(createDraw('#60a5fa')), 100);
$column2->addItem(new Rectangle(createDraw('#c084fc')), 100);
$borderDraw = createDraw('#ff00ff', 5);
$column2->setBorder($borderDraw, $borderDraw, $borderDraw, $borderDraw);

// Add columns to outer container
$outerContainer->addItem($column1, 500);
$outerContainer->addItem($column2, 500);

// Set border on outer container
$outerBorder = createDraw('#000000', 15);
$outerContainer->setBorder($outerBorder, $outerBorder, $outerBorder, $outerBorder);

$example5->addItem($outerContainer, 1100);

// Add all examples to main container
$mainContainer->addItem($example1, 200);
$mainContainer->addItem($example2, 200);
$mainContainer->addItem($example3, 200);
$mainContainer->addItem($example4, 200);
$mainContainer->addItem($example5, 200);

// Draw container onto image
$mainContainer->draw($imagick, 0, 0, $width, $height);

// Output image as png to file
$imagick->setImageFormat('png');
$imagick->writeImage(__DIR__.'/07.png');
