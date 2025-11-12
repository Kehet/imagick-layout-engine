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

namespace Kehet\ImagickLayoutEngine\Tests\Traits;

/** @mixin \PHPUnit\Framework\TestCase */
trait HasImageAssertion
{

    /**
     * @throws \ImagickException
     */
    public function assertImageEquals(string $expectedPath, string $actualPath, float $threshold = 0.01): void
    {
        $this->assertFileExists($expectedPath, 'Expected image file does not exist');
        $this->assertFileExists($actualPath, 'Generated image file does not exist');

        $expected = new \Imagick($expectedPath);
        $actual = new \Imagick($actualPath);

        $expectedWidth = $expected->getImageWidth();
        $expectedHeight = $expected->getImageHeight();
        $actualWidth = $actual->getImageWidth();
        $actualHeight = $actual->getImageHeight();

        $this->assertEquals($expectedWidth, $actualWidth, 'Image widths do not match');
        $this->assertEquals($expectedHeight, $actualHeight, 'Image heights do not match');

        $result = $expected->compareImages($actual, \Imagick::METRIC_MEANABSOLUTEERROR);
        $differenceRatio = $result[1];

        if (getenv('SAVE_IMAGE_DIFF') !== false) {
            $debugFilename = __DIR__ . '/../temp/' . basename($actualPath, '.png') . '_diff.png';
            $result[0]->setImageFormat('png');
            $result[0]->writeImage($debugFilename);
        }

        $expected->clear();
        $actual->clear();
        $result[0]->clear();

        $this->assertLessThanOrEqual(
            $threshold,
            $differenceRatio,
            sprintf('Images differ by %.2f%% (threshold: %.2f%%)', $differenceRatio * 100, $threshold * 100)
        );
    }

}
