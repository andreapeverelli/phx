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

final class Icon
{
    public function __construct(
        public IconVariant $variant,
        public int $size,
        public int $weight,
        public string $class = "",
    ) {}

    public function __toString(): string
    {
        return serialize($this);
    }
}
