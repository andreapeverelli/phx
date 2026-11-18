<?php

/*
 * Copyright (c) 2026 Andrea Peverelli
 * https://github.com/andreapeverelli/phx.git
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

declare(strict_types=1);

namespace AndreaPeverelli\Phx;

final class Render
{
    public function __construct(
        public string $html = "",
        public array $css = [],
        public array $colors = [],
        public array $typos = [],
        public array $icons = [],
        public array $js_before = [],
        public array $js_after = [],
    ) {}
}
