<?php

/*
 * The Imagick Layout Engine
 * Copyright (C) 2025 Kehet
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

use Kehet\ImagickLayoutEngine\Containers\GridContainer;
use Kehet\ImagickLayoutEngine\Items\Rectangle;

class GridContainerTest extends TestCase
{
    public function test_grid_container_auto_placement(): void
    {
        $imagick = $this->createImage();

        $frame = new GridContainer;
        $frame->setTemplateColumns(null, null, null);
        $frame->addItem(new Rectangle($this->draw('#fee2e2')));
        $frame->addItem(new Rectangle($this->draw('#fca5a5')));
        $frame->addItem(new Rectangle($this->draw('#dc2626')));
        $frame->addItem(new Rectangle($this->draw('#450a0a')));
        $frame->addItem(new Rectangle($this->draw('#7f1d1d')));

        $this->saveImage($imagick, $frame, __CLASS__.'__'.__FUNCTION__.'.png');
    }

    public function test_grid_container_fixed_and_auto_columns(): void
    {
        $imagick = $this->createImage();

        $frame = new GridContainer;
        $frame->setTemplateColumns(200, null, 300, null);
        $frame->addItem(new Rectangle($this->draw('#ecfccb')));
        $frame->addItem(new Rectangle($this->draw('#bef264')));
        $frame->addItem(new Rectangle($this->draw('#65a30d')));
        $frame->addItem(new Rectangle($this->draw('#1a2e05')));

        $this->saveImage($imagick, $frame, __CLASS__.'__'.__FUNCTION__.'.png');
    }

    public function test_grid_container_with_gap(): void
    {
        $imagick = $this->createImage();

        $frame = new GridContainer;
        $frame->setTemplateColumns(null, null);
        $frame->setTemplateRows(null, null);
        $frame->setGap(20);
        $frame->addItem(new Rectangle($this->draw('#cffafe')));
        $frame->addItem(new Rectangle($this->draw('#67e8f9')));
        $frame->addItem(new Rectangle($this->draw('#0891b2')));
        $frame->addItem(new Rectangle($this->draw('#164e63')));

        $this->saveImage($imagick, $frame, __CLASS__.'__'.__FUNCTION__.'.png');
    }

    public function test_grid_container_column_span(): void
    {
        $imagick = $this->createImage();

        $frame = new GridContainer;
        $frame->setTemplateColumns(null, null, null);
        $frame->addItem(new Rectangle($this->draw('#ede9fe')), columnSpan: 2);
        $frame->addItem(new Rectangle($this->draw('#a78bfa')));
        $frame->addItem(new Rectangle($this->draw('#5b21b6')));
        $frame->addItem(new Rectangle($this->draw('#2e1065')));
        $frame->addItem(new Rectangle($this->draw('#f5f3ff')));

        $this->saveImage($imagick, $frame, __CLASS__.'__'.__FUNCTION__.'.png');
    }

    public function test_grid_container_explicit_placement(): void
    {
        $imagick = $this->createImage();

        $frame = new GridContainer;
        $frame->setTemplateColumns(null, null);
        $frame->setTemplateRows(null, null);
        $frame->addItem(new Rectangle($this->draw('#fef3c7')), column: 1, row: 0);
        $frame->addItem(new Rectangle($this->draw('#fde68a')), column: 0, row: 1);
        $frame->addItem(new Rectangle($this->draw('#d97706')));

        $this->saveImage($imagick, $frame, __CLASS__.'__'.__FUNCTION__.'.png');
    }
}
