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

class ContainerTest extends TestCase
{

    public function test_row_container(): void
    {
        $imagick = $this->createImage();

        $frame = new RowContainer();
        $frame->addItem(new Rectangle($this->draw('#fee2e2')));
        $frame->addItem(new Rectangle($this->draw('#fca5a5')));
        $frame->addItem(new Rectangle($this->draw('#dc2626')));
        $frame->addItem(new Rectangle($this->draw('#450a0a')));

        $this->saveImage($imagick, $frame, __FUNCTION__ . '.png');
    }

    public function test_row_container_with_forced_width(): void
    {
        $imagick = $this->createImage();

        $frame = new RowContainer();
        $frame->addItem(new Rectangle($this->draw('#fee2e2')));
        $frame->addItem(new Rectangle($this->draw('#fca5a5')), 100);
        $frame->addItem(new Rectangle($this->draw('#dc2626')), 150);
        $frame->addItem(new Rectangle($this->draw('#450a0a')));

        $this->saveImage($imagick, $frame, __FUNCTION__ . '.png');
    }

    public function test_column_container(): void
    {
        $imagick = $this->createImage();

        $frame = new ColumnContainer();
        $frame->addItem(new Rectangle($this->draw('#fee2e2')));
        $frame->addItem(new Rectangle($this->draw('#fca5a5')));
        $frame->addItem(new Rectangle($this->draw('#dc2626')));
        $frame->addItem(new Rectangle($this->draw('#450a0a')));

        $this->saveImage($imagick, $frame, __FUNCTION__ . '.png');
    }

    public function test_column_container_with_forced_width(): void
    {
        $imagick = $this->createImage();

        $frame = new ColumnContainer();
        $frame->addItem(new Rectangle($this->draw('#fee2e2')));
        $frame->addItem(new Rectangle($this->draw('#fca5a5')), 150);
        $frame->addItem(new Rectangle($this->draw('#dc2626')), 100);
        $frame->addItem(new Rectangle($this->draw('#450a0a')));

        $this->saveImage($imagick, $frame, __FUNCTION__ . '.png');
    }

    public function test_cascading_containers(): void
    {
        $imagick = $this->createImage();

        $frame = new ColumnContainer();

        $row = new RowContainer();
        $row->addItem(new Rectangle($this->draw('#fee2e2')), 50);
        $row->addItem(new Rectangle($this->draw('#fca5a5')), 100);
        $row->addItem(new Rectangle($this->draw('#dc2626')), 150);
        $row->addItem(new Rectangle($this->draw('#450a0a')));
        $frame->addItem($row);

        $row = new RowContainer();
        $row->addItem(new Rectangle($this->draw('#ecfccb')));
        $row->addItem(new Rectangle($this->draw('#bef264')), 50);
        $row->addItem(new Rectangle($this->draw('#65a30d')), 100);
        $row->addItem(new Rectangle($this->draw('#1a2e05')), 150);
        $frame->addItem($row);

        $row = new RowContainer();
        $row->addItem(new Rectangle($this->draw('#cffafe')));
        $row->addItem(new Rectangle($this->draw('#67e8f9')));
        $row->addItem(new Rectangle($this->draw('#0891b2')));
        $row->addItem(new Rectangle($this->draw('#164e63')));
        $frame->addItem($row);

        $this->saveImage($imagick, $frame, __FUNCTION__ . '.png');
    }

}
