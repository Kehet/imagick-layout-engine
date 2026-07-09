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

namespace Kehet\ImagickLayoutEngine\Tests\Templates;

use Kehet\ImagickLayoutEngine\Containers\ColumnContainer;
use Kehet\ImagickLayoutEngine\Containers\GridContainer;
use Kehet\ImagickLayoutEngine\Containers\RowContainer;
use Kehet\ImagickLayoutEngine\Containers\StackContainer;
use Kehet\ImagickLayoutEngine\Enums\Gravity;
use Kehet\ImagickLayoutEngine\Enums\ImageMode;
use Kehet\ImagickLayoutEngine\Items\DrawableInterface;
use Kehet\ImagickLayoutEngine\Items\Image;
use Kehet\ImagickLayoutEngine\Items\Rectangle;
use Kehet\ImagickLayoutEngine\Items\Text;
use Kehet\ImagickLayoutEngine\Items\TextWrap;
use Kehet\ImagickLayoutEngine\Templates\Exceptions\InvalidTemplateException;
use Kehet\ImagickLayoutEngine\Templates\Template;
use Kehet\ImagickLayoutEngine\Templates\TemplateSettings;
use Kehet\ImagickLayoutEngine\Tests\TestCase;

class TemplateTest extends TestCase
{
    private function assertTemplateRendersLike(DrawableInterface $expected, array $node, array $data, string $name): void
    {
        $expectedImagick = $this->createImage();
        $expected->draw($expectedImagick, 0, 0, 1500, 1000);
        $expectedImagick->setImageFormat('png');
        $expectedImagick->writeImage(__DIR__.'/../temp/'.$name.'_expected.png');

        $actualImagick = $this->createImage();
        Template::fromArray($node)->toDrawable($data)->draw($actualImagick, 0, 0, 1500, 1000);
        $actualImagick->setImageFormat('png');
        $actualImagick->writeImage(__DIR__.'/../temp/'.$name.'_actual.png');

        $this->assertImageEquals(
            __DIR__.'/../temp/'.$name.'_expected.png',
            __DIR__.'/../temp/'.$name.'_actual.png',
            0.0
        );
    }

    public function test_rectangle_from_array(): void
    {
        $this->assertTemplateRendersLike(
            new Rectangle($this->draw('#dc2626')),
            ['type' => 'rectangle', 'fill' => '#dc2626'],
            [],
            'rectangle_from_array'
        );
    }

    public function test_rectangle_from_json(): void
    {
        $imagick = $this->createImage();
        $expected = new Rectangle($this->draw('#dc2626'));
        $expected->draw($imagick, 0, 0, 1500, 1000);
        $imagick->setImageFormat('png');
        $imagick->writeImage(__DIR__.'/../temp/rectangle_from_json_expected.png');

        $actual = $this->createImage();
        Template::fromJson('{"type": "rectangle", "fill": "#dc2626"}')->toDrawable()->draw($actual, 0, 0, 1500, 1000);
        $actual->setImageFormat('png');
        $actual->writeImage(__DIR__.'/../temp/rectangle_from_json_actual.png');

        $this->assertImageEquals(
            __DIR__.'/../temp/rectangle_from_json_expected.png',
            __DIR__.'/../temp/rectangle_from_json_actual.png',
            0.0
        );
    }

    public function test_rectangle_from_file(): void
    {
        $imagick = $this->createImage();
        $expected = new Rectangle($this->draw('#16a34a'));
        $expected->draw($imagick, 0, 0, 1500, 1000);
        $imagick->setImageFormat('png');
        $imagick->writeImage(__DIR__.'/../temp/rectangle_from_file_expected.png');

        $actual = $this->createImage();
        Template::fromFile(__DIR__.'/../assets/templates/valid-rectangle.json')->toDrawable()->draw($actual, 0, 0, 1500, 1000);
        $actual->setImageFormat('png');
        $actual->writeImage(__DIR__.'/../temp/rectangle_from_file_actual.png');

        $this->assertImageEquals(
            __DIR__.'/../temp/rectangle_from_file_expected.png',
            __DIR__.'/../temp/rectangle_from_file_actual.png',
            0.0
        );
    }

    public function test_missing_type_key_throws(): void
    {
        $this->expectException(InvalidTemplateException::class);
        $this->expectExceptionMessage('Template node is missing a "type" string.');

        Template::fromArray(['fill' => '#000'])->toDrawable();
    }

    public function test_unknown_type_throws(): void
    {
        $this->expectException(InvalidTemplateException::class);
        $this->expectExceptionMessage('Unknown template node type "circle".');

        Template::fromArray(['type' => 'circle'])->toDrawable();
    }

    public function test_malformed_json_throws(): void
    {
        $this->expectException(InvalidTemplateException::class);

        Template::fromJson('{not valid json');
    }

    public function test_missing_file_throws(): void
    {
        $this->expectException(InvalidTemplateException::class);
        $this->expectExceptionMessage('Template file "/nonexistent/path.json" does not exist.');

        Template::fromFile('/nonexistent/path.json');
    }

    public function test_row_container_with_gap_and_forced_size(): void
    {
        $expected = new RowContainer;
        $expected->setGap(10);
        $expected->addItem(new Rectangle($this->draw('#fee2e2')));
        $expected->addItem(new Rectangle($this->draw('#fca5a5')), 200);
        $expected->addItem(new Rectangle($this->draw('#dc2626')));

        $this->assertTemplateRendersLike(
            $expected,
            [
                'type' => 'row',
                'gap' => 10,
                'children' => [
                    ['type' => 'rectangle', 'fill' => '#fee2e2'],
                    ['type' => 'rectangle', 'fill' => '#fca5a5', 'size' => 200],
                    ['type' => 'rectangle', 'fill' => '#dc2626'],
                ],
            ],
            [],
            'row_container_with_gap_and_forced_size'
        );
    }

    public function test_column_container_with_gap_and_forced_size(): void
    {
        $expected = new ColumnContainer;
        $expected->setGap(10);
        $expected->addItem(new Rectangle($this->draw('#fee2e2')));
        $expected->addItem(new Rectangle($this->draw('#fca5a5')), 200);
        $expected->addItem(new Rectangle($this->draw('#dc2626')));

        $this->assertTemplateRendersLike(
            $expected,
            [
                'type' => 'column',
                'gap' => 10,
                'children' => [
                    ['type' => 'rectangle', 'fill' => '#fee2e2'],
                    ['type' => 'rectangle', 'fill' => '#fca5a5', 'size' => 200],
                    ['type' => 'rectangle', 'fill' => '#dc2626'],
                ],
            ],
            [],
            'column_container_with_gap_and_forced_size'
        );
    }

    public function test_nested_row_in_column(): void
    {
        $expectedInnerRow = new RowContainer;
        $expectedInnerRow->addItem(new Rectangle($this->draw('#0891b2')));
        $expectedInnerRow->addItem(new Rectangle($this->draw('#164e63')));

        $expected = new ColumnContainer;
        $expected->addItem(new Rectangle($this->draw('#fee2e2')));
        $expected->addItem($expectedInnerRow);

        $this->assertTemplateRendersLike(
            $expected,
            [
                'type' => 'column',
                'children' => [
                    ['type' => 'rectangle', 'fill' => '#fee2e2'],
                    [
                        'type' => 'row',
                        'children' => [
                            ['type' => 'rectangle', 'fill' => '#0891b2'],
                            ['type' => 'rectangle', 'fill' => '#164e63'],
                        ],
                    ],
                ],
            ],
            [],
            'nested_row_in_column'
        );
    }

    public function test_grid_container(): void
    {
        $expected = new GridContainer;
        $expected->setTemplateColumns(200, null, null);
        $expected->setTemplateRows(null, null);
        $expected->setGap(20, 10);
        $expected->addItem(new Rectangle($this->draw('#ecfccb')), columnSpan: 2);
        $expected->addItem(new Rectangle($this->draw('#bef264')));
        $expected->addItem(new Rectangle($this->draw('#65a30d')), column: 0, row: 1, rowSpan: 2);
        $expected->addItem(new Rectangle($this->draw('#1a2e05')));

        $this->assertTemplateRendersLike(
            $expected,
            [
                'type' => 'grid',
                'templateColumns' => [200, null, null],
                'templateRows' => [null, null],
                'gap' => [20, 10],
                'children' => [
                    ['type' => 'rectangle', 'fill' => '#ecfccb', 'columnSpan' => 2],
                    ['type' => 'rectangle', 'fill' => '#bef264'],
                    ['type' => 'rectangle', 'fill' => '#65a30d', 'column' => 0, 'row' => 1, 'rowSpan' => 2],
                    ['type' => 'rectangle', 'fill' => '#1a2e05'],
                ],
            ],
            [],
            'grid_container'
        );
    }

    public function test_grid_container_scalar_gap(): void
    {
        $expected = new GridContainer;
        $expected->setTemplateColumns(null, null);
        $expected->setGap(15);
        $expected->addItem(new Rectangle($this->draw('#cffafe')));
        $expected->addItem(new Rectangle($this->draw('#67e8f9')));

        $this->assertTemplateRendersLike(
            $expected,
            [
                'type' => 'grid',
                'templateColumns' => [null, null],
                'gap' => 15,
                'children' => [
                    ['type' => 'rectangle', 'fill' => '#cffafe'],
                    ['type' => 'rectangle', 'fill' => '#67e8f9'],
                ],
            ],
            [],
            'grid_container_scalar_gap'
        );
    }

    public function test_stack_container(): void
    {
        $expected = new StackContainer;
        $expected->addItem(new Rectangle($this->draw('#fee2e2')));
        $expected->addItem(new Rectangle($this->draw('#dc2626')));

        $this->assertTemplateRendersLike(
            $expected,
            [
                'type' => 'stack',
                'children' => [
                    ['type' => 'rectangle', 'fill' => '#fee2e2'],
                    ['type' => 'rectangle', 'fill' => '#dc2626'],
                ],
            ],
            [],
            'stack_container'
        );
    }

    public function test_margin_scalar(): void
    {
        $expected = new RowContainer;
        $expected->addItem((new Rectangle($this->draw('#fee2e2')))->setMargin(40));

        $this->assertTemplateRendersLike(
            $expected,
            [
                'type' => 'row',
                'children' => [
                    ['type' => 'rectangle', 'fill' => '#fee2e2', 'margin' => 40],
                ],
            ],
            [],
            'margin_scalar'
        );
    }

    public function test_margin_per_side_object(): void
    {
        $expected = new RowContainer;
        $expected->addItem((new Rectangle($this->draw('#fee2e2')))->setMarginTop(10)->setMarginRight(20));

        $this->assertTemplateRendersLike(
            $expected,
            [
                'type' => 'row',
                'children' => [
                    ['type' => 'rectangle', 'fill' => '#fee2e2', 'margin' => ['top' => 10, 'right' => 20]],
                ],
            ],
            [],
            'margin_per_side_object'
        );
    }

    public function test_padding_scalar(): void
    {
        $expected = new RowContainer;
        $expected->addItem((new Rectangle($this->draw('#fee2e2')))->setPadding(40));

        $this->assertTemplateRendersLike(
            $expected,
            [
                'type' => 'row',
                'children' => [
                    ['type' => 'rectangle', 'fill' => '#fee2e2', 'padding' => 40],
                ],
            ],
            [],
            'padding_scalar'
        );
    }

    public function test_padding_per_side_object(): void
    {
        $expected = new RowContainer;
        $expected->addItem((new Rectangle($this->draw('#fee2e2')))->setPaddingBottom(15)->setPaddingLeft(25));

        $this->assertTemplateRendersLike(
            $expected,
            [
                'type' => 'row',
                'children' => [
                    ['type' => 'rectangle', 'fill' => '#fee2e2', 'padding' => ['bottom' => 15, 'left' => 25]],
                ],
            ],
            [],
            'padding_per_side_object'
        );
    }

    public function test_invalid_margin_type_throws(): void
    {
        $this->expectException(InvalidTemplateException::class);
        $this->expectExceptionMessage('"margin" must be a number or an object.');

        Template::fromArray(['type' => 'rectangle', 'fill' => '#000', 'margin' => 'lots'])->toDrawable();
    }

    public function test_border_single_spec_all_sides(): void
    {
        $expected = new RowContainer;
        $expected->addItem((new Rectangle($this->draw('#fee2e2')))->setBorder(\draw(stroke: '#000000', strokeWidth: 4, font: null)));

        $this->assertTemplateRendersLike(
            $expected,
            [
                'type' => 'row',
                'children' => [
                    [
                        'type' => 'rectangle',
                        'fill' => '#fee2e2',
                        'border' => ['stroke' => '#000000', 'strokeWidth' => 4],
                    ],
                ],
            ],
            [],
            'border_single_spec_all_sides'
        );
    }

    public function test_border_per_side_object(): void
    {
        $expected = new RowContainer;
        $expected->addItem(
            (new Rectangle($this->draw('#fee2e2')))
                ->setBorderTop(\draw(stroke: '#000000', strokeWidth: 2, font: null))
                ->setBorderLeft(\draw(stroke: '#111111', strokeWidth: 6, font: null))
        );

        $this->assertTemplateRendersLike(
            $expected,
            [
                'type' => 'row',
                'children' => [
                    [
                        'type' => 'rectangle',
                        'fill' => '#fee2e2',
                        'border' => [
                            'top' => ['stroke' => '#000000', 'strokeWidth' => 2],
                            'left' => ['stroke' => '#111111', 'strokeWidth' => 6],
                        ],
                    ],
                ],
            ],
            [],
            'border_per_side_object'
        );
    }

    public function test_invalid_border_type_throws(): void
    {
        $this->expectException(InvalidTemplateException::class);
        $this->expectExceptionMessage('"border" must be an object.');

        Template::fromArray(['type' => 'rectangle', 'fill' => '#000', 'border' => 'lots'])->toDrawable();
    }

    public function test_text_basic(): void
    {
        $expected = new RowContainer;
        $expected->addItem(new Text($this->draw('#000000'), 'Hello', 48, 12, Gravity::CENTER));

        $this->assertTemplateRendersLike(
            $expected,
            [
                'type' => 'row',
                'children' => [
                    [
                        'type' => 'text',
                        'text' => 'Hello',
                        'fill' => '#000000',
                        'fontSize' => 48,
                        'minFontSize' => 12,
                        'gravity' => 'center',
                    ],
                ],
            ],
            [],
            'text_basic'
        );
    }

    public function test_text_letter_and_word_spacing(): void
    {
        $expected = new RowContainer;
        $expectedText = new Text($this->draw('#000000'), 'Hello world');
        $expectedText->setLetterSpacing(2.5)->setWordSpacing(6);
        $expected->addItem($expectedText);

        $this->assertTemplateRendersLike(
            $expected,
            [
                'type' => 'row',
                'children' => [
                    [
                        'type' => 'text',
                        'text' => 'Hello world',
                        'fill' => '#000000',
                        'letterSpacing' => 2.5,
                        'wordSpacing' => 6,
                    ],
                ],
            ],
            [],
            'text_letter_and_word_spacing'
        );
    }

    public function test_text_wrap_with_line_spacing(): void
    {
        $expected = new RowContainer;
        $expectedText = new TextWrap($this->draw('#000000'), self::SHORT_TEXT);
        $expectedText->setLineSpacing(12);
        $expected->addItem($expectedText);

        $this->assertTemplateRendersLike(
            $expected,
            [
                'type' => 'row',
                'children' => [
                    [
                        'type' => 'text-wrap',
                        'text' => self::SHORT_TEXT,
                        'fill' => '#000000',
                        'lineSpacing' => 12,
                    ],
                ],
            ],
            [],
            'text_wrap_with_line_spacing'
        );
    }

    public function test_text_placeholder_substitution_present_key(): void
    {
        $expected = new RowContainer;
        $expected->addItem(new Text($this->draw('#000000'), 'Hello World'));

        $this->assertTemplateRendersLike(
            $expected,
            [
                'type' => 'row',
                'children' => [
                    ['type' => 'text', 'text' => 'Hello {{name}}', 'fill' => '#000000'],
                ],
            ],
            ['name' => 'World'],
            'text_placeholder_present_key'
        );
    }

    public function test_text_placeholder_substitution_missing_key_left_literal(): void
    {
        $expected = new RowContainer;
        $expected->addItem(new Text($this->draw('#000000'), 'Hello {{name}}'));

        $this->assertTemplateRendersLike(
            $expected,
            [
                'type' => 'row',
                'children' => [
                    ['type' => 'text', 'text' => 'Hello {{name}}', 'fill' => '#000000'],
                ],
            ],
            [],
            'text_placeholder_missing_key'
        );
    }

    public function test_text_missing_field_throws(): void
    {
        $this->expectException(InvalidTemplateException::class);
        $this->expectExceptionMessage('Template node of type "text" requires a "text" string.');

        Template::fromArray(['type' => 'text', 'fill' => '#000'])->toDrawable();
    }

    public function test_text_invalid_gravity_throws(): void
    {
        $this->expectException(InvalidTemplateException::class);
        $this->expectExceptionMessage('Invalid "gravity" value "diagonal".');

        Template::fromArray(['type' => 'text', 'text' => 'Hi', 'gravity' => 'diagonal'])->toDrawable();
    }

    public function test_image_basic(): void
    {
        $expected = new RowContainer;
        $expected->addItem(new Image(self::SMALL_IMAGE, ImageMode::FIT, Gravity::TOP_LEFT));

        $this->assertTemplateRendersLike(
            $expected,
            [
                'type' => 'row',
                'children' => [
                    [
                        'type' => 'image',
                        'file' => self::SMALL_IMAGE,
                        'mode' => 'fit',
                        'gravity' => 'top-left',
                    ],
                ],
            ],
            [],
            'image_basic'
        );
    }

    public function test_image_placeholder_substitution(): void
    {
        $expected = new RowContainer;
        $expected->addItem(new Image(self::SMALL_IMAGE, ImageMode::FILL, Gravity::CENTER));

        $this->assertTemplateRendersLike(
            $expected,
            [
                'type' => 'row',
                'children' => [
                    [
                        'type' => 'image',
                        'file' => '{{path}}',
                        'mode' => 'fill',
                    ],
                ],
            ],
            ['path' => self::SMALL_IMAGE],
            'image_placeholder_substitution'
        );
    }

    public function test_image_missing_field_throws(): void
    {
        $this->expectException(InvalidTemplateException::class);
        $this->expectExceptionMessage('Template node of type "image" requires a "file" string.');

        Template::fromArray(['type' => 'image'])->toDrawable();
    }

    public function test_image_invalid_mode_throws(): void
    {
        $this->expectException(InvalidTemplateException::class);
        $this->expectExceptionMessage('Invalid "mode" value "stretch".');

        Template::fromArray(['type' => 'image', 'file' => self::SMALL_IMAGE, 'mode' => 'stretch'])->toDrawable();
    }

    public function test_image_rejects_uri_scheme_file_value(): void
    {
        $this->expectException(InvalidTemplateException::class);
        $this->expectExceptionMessage('Invalid "file" value "https://example.com/x.png": ImageMagick coder/URI prefixes are not allowed.');

        Template::fromArray(['type' => 'image', 'file' => 'https://example.com/x.png'])->toDrawable();
    }

    public function test_image_rejects_uri_scheme_from_placeholder_substitution(): void
    {
        $this->expectException(InvalidTemplateException::class);
        $this->expectExceptionMessage('Invalid "file" value "msl:evil.msl": ImageMagick coder/URI prefixes are not allowed.');

        Template::fromArray(['type' => 'image', 'file' => '{{path}}'])->toDrawable(['path' => 'msl:evil.msl']);
    }

    public function test_image_rejects_pipe_prefixed_file_value(): void
    {
        $this->expectException(InvalidTemplateException::class);
        $this->expectExceptionMessage('Invalid "file" value "|id > /tmp/pwned": pipe ("|") prefixed paths are not allowed.');

        Template::fromArray(['type' => 'image', 'file' => '|id > /tmp/pwned'])->toDrawable();
    }

    public function test_image_rejects_pipe_prefix_from_placeholder_substitution(): void
    {
        $this->expectException(InvalidTemplateException::class);
        $this->expectExceptionMessage('Invalid "file" value "|id > /tmp/pwned": pipe ("|") prefixed paths are not allowed.');

        Template::fromArray(['type' => 'image', 'file' => '{{path}}'])->toDrawable(['path' => '|id > /tmp/pwned']);
    }

    public function test_image_rejects_path_traversal_outside_base_dir(): void
    {
        $this->expectException(InvalidTemplateException::class);
        $this->expectExceptionMessage('resolves outside the allowed image directory');

        Template::fromArray(['type' => 'image', 'file' => '{{path}}'])
            ->withSettings(new TemplateSettings(imageBaseDir: __DIR__.'/../assets/templates'))
            ->toDrawable(['path' => '../example-image-small.jpeg']);
    }

    public function test_image_rejects_absolute_path_outside_base_dir(): void
    {
        $this->expectException(InvalidTemplateException::class);
        $this->expectExceptionMessage('resolves outside the allowed image directory');

        Template::fromArray(['type' => 'image', 'file' => '{{path}}'])
            ->withSettings(new TemplateSettings(imageBaseDir: __DIR__.'/../assets/templates'))
            ->toDrawable(['path' => self::SMALL_IMAGE]);
    }

    public function test_image_allows_path_inside_base_dir(): void
    {
        $expected = new RowContainer;
        $expected->addItem(new Image(self::SMALL_IMAGE, ImageMode::FIT, Gravity::TOP_LEFT));

        $expectedImagick = $this->createImage();
        $expected->draw($expectedImagick, 0, 0, 1500, 1000);
        $expectedImagick->setImageFormat('png');
        $expectedImagick->writeImage(__DIR__.'/../temp/image_inside_base_dir_expected.png');

        $actualImagick = $this->createImage();
        Template::fromArray([
            'type' => 'row',
            'children' => [
                ['type' => 'image', 'file' => '{{path}}', 'mode' => 'fit', 'gravity' => 'top-left'],
            ],
        ])
            ->withSettings(new TemplateSettings(imageBaseDir: __DIR__.'/../assets'))
            ->toDrawable(['path' => 'example-image-small.jpeg'])
            ->draw($actualImagick, 0, 0, 1500, 1000);
        $actualImagick->setImageFormat('png');
        $actualImagick->writeImage(__DIR__.'/../temp/image_inside_base_dir_actual.png');

        $this->assertImageEquals(
            __DIR__.'/../temp/image_inside_base_dir_expected.png',
            __DIR__.'/../temp/image_inside_base_dir_actual.png',
            0.0
        );
    }

    public function test_readme_row_of_rectangles_matches_hand_built(): void
    {
        $expected = new RowContainer;
        $expected->addItem(new Rectangle($this->draw('#fee2e2')));
        $expected->addItem(new Rectangle($this->draw('#fca5a5')));
        $expected->addItem(new Rectangle($this->draw('#dc2626')));
        $expected->addItem(new Rectangle($this->draw('#450a0a')));

        $this->assertTemplateRendersLike(
            $expected,
            [
                'type' => 'row',
                'children' => [
                    ['type' => 'rectangle', 'fill' => '#fee2e2'],
                    ['type' => 'rectangle', 'fill' => '#fca5a5'],
                    ['type' => 'rectangle', 'fill' => '#dc2626'],
                    ['type' => 'rectangle', 'fill' => '#450a0a'],
                ],
            ],
            [],
            'readme_row_of_rectangles'
        );
    }

    public function test_template_composes_into_hand_built_container(): void
    {
        $templateItem = Template::fromArray([
            'type' => 'rectangle',
            'fill' => '#dc2626',
        ])->toDrawable();

        $expected = new RowContainer;
        $expected->addItem(new Rectangle($this->draw('#fee2e2')));
        $expected->addItem(new Rectangle($this->draw('#dc2626')));

        $actual = new RowContainer;
        $actual->addItem(new Rectangle($this->draw('#fee2e2')));
        $actual->addItem($templateItem);

        $expectedImagick = $this->createImage();
        $expected->draw($expectedImagick, 0, 0, 1500, 1000);
        $expectedImagick->setImageFormat('png');
        $expectedImagick->writeImage(__DIR__.'/../temp/template_composition_expected.png');

        $actualImagick = $this->createImage();
        $actual->draw($actualImagick, 0, 0, 1500, 1000);
        $actualImagick->setImageFormat('png');
        $actualImagick->writeImage(__DIR__.'/../temp/template_composition_actual.png');

        $this->assertImageEquals(
            __DIR__.'/../temp/template_composition_expected.png',
            __DIR__.'/../temp/template_composition_actual.png',
            0.0
        );
    }
}
