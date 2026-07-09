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

namespace Kehet\ImagickLayoutEngine\Templates;

use Kehet\ImagickLayoutEngine\Items\DrawableInterface;
use Kehet\ImagickLayoutEngine\Templates\Exceptions\InvalidTemplateException;

final class Template
{
    private function __construct(
        private readonly array $node,
        private readonly TemplateSettings $settings = new TemplateSettings,
    ) {}

    public static function fromArray(array $node): self
    {
        return new self($node);
    }

    public function withSettings(TemplateSettings $settings): self
    {
        return new self($this->node, $settings);
    }

    public static function fromJson(string $json): self
    {
        $decoded = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidTemplateException('Invalid template JSON: '.json_last_error_msg());
        }

        if (! is_array($decoded)) {
            throw new InvalidTemplateException('Template JSON must decode to an object.');
        }

        return self::fromArray($decoded);
    }

    public static function fromFile(string $path): self
    {
        if (! is_file($path)) {
            throw new InvalidTemplateException(sprintf('Template file "%s" does not exist.', $path));
        }

        return self::fromJson(file_get_contents($path));
    }

    public function toDrawable(array $data = []): DrawableInterface
    {
        return NodeBuilder::build($this->node, $data, $this->settings);
    }
}
