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
use Kehet\ImagickLayoutEngine\Tests\TestCase;

class ImageTest extends TestCase
{
    public function test_image_row_fit(): void
    {
        $imagick = $this->createImage();

        $frame = new RowContainer;

        $frame->addItem(new Image(self::SMALL_IMAGE, ImageMode::FIT, Image::GRAVITY_TOP));
        $frame->addItem(new Image(self::SMALL_IMAGE, ImageMode::FIT, Image::GRAVITY_CENTER));
        $frame->addItem(new Image(self::SMALL_IMAGE, ImageMode::FIT, Image::GRAVITY_BOTTOM));

        $this->saveImage($imagick, $frame, __FUNCTION__.'.png');
    }

    public function test_image_column_fit(): void
    {
        $imagick = $this->createImage();

        $frame = new ColumnContainer;

        $frame->addItem(new Image(self::SMALL_IMAGE, ImageMode::FIT, Image::GRAVITY_LEFT));
        $frame->addItem(new Image(self::SMALL_IMAGE, ImageMode::FIT, Image::GRAVITY_CENTER));
        $frame->addItem(new Image(self::SMALL_IMAGE, ImageMode::FIT, Image::GRAVITY_RIGHT));

        $this->saveImage($imagick, $frame, __FUNCTION__.'.png');
    }

    public function test_image_row_fill(): void
    {
        $imagick = $this->createImage();

        $frame = new RowContainer;

        $frame->addItem(new Image(self::SMALL_IMAGE, ImageMode::FILL, Image::GRAVITY_LEFT));
        $frame->addItem(new Image(self::SMALL_IMAGE, ImageMode::FILL, Image::GRAVITY_CENTER));
        $frame->addItem(new Image(self::SMALL_IMAGE, ImageMode::FILL, Image::GRAVITY_RIGHT));

        $this->saveImage($imagick, $frame, __FUNCTION__.'.png');
    }

    public function test_image_column_fill(): void
    {
        $imagick = $this->createImage();

        $frame = new ColumnContainer;

        $frame->addItem(new Image(self::SMALL_IMAGE, ImageMode::FILL, Image::GRAVITY_TOP));
        $frame->addItem(new Image(self::SMALL_IMAGE, ImageMode::FILL, Image::GRAVITY_CENTER));
        $frame->addItem(new Image(self::SMALL_IMAGE, ImageMode::FILL, Image::GRAVITY_BOTTOM));

        $this->saveImage($imagick, $frame, __FUNCTION__.'.png');
    }

    public function test_image_tiny_none(): void
    {
        $imagick = $this->createImage();

        $frame = new ColumnContainer;

        $row1 = new RowContainer;
        $row1->addItem(new Image(self::TINY_IMAGE, ImageMode::NONE, Image::GRAVITY_TOP_LEFT));
        $row1->addItem(new Image(self::TINY_IMAGE, ImageMode::NONE, Image::GRAVITY_TOP));
        $row1->addItem(new Image(self::TINY_IMAGE, ImageMode::NONE, Image::GRAVITY_TOP_RIGHT));
        $frame->addItem($row1);

        $row2 = new RowContainer;
        $row2->addItem(new Image(self::TINY_IMAGE, ImageMode::NONE, Image::GRAVITY_LEFT));
        $row2->addItem(new Image(self::TINY_IMAGE, ImageMode::NONE, Image::GRAVITY_CENTER));
        $row2->addItem(new Image(self::TINY_IMAGE, ImageMode::NONE, Image::GRAVITY_RIGHT));
        $frame->addItem($row2);

        $row3 = new RowContainer;
        $row3->addItem(new Image(self::TINY_IMAGE, ImageMode::NONE, Image::GRAVITY_BOTTOM_LEFT));
        $row3->addItem(new Image(self::TINY_IMAGE, ImageMode::NONE, Image::GRAVITY_BOTTOM));
        $row3->addItem(new Image(self::TINY_IMAGE, ImageMode::NONE, Image::GRAVITY_BOTTOM_RIGHT));
        $frame->addItem($row3);

        $this->saveImage($imagick, $frame, __FUNCTION__.'.png');
    }

    public function test_image_small_none(): void
    {
        $imagick = $this->createImage();

        $frame = new ColumnContainer;

        $row1 = new RowContainer;
        $row1->addItem(new Image(self::SMALL_IMAGE, ImageMode::NONE, Image::GRAVITY_TOP_LEFT));
        $row1->addItem(new Image(self::SMALL_IMAGE, ImageMode::NONE, Image::GRAVITY_TOP));
        $row1->addItem(new Image(self::SMALL_IMAGE, ImageMode::NONE, Image::GRAVITY_TOP_RIGHT));
        $frame->addItem($row1);

        $row2 = new RowContainer;
        $row2->addItem(new Image(self::SMALL_IMAGE, ImageMode::NONE, Image::GRAVITY_LEFT));
        $row2->addItem(new Image(self::SMALL_IMAGE, ImageMode::NONE, Image::GRAVITY_CENTER));
        $row2->addItem(new Image(self::SMALL_IMAGE, ImageMode::NONE, Image::GRAVITY_RIGHT));
        $frame->addItem($row2);

        $row3 = new RowContainer;
        $row3->addItem(new Image(self::SMALL_IMAGE, ImageMode::NONE, Image::GRAVITY_BOTTOM_LEFT));
        $row3->addItem(new Image(self::SMALL_IMAGE, ImageMode::NONE, Image::GRAVITY_BOTTOM));
        $row3->addItem(new Image(self::SMALL_IMAGE, ImageMode::NONE, Image::GRAVITY_BOTTOM_RIGHT));
        $frame->addItem($row3);

        $this->saveImage($imagick, $frame, __FUNCTION__.'.png');
    }

    public function test_image_large_none(): void
    {
        $imagick = $this->createImage();

        $frame = new ColumnContainer;

        $row1 = new RowContainer;
        $row1->addItem(new Image(self::LARGE_IMAGE, ImageMode::NONE, Image::GRAVITY_TOP_LEFT));
        $row1->addItem(new Image(self::LARGE_IMAGE, ImageMode::NONE, Image::GRAVITY_TOP));
        $row1->addItem(new Image(self::LARGE_IMAGE, ImageMode::NONE, Image::GRAVITY_TOP_RIGHT));
        $frame->addItem($row1);

        $row2 = new RowContainer;
        $row2->addItem(new Image(self::LARGE_IMAGE, ImageMode::NONE, Image::GRAVITY_LEFT));
        $row2->addItem(new Image(self::LARGE_IMAGE, ImageMode::NONE, Image::GRAVITY_CENTER));
        $row2->addItem(new Image(self::LARGE_IMAGE, ImageMode::NONE, Image::GRAVITY_RIGHT));
        $frame->addItem($row2);

        $row3 = new RowContainer;
        $row3->addItem(new Image(self::LARGE_IMAGE, ImageMode::NONE, Image::GRAVITY_BOTTOM_LEFT));
        $row3->addItem(new Image(self::LARGE_IMAGE, ImageMode::NONE, Image::GRAVITY_BOTTOM));
        $row3->addItem(new Image(self::LARGE_IMAGE, ImageMode::NONE, Image::GRAVITY_BOTTOM_RIGHT));
        $frame->addItem($row3);

        $this->saveImage($imagick, $frame, __FUNCTION__.'.png');
    }
}
