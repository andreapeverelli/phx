<?php

/*
 * Copyright (c) 2026 Andrea Peverelli
 * https://github.com/andreapeverelli/phx.git
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

declare(strict_types=1);

namespace AndreaPeverelli\Phx;

use voku\helper\HtmlMin;
use MatthiasMullie\Minify\JS;
use MatthiasMullie\Minify\CSS;
use AndreaPeverelli\Phx\Components\IconVariant;

final class App
{
    private RelationalDatabase $relational_database;
    private InMemoryDatabase $in_memory_database;

    public string $language = "en";
    public string $meta = "";
    public string $head = "";
    public string $body = "";

    private array $css = [];
    private array $js_after = [];

    private array $colors = [];
    private array $typos = [];
    private array $icons = [];

    private array $settings = [];

    public Render $component;

    public function __construct()
    {
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $this->relational_database = new RelationalDatabase();
        $this->in_memory_database = new InMemoryDatabase();

        $this->component = new Render();
    }

    public function authenticate(): void
    {
        Auth::authenticate(connection: $this->relational_database->connection);
    }

    public function login(
        string $username,
        string $password,
        string $form_id,
        string $csfr_token,
    ): bool
    {
        return Auth::login(
            connection: $this->database->connection,
            username: $username,
            password: $password,
            form_id: $form_id,
            csfr_token: $csfr_token,
        );
    }

    public static function generateFormCsfr(): array
    {
        $form_id = bin2hex(random_bytes(32));
        $csfr_token = bin2hex(random_bytes(32));
        $html_csfr_token = htmlspecialchars($csfr_token, ENT_QUOTES, "UTF-8");

        $_SESSION["csfr_token_form_$form_id"] = $csfr_token;

        return [
            "form_id" => $form_id,
            "html_csfr_token" => $html_csfr_token,
        ];
    }

    public function loadSettings(string $path): void
    {
        $this->settings = [
            "palette" => json_decode(file_get_contents("$path/palette.json"), true),
            "typescale" => json_decode(file_get_contents("$path/typescale.json"), true),
            "motion" => json_decode(file_get_contents("$path/motion.json"), true),
        ];
    }

    public function page(string $page_class, bool $static = true): string
    {
        if($static) {
            $hash = hash("xxh128", $page_class);

            $page = $this->in_memory_database->get(key: $hash);

            if($page !== null) {
                return $page;
            }
        }

        $page = (new HtmlMin())->minify((new $page_class())->render(app: $this));

        if($static) $this->in_memory_database->setnx(key: $hash, value:$page);

        return $page;
    }

    public function component(string $component_class, array $props, bool $static = true): string
    {
        if($static) {
            $hash = hash("xxh128", $component_class . serialize($props));

            $component = $this->in_memory_database->get(key: $hash);

            if($component !== null && ($component = unserialize($component))) {
                $this->registerComponentData(component: $component);

                return $component->html;
            }
        }

        $this->free();

        (new $component_class())->render(...$props, app: $this);
        $this->registerComponentData();

        if($static) $this->in_memory_database->setnx(key: $hash, value: serialize($this->component));

        return $this->component->html;
    }

    private function free(): void
    {
        $this->component = new Render();
    }

    private function registerComponentData(): void
    {
        $this->css = array_values(array_unique(array_merge($this->css, $this->component->css)));
        $this->js_after = array_values(array_unique(array_merge($this->js_after, $this->component->js_after)));

        $this->colors = array_values(array_unique(array_merge($this->colors, $this->component->colors)));
        $this->typos = array_values(array_unique(array_merge($this->typos, $this->component->typos)));
        $this->icons = array_values(array_unique(array_merge($this->icons, $this->component->icons)));
    }

    public function css(): string
    {
        if(count($this->css) || count($this->colors) || count($this->typos)) {
            $hash = hash("xxh128", serialize(array_merge($this->css, $this->colors, $this->typos)));

            $link = $this->in_memory_database->get($hash);

            if($link === null) {
                $this->buildColors();
                $this->buildTypos();

                file_put_contents(getcwd() . "/css/$hash.css" , (new CSS(implode("\n\n", $this->css)))->minify());
                $link = "<link rel=\"stylesheet\" href=\"/css/$hash.css\" />";

                $this->in_memory_database->setnx($hash, $link);

                return $link;
            }

            return $link;
        } else return "";
    }

    public function jsAfter(): string
    {
        $js_after = [];
        foreach($this->js_after as $js) {
            $names = [];
            preg_match_all("/\{\{(.*?)\}\}/", $js, $names);

            foreach(array_unique($names[1]) as $name) {
                $js = str_replace("{{" . $name . "}}", "phx_" . uniqid(), $js);
            }

            array_push($js_after, $js);
        }

        $_js = implode("\n\n", $js_after);

        if($_js !== "") {
            $_js = (new JS($_js))->minify();
            return <<<HTML
            <script>
                $_js
            </script>
            HTML;
        } else return "";
    }

    public function color(Color $color): string
    {
        $color->class = "phx_" . uniqid();
        array_push($this->component->colors, $color);

        return $color->class;
    }

    public function typo(Typo $typo): string
    {
        $typo->class = "phx_" . uniqid();
        array_push($this->component->typos, $typo);

        return $typo->class;
    }

    public function icon(Icon $icon): string
    {
        $icon->class = $this->getIconClass(icon: $icon->variant);
        array_push($this->component->icons, $icon);

        return $icon->class;
    }

    private function getIconClass(IconVariant $icon): string
    {
        return implode("-", explode(" ", strtolower($this->settings["typescale"]["icons-font-name"]))) . "-" . implode("-", explode("_", $icon->value));
    }

    function getMotion(string $motion): string
    {
        return $this->settings["motion"][$motion];
    }

    public function render(): string
    {
        $css = $this->css();
        $js_after = $this->jsAfter();

        $html = <<<HTML
        <!doctype html>
        <html lang="{$this->language}">
        <head>
            {$this->meta}
            {$this->head}
            $css
        </head>
        <body>
            {$this->body}
            $js_after
        </body>
        </html>
        HTML;

        $this->purge();

        return $html;
    }

    private function purge(): void
    {
        $this->language = "en";
        $this->meta = "";
        $this->head = "";
        $this->body = "";
        $this->css = [];
        $this->js_after = [];
        $this->colors = [];
        $this->typos = [];
        $this->icons = [];
    }

    private function buildColors(): void
    {
        foreach($this->colors as $color) {
            /* TAKING THE COLOR VALUES FROM THE SETTINGS */
            /* A color state is prefixed by "state-", followed the background role, "--" and foreground role, "--" and opacity */
            if(str_starts_with($color->role, "state-")) {
                $color_role = substr($color->role, 5);
                $color_role = explode("--", $color_role);
                $opacity = array_search((float) $color_role[3], [0.08, 0.1, 0.16, 0.38]);

                $light_default_values = $this->settings["palette"]["states"][$color_role[1]][$color_role[2]]["light"]["default"][$opacity];
                $light_high_values = $this->settings["palette"]["states"][$color_role[1]][$color_role[2]]["light"]["high"][$opacity];
                $dark_default_values = $this->settings["palette"]["states"][$color_role[1]][$color_role[2]]["dark"]["default"][$opacity];
                $dark_high_values = $this->settings["palette"]["states"][$color_role[1]][$color_role[2]]["dark"]["high"][$opacity];

            } else {
                if(array_key_exists($color->role, ColorRoles::$roles["special"])) {
                    $base_color = ColorRoles::$roles["special"][$color->role]["base-color"];

                    $light_default_tone = ColorRoles::$roles["special"][$color->role]["palette"]["light"]["default-contrast"];
                    $light_high_tone = ColorRoles::$roles["special"][$color->role]["palette"]["light"]["high-contrast"];
                    $dark_default_tone = ColorRoles::$roles["special"][$color->role]["palette"]["dark"]["default-contrast"];
                    $dark_high_tone = ColorRoles::$roles["special"][$color->role]["palette"]["dark"]["high-contrast"];

                    $light_default_values = $this->settings["palette"][$base_color][$light_default_tone];
                    $light_high_values = $this->settings["palette"][$base_color][$light_high_tone];
                    $dark_default_values = $this->settings["palette"][$base_color][$dark_default_tone];
                    $dark_high_values = $this->settings["palette"][$base_color][$dark_high_tone];
                } else {
                    $base_color = array_diff(explode("-", $color->role), ["on", "container", "fixed", "dim", "variant"])[0];
                    $role = str_replace($base_color, "role", $color->role);

                    $light_default_tone = ColorRoles::$roles["standard"][$role]["palette"]["light"]["default-contrast"];
                    $light_high_tone = ColorRoles::$roles["standard"][$role]["palette"]["light"]["high-contrast"];
                    $dark_default_tone = ColorRoles::$roles["standard"][$role]["palette"]["dark"]["default-contrast"];
                    $dark_high_tone = ColorRoles::$roles["standard"][$role]["palette"]["dark"]["high-contrast"];

                    $light_default_values = $this->settings["palette"][$base_color][$light_default_tone];
                    $light_high_values = $this->settings["palette"][$base_color][$light_high_tone];
                    $dark_default_values = $this->settings["palette"][$base_color][$dark_default_tone];
                    $dark_high_values = $this->settings["palette"][$base_color][$dark_high_tone];
                }
            }

            /* BUILDING THE CSS VALUES */
            $light_default_srgb = $light_default_values["srgb"];
            $light_default_display_p3 = "{$light_default_values["display-p3"]["r"]} {$light_default_values["display-p3"]["g"]} {$light_default_values["display-p3"]["b"]}";
            $light_default_rec2020 = "{$light_default_values["rec2020"]["r"]} {$light_default_values["rec2020"]["g"]} {$light_default_values["rec2020"]["b"]}";

            $light_high_srgb = $light_high_values["srgb"];
            $light_high_display_p3 = "{$light_high_values["display-p3"]["r"]} {$light_high_values["display-p3"]["g"]} {$light_high_values["display-p3"]["b"]}";
            $light_high_rec2020 = "{$light_high_values["rec2020"]["r"]} {$light_high_values["rec2020"]["g"]} {$light_high_values["rec2020"]["b"]}";

            $dark_default_srgb = $dark_default_values["srgb"];
            $dark_default_display_p3 = "{$dark_default_values["display-p3"]["r"]} {$dark_default_values["display-p3"]["g"]} {$dark_default_values["display-p3"]["b"]}";
            $dark_default_rec2020 = "{$dark_default_values["rec2020"]["r"]} {$dark_default_values["rec2020"]["g"]} {$dark_default_values["rec2020"]["b"]}";

            $dark_high_srgb = $dark_high_values["srgb"];
            $dark_high_display_p3 = "{$dark_high_values["display-p3"]["r"]} {$dark_high_values["display-p3"]["g"]} {$dark_high_values["display-p3"]["b"]}";
            $dark_high_rec2020 = "{$dark_high_values["rec2020"]["r"]} {$dark_high_values["rec2020"]["g"]} {$dark_high_values["rec2020"]["b"]}";

            /* COMPLEX PROPERTIES */
            if($color->mode === "box-shadow") {
                $box_shadow = "{$color->box_shadow_mode} {$color->box_shadow_offset_x} {$color->box_shadow_offset_y} 0 0 ";
            } else $box_shadow = "";

            array_push($this->css, <<<CSS
            .{$color->class} {
                {$color->mode}: $box_shadow$light_default_srgb;
                {$color->mode}: {$box_shadow}color(display-p3 $light_default_display_p3);
                {$color->mode}: {$box_shadow}color(rec2020 $light_default_rec2020);

                @media(prefers-contrast: more) {
                    {$color->mode}: $box_shadow$light_high_srgb;
                    {$color->mode}: {$box_shadow}color(display-p3 $light_high_display_p3);
                    {$color->mode}: {$box_shadow}color(rec2020 $light_high_rec2020);
                }

                @media(prefers-color-scheme: dark) {
                    {$color->mode}: $box_shadow$dark_default_srgb;
                    {$color->mode}: {$box_shadow}color(display-p3 $dark_default_display_p3);
                    {$color->mode}: {$box_shadow}color(rec2020 $dark_default_rec2020);

                    @media(prefers-contrast: more) {
                        {$color->mode}: $box_shadow$dark_high_srgb;
                        {$color->mode}: {$box_shadow}color(display-p3 $dark_high_display_p3);
                        {$color->mode}: {$box_shadow}color(rec2020 $dark_high_rec2020);
                    }
                }
            }
            CSS);
        }
    }

    private function buildTypos(): void
    {
        $typos_css = $this->renderFontFamilies();
        $fonts = $this->buildFontsToQuery();
        $fonts_css = $this->getGoogleFonts(fonts: $fonts);

        array_push($this->css, ...$fonts_css, ...$typos_css);
    }

    private function renderFontFamilies(): array
    {
        $typos_css = [];

        foreach($this->typos as $typo) {
            $font_family = in_array($typo->role, ["display", "headline", "title"]) ?
                $this->settings["typescale"]["main-font-name"] :
                $this->settings["typescale"]["support-font-name"];
            $font_size = $this->settings["typescale"][$typo->role][$typo->sub_role]["font-size"];
            $line_height = $this->settings["typescale"][$typo->role][$typo->sub_role]["line-height"];
            $font_weight = $typo->emphasized ?
                $this->settings["typescale"][$typo->role][$typo->sub_role]["font-weight"]["emphasized"] :
                $this->settings["typescale"][$typo->role][$typo->sub_role]["font-weight"]["regular"];
            $letter_spacing = $this->settings["typescale"][$typo->role][$typo->sub_role]["letter-spacing"];

            array_push($typos_css, <<<CSS
            .{$typo->class} {
                font-family: $font_family;
                font-size: {$font_size}px;
                line-height: {$line_height}px;
                font-weight: $font_weight;
                letter-spacing: {$letter_spacing}px;
            }
            CSS);
        }

        return $typos_css;
    }

    private function buildFontsToQuery(): array
    {
        $fonts = [];

        foreach($this->typos as $typo) {
            $is_italic = $typo->italic ? "italic" : "not-italic";

            $font_family =
                $typo->role === "display" ||
                $typo->role === "headline" ||
                $typo->role === "title" ?
                    $this->settings["typescale"]["main-font-name"] :
                    $this->settings["typescale"]["support-font-name"] ;
            $font_key = "$font_family-$is_italic";

            $font_weight = $typo->emphasized ?
                (int)$this->settings["typescale"][$typo->role][$typo->sub_role]["font-weight"]["emphasized"] :
                (int)$this->settings["typescale"][$typo->role][$typo->sub_role]["font-weight"]["regular"] ;

            if(!array_key_exists($font_key, $fonts)) {
                $fonts[$font_key] = new TypoFont(
                    font_family: $font_family,
                    italic: $typo->italic,
                    min_weight: $font_weight,
                    max_weight: $font_weight,
                );
            } else {
                if($font_weight < $fonts[$font_key]->min_weight) $fonts[$font_key]->min_weight = $font_weight;
                if($font_weight > $fonts[$font_key]->max_weight) $fonts[$font_key]->max_weight = $font_weight;
            }

            if($typo->text) {
                if($fonts[$font_key]->text) {
                    $fonts[$font_key]->text = implode("", array_values(array_unique(array_merge(str_split($typo->text), str_split($fonts[$font_key]->text)))));
                } else $fonts[$font_key]->text = $typo->text;
            }
        }

        foreach($this->icons as $icon) {
            if(!array_key_exists($icon->variant->value, $fonts)) {
                $fonts[$icon->variant->value] = new IconFont(
                    variant: $icon->variant,
                    min_weight: $icon->weight,
                    max_weight: $icon->weight,
                    min_size: $icon->size,
                    max_size: $icon->size,
                );
            } else {
                if($icon->weight < $fonts[$icon->variant->value]->min_weight) $fonts[$icon->variant->value]->min_weight = $icon->weight;
                if($icon->weight > $fonts[$icon->variant->value]->max_weight) $fonts[$icon->variant->value]->max_weight = $icon->weight;

                if($icon->size < $fonts[$icon->variant->value]->min_size) $fonts[$icon->variant->value]->min_size = $icon->size;
                if($icon->size > $fonts[$icon->variant->value]->max_size) $fonts[$icon->variant->value]->max_size = $icon->size;
            }
        }

        return array_values($fonts);
    }

    private function getGoogleFonts(array $fonts): array
    {
        $fonts_css = [];

        foreach($fonts as $font) {
            $url = "https://fonts.googleapis.com/css2?family=";

            if($font instanceof TypoFont) {
                $url .= urlencode($font->font_family);

                if($font->italic) $url .= ":ital,wght@1,";
                else $url .= ":wght@";

                if($font->min_weight === $font->max_weight) {
                    $url .= (string)$font->min_weight;
                } else {
                    $url .= (string)$font->min_weight . ".." . (string)$font->max_weight;
                }

                if($font->text) {
                    $text = urlencode($font->text);
                    $url .= "&text=$text";
                }

                $url .= "&display=swap";
            } else {
                $url .= urlencode($this->settings["typescale"]["icons-font-name"]) . ":opsz,wght@";

                if($font->min_size === $font->max_size) {
                    $url .= (string)$font->min_size . ",";
                } else {
                    $url .= (string)$font->min_size . ".." . (string)$font->max_size . ",";
                }

                if($font->min_weight === $font->max_weight) {
                    $url .= (string)$font->min_weight;
                } else {
                    $url .= (string)$font->min_weight . ".." . (string)$font->max_weight;
                }

                $url .= "&icon_names={$font->variant->value}&display=block";
            }
            
            $http_context = stream_context_create([
                "http" => [
                    "header" => implode("\r\n", [
                        "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36",
                        "Accept: text/css,*/*;q=0.1",
                    ]),
                    "timeout" => 15
                ],
            ]);

            $font_css = file_get_contents($url, false, $http_context);
            if($font instanceof IconFont) {
                $_class_name = implode("-", explode(" ", strtolower($this->settings["typescale"]["icons-font-name"])));
                $class_name = $this->getIconClass(icon: $font->variant);
                $font_css = str_replace($_class_name, $class_name, $font_css);

                $font_family = $this->settings["typescale"]["icons-font-name"] . " " . ucfirst(implode(" ", explode("_", $font->variant->value)));
                $font_css = str_replace($this->settings["typescale"]["icons-font-name"], $font_family, $font_css);
            }

            array_push($fonts_css, $font_css);
        }

        foreach($fonts_css as $index => $font_css) {
            $font_urls = preg_match_all(
                "~url\(\s*['\"]?([^)'\"]+)['\"]?\s*\)~",
                $font_css,
                $matches,
            );

            foreach($matches[1] as $match) {
                $hash = bin2hex(random_bytes(32));
                file_put_contents(getcwd() . "/fonts/$hash.woff2", file_get_contents($match));
                $fonts_css[$index] = str_replace($match, "/fonts/$hash.woff2", $fonts_css[$index]);
            }
        }

        return $fonts_css;
    }
}
