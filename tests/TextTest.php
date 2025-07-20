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

use ImagickDraw;
use ImagickPixel;
use Kehet\ImagickLayoutEngine\Containers\ColumnContainer;
use Kehet\ImagickLayoutEngine\Items\Text;

class TextTest extends TestCase
{

    const string TINY_TEXT = 'Lorem ipsum dolor sit amet.';
    const string SHORT_TEXT = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec elementum vulputate eros at rutrum.';
    const string LONG_TEXT = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris vitae ultrices erat. Integer id eleifend diam, sed commodo lacus.  Fusce iaculis aliquam pulvinar. Donec dictum mollis volutpat. Nulla facilisi. Nulla egestas hendrerit lobortis. Proin tincidunt interdum eros a pharetra. Nam tincidunt, justo eget pulvinar consequat, velit tortor iaculis urna, in vulputate libero ipsum at ante. ';

    public function test_text(): void
    {
        $imagick = $this->createImage();

        $frame = new ColumnContainer();
        $frame->addItem(new Text($this->draw('#000'), self::TINY_TEXT));
        $frame->addItem(new Text($this->draw('#000'), self::SHORT_TEXT));
        $frame->addItem(new Text($this->draw('#000'), self::LONG_TEXT));

        $this->saveImage($imagick, $frame, __FUNCTION__ . '.png');
    }

    public function test_text_with_initial_font_size(): void
    {
        $imagick = $this->createImage();

        $frame = new ColumnContainer();
        $frame->addItem(new Text($this->draw('#000'), self::TINY_TEXT, initialFontSize: 100));
        $frame->addItem(new Text($this->draw('#000'), self::SHORT_TEXT, initialFontSize: 100));
        $frame->addItem(new Text($this->draw('#000'), self::LONG_TEXT, initialFontSize: 100));

        $this->saveImage($imagick, $frame, __FUNCTION__ . '.png');
    }

    public function test_text_with_minimum_font_size(): void
    {
        $imagick = $this->createImage();

        $frame = new ColumnContainer();
        $frame->addItem(new Text($this->draw('#000'), self::TINY_TEXT, minFontSize: 100));
        $frame->addItem(new Text($this->draw('#000'), self::SHORT_TEXT, minFontSize: 100));
        $frame->addItem(new Text($this->draw('#000'), self::LONG_TEXT, minFontSize: 100));

        $this->saveImage($imagick, $frame, __FUNCTION__ . '.png');
    }

}
