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
use Kehet\ImagickLayoutEngine\Containers\StackContainer;
use Kehet\ImagickLayoutEngine\Enums\Gravity;
use Kehet\ImagickLayoutEngine\Enums\ImageMode;
use Kehet\ImagickLayoutEngine\Items\Image;
use Kehet\ImagickLayoutEngine\Items\Rectangle;
use Kehet\ImagickLayoutEngine\Items\Text;

class StackContainerTest extends TestCase
{
    public function test_overlay_background_and_foreground(): void
    {
        $imagick = $this->createImage();

        $stack = new StackContainer;

        // Background full area
        $bg = new Rectangle($this->draw('#e5e7eb')); // gray-200
        $stack->addItem($bg);

        // Foreground smaller rectangle with transparency and margins to show background
        $fg = new Rectangle($this->draw('rgba(252, 165, 165, 0.85)')); // red-300 with alpha
        $fg->setMargin(100); // inset on all sides
        $stack->addItem($fg);

        $this->saveImage($imagick, $stack, __CLASS__.'__'.__FUNCTION__.'.png');
    }

    public function test_overlay_text_over_image(): void
    {
        $imagick = $this->createImage();

        $frame = new ColumnContainer;

        $stack = new StackContainer;
        $stack->addItem(new Image(self::SMALL_IMAGE, ImageMode::FILL));
        $stack->addItem(new Text($this->draw('#fff'), 'Text over image', initialFontSize: 150, gravity: Gravity::CENTER));
        $frame->addItem($stack);

        $stack = new StackContainer;
        $stack->addItem(new Text($this->draw('#fff'), 'Text under image', initialFontSize: 150, gravity: Gravity::CENTER));
        $stack->addItem(new Image(self::SMALL_IMAGE, ImageMode::FILL));
        $frame->addItem($stack);

        $this->saveImage($imagick, $frame, __CLASS__.'__'.__FUNCTION__.'.png');
    }
}
