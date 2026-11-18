<?php

/*
 * Copyright (c) 2026 Andrea Peverelli
 * https://github.com/andreapeverelli/phx.git
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

declare(strict_types=1);

namespace AndreaPeverelli\Phx;

final class TypoFont
{
    public function __construct(
        public string $font_family,
        public int $min_weight,
        public int $max_weight,
        public bool $italic = false,
        public ?string $text = null,
    ) {}
}
