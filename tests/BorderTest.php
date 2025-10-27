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

namespace Kehet\ImagickLayoutEngine\Tests;

use Kehet\ImagickLayoutEngine\Containers\ColumnContainer;
use Kehet\ImagickLayoutEngine\Containers\RowContainer;
use Kehet\ImagickLayoutEngine\Enums\ImageMode;
use Kehet\ImagickLayoutEngine\Items\Image;
use Kehet\ImagickLayoutEngine\Items\Rectangle;
use Kehet\ImagickLayoutEngine\Items\Text;
use Kehet\ImagickLayoutEngine\Items\TextWrap;

class BorderTest extends TestCase
{
    public function test_rectangle_with_border(): void
    {
        $imagick = $this->createImage();

        $container = new RowContainer;
        $rectangle = new Rectangle($this->draw('#4ade80'));

        // Set borders with different colors
        $rectangle->setBorderTop($this->stroke('#ff0000'));
        $rectangle->setBorderRight($this->stroke('#00ff00'));
        $rectangle->setBorderBottom($this->stroke('#0000ff'));
        $rectangle->setBorderLeft($this->stroke('#ffff00'));

        $container->addItem($rectangle);

        $this->saveImage($imagick, $container, __FUNCTION__.'.png');
    }

    public function test_text_with_border(): void
    {
        $imagick = $this->createImage();

        $container = new RowContainer;
        $text = new Text($this->draw('#4ade80'), self::SHORT_TEXT);

        // Set all borders with the same color
        $borderDraw = $this->stroke('#ff0000');
        $text->setBorder($borderDraw, $borderDraw, $borderDraw, $borderDraw);

        $container->addItem($text);

        $this->saveImage($imagick, $container, __FUNCTION__.'.png');
    }

    public function test_text_wrap_with_border(): void
    {
        $imagick = $this->createImage();

        $container = new RowContainer;
        $textWrap = new TextWrap($this->draw('#4ade80'), self::LONG_TEXT);

        // Set individual borders
        $textWrap->setBorderTop($this->stroke('#ff0000'));
        $textWrap->setBorderRight($this->stroke('#00ff00'));
        $textWrap->setBorderBottom($this->stroke('#0000ff'));
        $textWrap->setBorderLeft($this->stroke('#ffff00'));

        $container->addItem($textWrap);

        $this->saveImage($imagick, $container, __FUNCTION__.'.png');
    }

    public function test_image_with_border(): void
    {
        $imagick = $this->createImage();

        $container = new RowContainer;
        $image = new Image(self::SMALL_IMAGE, ImageMode::FIT);

        // Set all borders with the same color
        $borderDraw = $this->stroke('#ff0000');
        $image->setBorder($borderDraw, $borderDraw, $borderDraw, $borderDraw);

        $container->addItem($image);

        $this->saveImage($imagick, $container, __FUNCTION__.'.png');
    }

    public function test_row_container_with_border(): void
    {
        $imagick = $this->createImage();

        $container = new RowContainer;

        // Add some items to the container
        $container->addItem(new Rectangle($this->draw('#4ade80')), 300);
        $container->addItem(new Rectangle($this->draw('#f87171')), 300);
        $container->addItem(new Rectangle($this->draw('#60a5fa')), 300);

        // Set borders with different colors
        $container->setBorderTop($this->stroke('#ff0000'));
        $container->setBorderRight($this->stroke('#00ff00'));
        $container->setBorderBottom($this->stroke('#0000ff'));
        $container->setBorderLeft($this->stroke('#ffff00'));

        $this->saveImage($imagick, $container, __FUNCTION__.'.png');
    }

    public function test_column_container_with_border(): void
    {
        $imagick = $this->createImage();

        $container = new ColumnContainer;

        // Add some items to the container
        $container->addItem(new Rectangle($this->draw('#4ade80')), 300);
        $container->addItem(new Rectangle($this->draw('#f87171')), 300);
        $container->addItem(new Rectangle($this->draw('#60a5fa')), 300);

        // Set all borders with the same color
        $borderDraw = $this->stroke('#ff0000');
        $container->setBorder($borderDraw, $borderDraw, $borderDraw, $borderDraw);

        $this->saveImage($imagick, $container, __FUNCTION__.'.png');
    }

    public function test_nested_containers_with_border(): void
    {
        $imagick = $this->createImage();

        $outerContainer = new RowContainer;

        // Create first column
        $column1 = new ColumnContainer;
        $column1->addItem(new Rectangle($this->draw('#4ade80')));
        $column1->addItem(new Rectangle($this->draw('#f87171')));
        $column1->setBorderTop($this->stroke('#ff0000'));
        $column1->setBorderRight($this->stroke('#00ff00'));
        $column1->setBorderBottom($this->stroke('#0000ff'));
        $column1->setBorderLeft($this->stroke('#ffff00'));

        // Create second column
        $column2 = new ColumnContainer;
        $column2->addItem(new Rectangle($this->draw('#60a5fa')));
        $column2->addItem(new Rectangle($this->draw('#c084fc')));
        $borderDraw = $this->stroke('#ff00ff');
        $column2->setBorder($borderDraw, $borderDraw, $borderDraw, $borderDraw);

        // Add columns to outer container
        $outerContainer->addItem($column1);
        $outerContainer->addItem($column2);

        // Set border on outer container
        $outerBorder = $this->stroke('#000000');
        $outerContainer->setBorder($outerBorder, $outerBorder, $outerBorder, $outerBorder);

        $this->saveImage($imagick, $outerContainer, __FUNCTION__.'.png');
    }

    public function test_container_with_border_and_padding(): void
    {
        $imagick = $this->createImage();

        // Create a row container with a border
        $container = new RowContainer;
        $borderDraw = $this->stroke('#ff0000');
        $container->setBorder($borderDraw, $borderDraw, $borderDraw, $borderDraw);

        // Add items with different padding values
        // First item: 50px padding on all sides
        $container->addItem((new Rectangle($this->draw('#4ade80')))->setPadding(50), 300);

        // Second item: different padding on each side (top, right, bottom, left)
        $container->addItem((new Rectangle($this->draw('#f87171')))->setPadding(20, 40, 60, 30), 300);

        // Third item: padding on top and bottom only
        $container->addItem((new Rectangle($this->draw('#60a5fa')))->setPadding(30, 0, 30, 0), 300);

        $this->saveImage($imagick, $container, __FUNCTION__.'.png');
    }

    public function test_container_with_border_and_margin(): void
    {
        $imagick = $this->createImage();

        // Create a row container with a border
        $container = new RowContainer;
        $borderDraw = $this->stroke('#ff0000');
        $container->setBorder($borderDraw, $borderDraw, $borderDraw, $borderDraw);

        // Add items with different margin values
        // First item: 50px margin on all sides
        $container->addItem((new Rectangle($this->draw('#4ade80')))->setMargin(50), 300);

        // Second item: different margin on each side (top, right, bottom, left)
        $container->addItem((new Rectangle($this->draw('#f87171')))->setMargin(20, 40, 60, 30), 300);

        // Third item: margin on top and bottom only
        $container->addItem((new Rectangle($this->draw('#60a5fa')))->setMargin(30, 0, 30, 0), 300);

        $this->saveImage($imagick, $container, __FUNCTION__.'.png');
    }

    public function test_container_with_simple_border_margin(): void
    {
        $imagick = $this->createImage();

        // Create a row container with a border
        $container = new RowContainer;
        $borderDraw = $this->stroke('#ff0000', 50);
        $container->setBorder($borderDraw, $borderDraw, $borderDraw, $borderDraw);
        $container->setMargin(50);
        $container->addItem((new Rectangle($this->draw('#4ade80'))));

        $this->saveImage($imagick, $container, __FUNCTION__.'.png');
    }

    public function test_container_with_simple_border_padding(): void
    {
        $imagick = $this->createImage();

        // Create a row container with a border
        $container = new RowContainer;
        $borderDraw = $this->stroke('#ff0000', 50);
        $container->setBorder($borderDraw, $borderDraw, $borderDraw, $borderDraw);
        $container->setPadding(50);
        $container->addItem((new Rectangle($this->draw('#4ade80'))));

        $this->saveImage($imagick, $container, __FUNCTION__.'.png');
    }

    public function test_container_with_simple_border_margin_and_padding(): void
    {
        $imagick = $this->createImage();

        // Create a row container with a border
        $container = new RowContainer;
        $borderDraw = $this->stroke('#ff0000', 50);
        $container->setBorder($borderDraw, $borderDraw, $borderDraw, $borderDraw);
        $container->setMargin(50);
        $container->setPadding(50);
        $container->addItem((new Rectangle($this->draw('#4ade80'))));

        $this->saveImage($imagick, $container, __FUNCTION__.'.png');
    }

    public function test_container_with_border_margin_and_padding(): void
    {
        $imagick = $this->createImage();

        // Create a row container with a border
        $container = new RowContainer;
        $borderDraw = $this->stroke('#ff0000');
        $container->setBorder($borderDraw, $borderDraw, $borderDraw, $borderDraw);

        // Add items combining margin and padding in various ways
        // Item 1: uniform margin and padding
        $container->addItem((new Rectangle($this->draw('#4ade80')))->setMargin(20)->setPadding(20), 300);

        // Item 2: asymmetric margin and padding (top, right, bottom, left)
        $container->addItem(
            (new Rectangle($this->draw('#f87171')))->setMargin(10, 20, 30, 40)->setPadding(40, 30, 20, 10),
            300
        );

        // Item 3: two-value margin (tb, lr) and padding (tb, lr)
        $container->addItem((new Rectangle($this->draw('#60a5fa')))->setMargin(30, 10)->setPadding(10, 30), 300);

        $this->saveImage($imagick, $container, __FUNCTION__.'.png');
    }

    public function test_rectangle_border_with_margin(): void
    {
        $imagick = $this->createImage();

        $container = new RowContainer;
        $rectangle = new Rectangle($this->draw('#4ade80'));
        // border on the item + margin
        $rectangle->setBorder(
            $this->stroke('#ff0000'),
            $this->stroke('#00ff00'),
            $this->stroke('#0000ff'),
            $this->stroke('#ffff00')
        );
        $rectangle->setMargin(40);
        $container->addItem($rectangle);

        $this->saveImage($imagick, $container, __FUNCTION__.'.png');
    }

    public function test_rectangle_border_with_padding(): void
    {
        $imagick = $this->createImage();

        $container = new RowContainer;
        $rectangle = new Rectangle($this->draw('#4ade80'));
        // border on the item + padding
        $rectangle->setBorder(
            $this->stroke('#ff0000'),
            $this->stroke('#00ff00'),
            $this->stroke('#0000ff'),
            $this->stroke('#ffff00')
        );
        $rectangle->setPadding(40);
        $container->addItem($rectangle);

        $this->saveImage($imagick, $container, __FUNCTION__.'.png');
    }

    public function test_rectangle_border_with_margin_and_padding(): void
    {
        $imagick = $this->createImage();

        $container = new RowContainer;
        $rectangle = new Rectangle($this->draw('#4ade80'));
        // border on the item + margin and padding
        $rectangle->setBorder(
            $this->stroke('#ff0000'),
            $this->stroke('#00ff00'),
            $this->stroke('#0000ff'),
            $this->stroke('#ffff00')
        );
        $rectangle->setMargin(20, 40, 60, 30);
        $rectangle->setPadding(30, 10);
        $container->addItem($rectangle);

        $this->saveImage($imagick, $container, __FUNCTION__.'.png');
    }
}
