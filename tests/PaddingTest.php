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
use Kehet\ImagickLayoutEngine\Items\Rectangle;

class PaddingTest extends TestCase
{
    public function test_row_container_with_padding(): void
    {
        $imagick = $this->createImage();

        $frame = new RowContainer;
        $frame->addItem(new Rectangle($this->draw('#fee2e2')), padding: 40);
        $frame->addItem(new Rectangle($this->draw('#fca5a5')), padding: 60);
        $frame->addItem(new Rectangle($this->draw('#dc2626')), padding: 80);
        $frame->addItem(new Rectangle($this->draw('#450a0a')), padding: 100);

        $this->saveImage($imagick, $frame, __FUNCTION__.'.png');
    }

    public function test_column_container_with_padding(): void
    {
        $imagick = $this->createImage();

        $frame = new ColumnContainer;
        $frame->addItem(new Rectangle($this->draw('#fee2e2')), padding: 40);
        $frame->addItem(new Rectangle($this->draw('#fca5a5')), padding: 60);
        $frame->addItem(new Rectangle($this->draw('#dc2626')), padding: 80);
        $frame->addItem(new Rectangle($this->draw('#450a0a')), padding: 100);

        $this->saveImage($imagick, $frame, __FUNCTION__.'.png');
    }

    public function test_row_container_with_two_value_padding(): void
    {
        $imagick = $this->createImage();

        $frame = new RowContainer;
        // First value: top/bottom, Second value: left/right
        $frame->addItem(new Rectangle($this->draw('#fee2e2')), padding: [20, 40]);
        $frame->addItem(new Rectangle($this->draw('#fca5a5')), padding: [40, 20]);
        $frame->addItem(new Rectangle($this->draw('#dc2626')), padding: [60, 80]);
        $frame->addItem(new Rectangle($this->draw('#450a0a')), padding: [80, 60]);

        $this->saveImage($imagick, $frame, __FUNCTION__.'.png');
    }

    public function test_column_container_with_two_value_padding(): void
    {
        $imagick = $this->createImage();

        $frame = new ColumnContainer;
        // First value: top/bottom, Second value: left/right
        $frame->addItem(new Rectangle($this->draw('#fee2e2')), padding: [20, 40]);
        $frame->addItem(new Rectangle($this->draw('#fca5a5')), padding: [40, 20]);
        $frame->addItem(new Rectangle($this->draw('#dc2626')), padding: [60, 80]);
        $frame->addItem(new Rectangle($this->draw('#450a0a')), padding: [80, 60]);

        $this->saveImage($imagick, $frame, __FUNCTION__.'.png');
    }

    public function test_row_container_with_three_value_padding(): void
    {
        $imagick = $this->createImage();

        $frame = new RowContainer;
        // First value: top, Second value: left/right, Third value: bottom
        $frame->addItem(new Rectangle($this->draw('#fee2e2')), padding: [20, 40, 60]);
        $frame->addItem(new Rectangle($this->draw('#fca5a5')), padding: [40, 60, 20]);
        $frame->addItem(new Rectangle($this->draw('#dc2626')), padding: [60, 20, 40]);
        $frame->addItem(new Rectangle($this->draw('#450a0a')), padding: [80, 40, 60]);

        $this->saveImage($imagick, $frame, __FUNCTION__.'.png');
    }

    public function test_column_container_with_three_value_padding(): void
    {
        $imagick = $this->createImage();

        $frame = new ColumnContainer;
        // First value: top, Second value: left/right, Third value: bottom
        $frame->addItem(new Rectangle($this->draw('#fee2e2')), padding: [20, 40, 60]);
        $frame->addItem(new Rectangle($this->draw('#fca5a5')), padding: [40, 60, 20]);
        $frame->addItem(new Rectangle($this->draw('#dc2626')), padding: [60, 20, 40]);
        $frame->addItem(new Rectangle($this->draw('#450a0a')), padding: [80, 40, 60]);

        $this->saveImage($imagick, $frame, __FUNCTION__.'.png');
    }

    public function test_row_container_with_four_value_padding(): void
    {
        $imagick = $this->createImage();

        $frame = new RowContainer;
        // Values: top, right, bottom, left (clockwise)
        $frame->addItem(new Rectangle($this->draw('#fee2e2')), padding: [20, 40, 60, 80]);
        $frame->addItem(new Rectangle($this->draw('#fca5a5')), padding: [40, 60, 80, 20]);
        $frame->addItem(new Rectangle($this->draw('#dc2626')), padding: [60, 80, 20, 40]);
        $frame->addItem(new Rectangle($this->draw('#450a0a')), padding: [80, 20, 40, 60]);

        $this->saveImage($imagick, $frame, __FUNCTION__.'.png');
    }

    public function test_column_container_with_four_value_padding(): void
    {
        $imagick = $this->createImage();

        $frame = new ColumnContainer;
        // Values: top, right, bottom, left (clockwise)
        $frame->addItem(new Rectangle($this->draw('#fee2e2')), padding: [20, 40, 60, 80]);
        $frame->addItem(new Rectangle($this->draw('#fca5a5')), padding: [40, 60, 80, 20]);
        $frame->addItem(new Rectangle($this->draw('#dc2626')), padding: [60, 80, 20, 40]);
        $frame->addItem(new Rectangle($this->draw('#450a0a')), padding: [80, 20, 40, 60]);

        $this->saveImage($imagick, $frame, __FUNCTION__.'.png');
    }

    public function test_nested_containers_with_padding(): void
    {
        $imagick = $this->createImage();

        $frame = new ColumnContainer;

        $row = new RowContainer;
        $row->addItem(new Rectangle($this->draw('#fee2e2')), 50, 10);
        $row->addItem(new Rectangle($this->draw('#fca5a5')), 100, 20);
        $row->addItem(new Rectangle($this->draw('#dc2626')), 150, 30);
        $row->addItem(new Rectangle($this->draw('#450a0a')), null, 40);
        $frame->addItem($row, padding: 20);

        $row = new RowContainer;
        $row->addItem(new Rectangle($this->draw('#ecfccb')), null, 10);
        $row->addItem(new Rectangle($this->draw('#bef264')), 50, 20);
        $row->addItem(new Rectangle($this->draw('#65a30d')), 100, 30);
        $row->addItem(new Rectangle($this->draw('#1a2e05')), 150, 40);
        $frame->addItem($row, padding: 30);

        $row = new RowContainer;
        $row->addItem(new Rectangle($this->draw('#cffafe')), padding: 10);
        $row->addItem(new Rectangle($this->draw('#67e8f9')), padding: 20);
        $row->addItem(new Rectangle($this->draw('#0891b2')), padding: 30);
        $row->addItem(new Rectangle($this->draw('#164e63')), padding: 40);
        $frame->addItem($row, padding: 40);

        $this->saveImage($imagick, $frame, __FUNCTION__.'.png');
    }
}
