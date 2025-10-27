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

namespace Kehet\ImagickLayoutEngine\Containers;

use Kehet\ImagickLayoutEngine\Items\DrawableInterface;
use Kehet\ImagickLayoutEngine\Traits\BorderTrait;
use Kehet\ImagickLayoutEngine\Traits\MarginTrait;
use Kehet\ImagickLayoutEngine\Traits\PaddingTrait;

abstract class Container implements DrawableInterface
{

    use BorderTrait;
    use PaddingTrait;
    use MarginTrait;

    /** @var array> */
    protected array $items;

    public function addItem(
        DrawableInterface $item,
        ?int $forceSize = null
    ): void {

        $this->items[] = [
            'item'    => $item,
            'size'    => $forceSize,
        ];
    }
}
