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

namespace Kehet\ImagickLayoutEngine\Items;

use Imagick;
use ImagickDraw;

/**
 * Represents an simple text that can be drawn onto container grid.
 * Chops text and scales font size down as needed.
 */
class TextWrap implements DrawableInterface
{
    public function __construct(
        protected ImagickDraw $draw,
        protected string $text,
        protected int $initialFontSize = 60,
        protected int $minFontSize = 10,
    ) {}

    protected function calculateWrapping(Imagick $imagick, int $width): array
    {
        $words = explode(' ', $this->text);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            $testLine = $currentLine.($currentLine ? ' ' : '').$word;
            $metrics = $imagick->queryFontMetrics($this->draw, $testLine);

            if ($metrics['textWidth'] <= $width) {
                // Word fits, add it to the current line
                $currentLine = $testLine;
            } else {
                // Word doesn't fit, start a new line
                if ($currentLine) {
                    $lines[] = $currentLine;
                }

                $currentLine = $word;
            }
        }

        if ($currentLine) {
            $lines[] = $currentLine;
        }

        $lineHeight = $imagick->queryFontMetrics($this->draw, 'Tg')['textHeight'];
        $totalHeight = count($lines) * $lineHeight;

        return [
            'lines' => $lines,
            'lineHeight' => $lineHeight,
            'totalHeight' => $totalHeight,
        ];
    }

    public function draw(Imagick $imagick, int $x, int $y, int $width, int $height): void
    {
        $this->draw->setGravity(Imagick::GRAVITY_NORTHWEST);

        $fontSize = $this->initialFontSize;
        $this->draw->setFontSize($fontSize);

        $result = $this->calculateWrapping($imagick, $width);

        while ($result['totalHeight'] > $height && $fontSize > $this->minFontSize) {
            $fontSize -= 2;
            $this->draw->setFontSize($fontSize);
            $result = $this->calculateWrapping($imagick, $width);
        }

        $lines = $result['lines'];
        $lineHeight = $result['lineHeight'];
        $currentY = $y;

        foreach ($lines as $line) {
            $this->draw->annotation($x, $currentY, $line);
            $currentY += $lineHeight;

            // stop if we exceed the available height
            if ($currentY - $y > $height) {
                break;
            }
        }

        $imagick->drawImage($this->draw);
    }
}
