<?php

/*
 * Copyright (c) 2026 Andrea Peverelli
 * https://github.com/andreapeverelli/phx.git
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

declare(strict_types=1);

namespace AndreaPeverelli\Phx;

final class Typo
{
    public function __construct(
        public string $role,
        public string $sub_role,
        public bool $emphasized = false,
        public bool $italic = false,
        public ?string $text = null,
        public string $class = "",
    ) {}

    public function __toString(): string
    {
        return serialize($this);
    }
}
