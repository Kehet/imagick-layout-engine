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

use Imagick;
use ImagickDraw;
use ImagickPixel;
use Kehet\ImagickLayoutEngine\Containers\Container;
use Kehet\ImagickLayoutEngine\Tests\Traits\HasImageAssertion;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    use HasImageAssertion;

    const string TINY_TEXT = 'Lorem ipsum dolor sit amet.';

    const string SHORT_TEXT = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec elementum vulputate eros at rutrum.';

    const string LONG_TEXT = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris vitae ultrices erat. Integer id eleifend diam, sed commodo lacus.  Fusce iaculis aliquam pulvinar. Donec dictum mollis volutpat. Nulla facilisi. Nulla egestas hendrerit lobortis. Proin tincidunt interdum eros a pharetra. Nam tincidunt, justo eget pulvinar consequat, velit tortor iaculis urna, in vulputate libero ipsum at ante. ';

    const string TINY_IMAGE = __DIR__.'/assets/example-image-tiny.jpeg';

    const string SMALL_IMAGE = __DIR__.'/assets/example-image-small.jpeg';

    const string LARGE_IMAGE = __DIR__.'/assets/example-image-large.jpeg';

    public function draw(string $fill): ImagickDraw
    {
        $return = new ImagickDraw;
        $return->setFont('DejaVu-Sans'); // This seems to be available on GH runners
        $return->setFillColor(new ImagickPixel($fill));

        return $return;
    }

    public function stroke(string $fill, int $width = 50): ImagickDraw
    {
        $return = new ImagickDraw;
        $return->setStrokeColor(new ImagickPixel($fill));
        $return->setStrokeWidth($width);

        return $return;
    }

    public function createImage(): Imagick
    {
        $imagick = new Imagick;
        $imagick->newImage(1500, 1000, new ImagickPixel('white'));

        return $imagick;
    }

    public function saveImage(Imagick $imagick, Container $container, string $filename): void
    {
        $container->draw($imagick, 0, 0, 1500, 1000);

        $position = strrpos($filename, '\\');
        $filename = substr($filename, $position + 1);

        $imagick->setImageFormat('png');
        $imagick->writeImage(__DIR__.'/temp/'.$filename);

        if (! file_exists(__DIR__.'/__snapshots__/'.$filename)) {
            if (getenv('SAVE_SNAPSHOT') !== false) {
                $imagick->writeImage(__DIR__.'/__snapshots__/'.$filename);
                $this->fail('Snapshot file not found, adding it.');
            }
            $this->fail('Snapshot file not found.');
        }

        $this->assertImageEquals(
            __DIR__.'/__snapshots__/'.$filename,
            __DIR__.'/temp/'.$filename,
            0.05
        );
    }
}
