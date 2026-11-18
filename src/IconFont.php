<?php

/*
 * Copyright (c) 2026 Andrea Peverelli
 * https://github.com/andreapeverelli/phx.git
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

declare(strict_types=1);

namespace AndreaPeverelli\Phx;

use AndreaPeverelli\Phx\Components\IconVariant;

final class IconFont
{
    public function __construct(
        public IconVariant $variant,
        public int $min_weight,
        public int $max_weight,
        public int $min_size,
        public int $max_size,
    ) {}
}
