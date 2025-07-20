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

namespace Kehet\ImagickLayoutEngine\Enums;

enum Gravity: string
{
    case TOP_LEFT = 'top-left';
    case TOP = 'top';
    case TOP_RIGHT = 'top-right';
    case LEFT = 'left';
    case CENTER = 'center';
    case RIGHT = 'right';
    case BOTTOM_LEFT = 'bottom-left';
    case BOTTOM = 'bottom';
    case BOTTOM_RIGHT = 'bottom-right';

}
