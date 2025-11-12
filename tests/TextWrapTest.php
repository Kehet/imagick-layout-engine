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
use Kehet\ImagickLayoutEngine\Enums\Gravity;
use Kehet\ImagickLayoutEngine\Items\TextWrap;

class TextWrapTest extends TestCase
{
    public function test_text(): void
    {
        $imagick = $this->createImage();

        $frame = new ColumnContainer;
        $frame->addItem(new TextWrap($this->draw('#000'), self::TINY_TEXT));
        $frame->addItem(new TextWrap($this->draw('#000'), self::SHORT_TEXT));
        $frame->addItem(new TextWrap($this->draw('#000'), self::LONG_TEXT));

        $this->saveImage($imagick, $frame, __CLASS__ . '__' . __FUNCTION__.'.png');
    }

    public function test_text_with_initial_font_size(): void
    {
        $imagick = $this->createImage();

        $frame = new ColumnContainer;
        $frame->addItem(new TextWrap($this->draw('#000'), self::TINY_TEXT, initialFontSize: 100));
        $frame->addItem(new TextWrap($this->draw('#000'), self::SHORT_TEXT, initialFontSize: 100));
        $frame->addItem(new TextWrap($this->draw('#000'), self::LONG_TEXT, initialFontSize: 100));

        $this->saveImage($imagick, $frame, __CLASS__ . '__' . __FUNCTION__.'.png');
    }

    public function test_text_with_minimum_font_size(): void
    {
        $imagick = $this->createImage();

        $frame = new ColumnContainer;
        $frame->addItem(new TextWrap($this->draw('#000'), self::TINY_TEXT, minFontSize: 100));
        $frame->addItem(new TextWrap($this->draw('#000'), self::SHORT_TEXT, minFontSize: 100));
        $frame->addItem(new TextWrap($this->draw('#000'), self::LONG_TEXT, minFontSize: 100));

        $this->saveImage($imagick, $frame, __CLASS__ . '__' . __FUNCTION__.'.png');
    }

    public function test_text_wrap_with_gravity(): void
    {
        $imagick = $this->createImage();

        // Create a 3x3 grid to demonstrate all gravity options
        $mainContainer = new ColumnContainer;

        // Top row: TOP_LEFT, TOP, TOP_RIGHT
        $row = new RowContainer;
        $row->addItem($this->createGravityDemoContainer(Gravity::TOP_LEFT, 'TOP_LEFT'));
        $row->addItem($this->createGravityDemoContainer(Gravity::TOP, 'TOP'));
        $row->addItem($this->createGravityDemoContainer(Gravity::TOP_RIGHT, 'TOP_RIGHT'));
        $mainContainer->addItem($row);

        // Middle row: LEFT, CENTER, RIGHT
        $row = new RowContainer;
        $row->addItem($this->createGravityDemoContainer(Gravity::LEFT, 'LEFT'));
        $row->addItem($this->createGravityDemoContainer(Gravity::CENTER, 'CENTER'));
        $row->addItem($this->createGravityDemoContainer(Gravity::RIGHT, 'RIGHT'));
        $mainContainer->addItem($row);

        // Bottom row: BOTTOM_LEFT, BOTTOM, BOTTOM_RIGHT
        $row = new RowContainer;
        $row->addItem($this->createGravityDemoContainer(Gravity::BOTTOM_LEFT, 'BOTTOM_LEFT'));
        $row->addItem($this->createGravityDemoContainer(Gravity::BOTTOM, 'BOTTOM'));
        $row->addItem($this->createGravityDemoContainer(Gravity::BOTTOM_RIGHT, 'BOTTOM_RIGHT'));
        $mainContainer->addItem($row);

        $this->saveImage($imagick, $mainContainer, __CLASS__ . '__' . __FUNCTION__.'.png');
    }

    private function createGravityDemoContainer(Gravity $gravity, string $label): ColumnContainer
    {
        $container = new ColumnContainer;

        // Add a text with the specified gravity
        $container->addItem(new TextWrap($this->draw('#ff0000'), 'Text with gravity that wraps to multiple lines', initialFontSize: 25, gravity: $gravity));

        // Add a label at the bottom
        $container->addItem(new TextWrap($this->draw('#000000'), $label));

        return $container;
    }
}
