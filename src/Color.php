<?php

/*
 * Copyright (c) 2026 Andrea Peverelli
 * https://github.com/andreapeverelli/phx.git
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

declare(strict_types=1);

namespace AndreaPeverelli\Phx;

final class Color
{
    public function __construct(
        public string $role,
        public string $mode,
        public string $box_shadow_mode = "",
        public string $box_shadow_offset_x = "0",
        public string $box_shadow_offset_y = "0",
        public string $class = "",
    ) {}

    public function __toString(): string
    {
        return serialize($this);
    }
}
