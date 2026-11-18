<?php

/*
 * Copyright (c) 2026 Andrea Peverelli
 * https://github.com/andreapeverelli/phx.git
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

declare(strict_types=1);

namespace AndreaPeverelli\Phx\Components;

use AndreaPeverelli\Phx\App;
use AndreaPeverelli\Phx\Color;
use AndreaPeverelli\Phx\Typo;
use AndreaPeverelli\Phx\Icon;

final class FilledInput
{
    public static function render(
        App $app,
        string $name,
        string $type,
        ?string $placeholder,
        ?string $supporting_text = null,
        ?IconVariant $icon = null,
        ?string $value = null,
        ?float $min = null,
        ?float $max = null,
        ?float $step = null,
        ?bool $required = null,
    ): void
    {
        $value_html = $value ? " value=\"$value\"" : "";
        $min_html = $min ? " min=\"" . (string) round($min, 2) . "\"" : "";
        $max_html = $max ? " max=\"" . (string) round($max, 2) . "\"" : "";
        $step_html = $step ? " step=\"" . (string) round($step, 2) . "\"" : "";
        $required_html = $required !== null ? " required=\"" . json_encode($required) . "\"" : "";

        $cancel_icon = IconVariant::CANCEL->value;

        $leading_icon_class = $app->icon(new Icon(
            variant: $icon,
            size: 24,
            weight: 400,
        ));
        $on_surface_variant_color_class = $app->color(new Color(role: "on-surface-variant", mode: "color"));
        $leading_icon_html = $icon ? <<<HTML
        <div
            class="
                phx-icon
                $leading_icon_class
                $on_surface_variant_color_class
            "
        >{$icon->value}</div>
        HTML : "";

        $trailing_icon_class = $app->icon(new Icon(
            variant: IconVariant::CANCEL,
            size: 24,
            weight: 400,
        ));
        $trailing_icon_html = <<<HTML
        <div
            class="
                phx-icon
                $trailing_icon_class
                $on_surface_variant_color_class
            "
        >$cancel_icon</div>
        HTML;

        $label_text_id = uniqid();
        $primary_color_class = $app->color(new Color(role: "primary", mode: "color"));
        $body_large_class = $app->typo(new Typo(role: "body", sub_role: "large"));
        $body_small_class = $app->typo(new Typo(role: "body", sub_role: "small"));
        $label_text_html = <<<HTML
        <div
            id="$label_text_id"
            class="
                label-text
                $body_large_class
                $on_surface_variant_color_class
            "
        >$placeholder</div>
        HTML;

        $container_id = uniqid();
        $input_id = uniqid();
        $surface_container_highest_background_color_class = $app->color(new Color(role: "surface-container-highest", mode: "background-color"));
        $on_surface_variant_inset_1px_bottom_box_shadow_class = $app->color(new Color(
            role: "on-surface-variant",
            mode: "box-shadow",
            box_shadow_mode: "inset",
            box_shadow_offset_y: "-1px",
        ));
        $primary_inset_3px_bottom_box_shadow_class = $app->color(new Color(
            role: "primary",
            mode: "box-shadow",
            box_shadow_mode: "inset",
            box_shadow_offset_y: "-3px",
        ));
        $primary_caret_color_class = $app->color(new Color(role: "primary", mode: "caret-color"));
        $on_surface_color_class = $app->color(new Color(role: "on-surface", mode: "color"));
        $input_html = <<<HTML
        <div
            id="$container_id"
            class="
                phx-input
                content
                $surface_container_highest_background_color_class
                $on_surface_variant_inset_1px_bottom_box_shadow_class
            "
            tabindex="0"
        >
            $leading_icon_html
            <div class="content">
                $label_text_html
                <input
                    id="$input_id"
                    name="$name"
                    class="
                        $type
                        $body_large_class
                        $on_surface_color_class
                        $surface_container_highest_background_color_class
                        $primary_caret_color_class
                    "
                    type="$type"
                    placeholder="$placeholder"
                    $value_html
                    $min_html
                    $max_html
                    $step_html
                    $required_html
                />
            </div>
            $trailing_icon_html
        </div>
        HTML;

        $input_container_id = uniqid();
        $app->component->html = $supporting_text ? <<<HTML
        <div
            id="$input_container_id"
            class="phx-input"
            tabindex="0"
        >
            $input_html
            <div>$supporting_text</div>
        </div>
        HTML : $input_html;

        $container_padding_left = $icon ? "12px" : "16px";

        $expressive_fast_effects = $app->getMotion("expressive-fast-effects");
        $expressive_fast_spatial = $app->getMotion("expressive-fast-spatial");
        $hover_background = $app->color(new Color(
            role: "state--surface-container-highest--on-surface--0.08",
            mode: "background-color",
        ));
        $app->component->css = [<<<CSS
        div.phx-input, div.phx-input * {
            transition:
                all $expressive_fast_effects,
                font-size $expressive_fast_spatial;
        }

        div.phx-input {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        div.phx-input.content {
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: flex-start;
            text-align: left;
            padding-left: $container_padding_left;
            padding-right: 16px;
            gap: 16px;
            min-height: 56px;
            max-width: 256px;
            border-radius: 4px 4px 0 0;
        }

        div.phx-input.content > :is(div.leading-icon, div.trailing-icon) {
            font-size: 24px;
        }

        div.phx-input.content > div.$trailing_icon_class {
            display: none;
        }

        div.phx-input.content > div.content {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
        }

        div.phx-input.content > div.content > input {
            display: none;
            border: none;
            padding: 0;
            margin: 0;
        }

        div.phx-input.content > div.content > input:focus-visible {
            outline: none;
        }

        div.phx-input.content.focused > div.content > input {
            display: flex;
        }

        div.phx-input.content.focused > div.$trailing_icon_class {
            display: block;
        }
        CSS];

        $app->component->js_after = [<<<JS
        const {{container}} = document.getElementById("$container_id");
        const {{label_text}} = document.getElementById("$label_text_id");
        const {{input}} = document.getElementById("$input_id");

        const {{container_enabled_classes}} = [
            "$surface_container_highest_background_color_class",
            "$on_surface_variant_inset_1px_bottom_box_shadow_class",
        ];
        const {{container_focused_classes}} = [
            "focused",
            "$surface_container_highest_background_color_class",
            "$primary_inset_3px_bottom_box_shadow_class"
        ];
        const {{container_hovered_classes}} = [
            "$hover_background",
            "$on_surface_variant_inset_1px_bottom_box_shadow_class",
        ];

        const {{label_text_enabled_classes}} = [
            "$on_surface_variant_color_class",
            "$body_large_class"
        ];

        const {{label_text_focused_classes}} = [
            "$primary_color_class",
            "$body_small_class"
        ];

        const {{input_enabled_classes}} = ["$surface_container_highest_background_color_class"];
        const {{input_hovered_classes}} = ["$hover_background"];

        document.addEventListener("click", function (event) {
            event.preventDefault();
            if(!{{container}}.contains(event.target)) {{input}}.blur();
        });

        {{container}}.addEventListener("focus", function (event) {
            event.preventDefault();
            {{focused}}();
        });

        {{container}}.addEventListener("mouseenter", function (event) {
            event.preventDefault();
            {{hovered}}();
        });

        {{container}}.addEventListener("mouseleave", function (event) {
            event.preventDefault();
            {{unHovered}}();
        });

        {{input}}.addEventListener("blur", function (event) {
            event.preventDefault();
            {{enabled}}();
        });

        function {{focused}}() {
            {{container}}.classList.remove(...{{container_enabled_classes}});
            {{container}}.classList.add(...{{container_focused_classes}});

            {{label_text}}.classList.remove(...{{label_text_enabled_classes}});
            {{label_text}}.classList.add(...{{label_text_focused_classes}});

            {{input}}.focus();
        }

        function {{enabled}}() {
            if({{input}}.value === "") {
                {{container}}.classList.remove(...{{container_focused_classes}});
                {{container}}.classList.add(...{{container_enabled_classes}});

                {{label_text}}.classList.remove(...{{label_text_focused_classes}});
                {{label_text}}.classList.add(...{{label_text_enabled_classes}});
            }
        }

        function {{hovered}}() {
            {{container}}.classList.remove(...{{container_enabled_classes}});
            {{container}}.classList.add(...{{container_hovered_classes}});

            {{input}}.classList.remove(...{{input_enabled_classes}});
            {{input}}.classList.add(...{{input_hovered_classes}});
        }

        function {{unHovered}}() {
            {{container}}.classList.remove(...{{container_hovered_classes}});
            {{container}}.classList.add(...{{container_enabled_classes}});

            {{input}}.classList.remove(...{{input_hovered_classes}});
            {{input}}.classList.add(...{{input_enabled_classes}});
        }
        JS];
    }
}
