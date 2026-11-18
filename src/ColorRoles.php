<?php

/*
 * Copyright (c) 2026 Andrea Peverelli
 * https://github.com/andreapeverelli/phx.git
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

declare(strict_types=1);

namespace AndreaPeverelli\Phx;

final class ColorRoles
{
    public static array $roles = [
        "standard" => [
            "role" => [
                "palette" => [
                    "light" => [
                        "default-contrast" => 40,
                        "high-contrast" => 20,
                    ],
                    "dark" => [
                        "default-contrast" => 80,
                        "high-contrast" => 95,
                    ],
                ],
                "background-of" => ["on-role"],
            ],
            "on-role" => [
                "palette" => [
                    "light" => [
                        "default-contrast" => 100,
                        "high-contrast" => 100,
                    ],
                    "dark" => [
                        "default-contrast" => 20,
                        "high-contrast" => 0,
                    ],
                ],
                "background-of" => [],
            ],
            "role-container" => [
                "palette" => [
                    "light" => [
                        "default-contrast" => 90,
                        "high-contrast" => 30,
                    ],
                    "dark" => [
                        "default-contrast" => 30,
                        "high-contrast" => 80,
                    ],
                ],
                "background-of" => ["on-role-container"],
            ],
            "on-role-container" => [
                "palette" => [
                    "light" => [
                        "default-contrast" => 30,
                        "high-contrast" => 100,
                    ],
                    "dark" => [
                        "default-contrast" => 90,
                        "high-contrast" => 0,
                    ],
                ],
                "background-of" => [],
            ],
            "role-fixed" => [
                "palette" => [
                    "light" => [
                        "default-contrast" => 90,
                        "high-contrast" => 30,
                    ],
                    "dark" => [
                        "default-contrast" => 90,
                        "high-contrast" => 90,
                    ],
                ],
                "background-of" => ["on-role-fixed", "on-role-fixed-variant"],
            ],
            "on-role-fixed" => [
                "palette" => [
                    "light" => [
                        "default-contrast" => 10,
                        "high-contrast" => 100,
                    ],
                    "dark" => [
                        "default-contrast" => 10,
                        "high-contrast" => 0,
                    ],
                ],
                "background-of" => [],
            ],
            "role-fixed-dim" => [
                "palette" => [
                    "light" => [
                        "default-contrast" => 80,
                        "high-contrast" => 20,
                    ],
                    "dark" => [
                        "default-contrast" => 80,
                        "high-contrast" => 80,
                    ],
                ],
                "background-of" => ["on-role-fixed", "on-role-fixed-variant"],
            ],
            "on-role-fixed-variant" => [
                "palette" => [
                    "light" => [
                        "default-contrast" => 30,
                        "high-contrast" => 100,
                    ],
                    "dark" => [
                        "default-contrast" => 30,
                        "high-contrast" => 0,
                    ],
                ],
                "background-of" => [],
            ],
        ],
        "special" => [
            "inverse-primary" => [
                "base-color" => "primary",
                "palette" => [
                    "light" => [
                        "default-contrast" => 80,
                        "high-contrast" => 80,
                    ],
                    "dark" => [
                        "default-contrast" => 40,
                        "high-contrast" => 20,
                    ],
                ],
                "background-of" => [],
            ],
            "surface" => [
                "base-color" => "neutral",
                "palette" => [
                    "light" => [
                        "default-contrast" => 98,
                        "high-contrast" => 98,
                    ],
                    "dark" => [
                        "default-contrast" => 6,
                        "high-contrast" => 6,
                    ],
                ],
                "background-of" => ["on-surface", "surface-tint", "role"],
            ],
            "on-surface" => [
                "base-color" => "neutral",
                "palette" => [
                    "light" => [
                        "default-contrast" => 10,
                        "high-contrast" => 0,
                    ],
                    "dark" => [
                        "default-contrast" => 90,
                        "high-contrast" => 100,
                    ],
                ],
                "background-of" => [],
            ],
            "surface-variant" => [
                "base-color" => "variant",
                "palette" => [
                    "light" => [
                        "default-contrast" => 90,
                        "high-contrast" => 90,
                    ],
                    "dark" => [
                        "default-contrast" => 30,
                        "high-contrast" => 30,
                    ],
                ],
                "background-of" => ["on-surface-variant", "surface-tint", "role"],
            ],
            "on-surface-variant" => [
                "base-color" => "variant",
                "palette" => [
                    "light" => [
                        "default-contrast" => 30,
                        "high-contrast" => 0,
                    ],
                    "dark" => [
                        "default-contrast" => 80,
                        "high-contrast" => 100,
                    ],
                ],
                "background-of" => [],
            ],
            "surface-container-highest" => [
                "base-color" => "neutral",
                "palette" => [
                    "light" => [
                        "default-contrast" => 90,
                        "high-contrast" => 90,
                    ],
                    "dark" => [
                        "default-contrast" => 22,
                        "high-contrast" => 22,
                    ],
                ],
                "background-of" => ["on-surface", "surface-tint", "role"],
            ],
            "surface-container-high" => [
                "base-color" => "neutral",
                "palette" => [
                    "light" => [
                        "default-contrast" => 92,
                        "high-contrast" => 92,
                    ],
                    "dark" => [
                        "default-contrast" => 17,
                        "high-contrast" => 17,
                    ],
                ],
                "background-of" => ["on-surface", "surface-tint", "role"],
            ],
            "surface-container" => [
                "base-color" => "neutral",
                "palette" => [
                    "light" => [
                        "default-contrast" => 94,
                        "high-contrast" => 94,
                    ],
                    "dark" => [
                        "default-contrast" => 12,
                        "high-contrast" => 12,
                    ],
                ],
                "background-of" => ["on-surface", "surface-tint", "role"],
            ],
            "surface-container-low" => [
                "base-color" => "neutral",
                "palette" => [
                    "light" => [
                        "default-contrast" => 96,
                        "high-contrast" => 96,
                    ],
                    "dark" => [
                        "default-contrast" => 10,
                        "high-contrast" => 10,
                    ],
                ],
                "background-of" => ["on-surface", "surface-tint", "role"],
            ],
            "surface-container-lowest" => [
                "base-color" => "neutral",
                "palette" => [
                    "light" => [
                        "default-contrast" => 100,
                        "high-contrast" => 100,
                    ],
                    "dark" => [
                        "default-contrast" => 4,
                        "high-contrast" => 4,
                    ],
                ],
                "background-of" => ["on-surface", "surface-tint", "role"],
            ],
            "inverse-surface" => [
                "base-color" => "neutral",
                "palette" => [
                    "light" => [
                        "default-contrast" => 20,
                        "high-contrast" => 20,
                    ],
                    "dark" => [
                        "default-contrast" => 90,
                        "high-contrast" => 90,
                    ],
                ],
                "background-of" => ["inverse-on-surface", "inverse-primary"],
            ],
            "inverse-on-surface" => [
                "base-color" => "neutral",
                "palette" => [
                    "light" => [
                        "default-contrast" => 95,
                        "high-contrast" => 100,
                    ],
                    "dark" => [
                        "default-contrast" => 20,
                        "high-contrast" => 0,
                    ],
                ],
                "background-of" => [],
            ],
            "surface-tint" => [
                "base-color" => "primary",
                "palette" => [
                    "light" => [
                        "default-contrast" => 40,
                        "high-contrast" => 20,
                    ],
                    "dark" => [
                        "default-contrast" => 80,
                        "high-contrast" => 95,
                    ],
                ],
                "background-of" => [],
            ],
            "surface-tint-color" => [
                "base-color" => "primary",
                "palette" => [
                    "light" => [
                        "default-contrast" => 40,
                        "high-contrast" => 20,
                    ],
                    "dark" => [
                        "default-contrast" => 80,
                        "high-contrast" => 95,
                    ],
                ],
                "background-of" => [],
            ],
            "outline" => [
                "base-color" => "variant",
                "palette" => [
                    "light" => [
                        "default-contrast" => 50,
                        "high-contrast" => 20,
                    ],
                    "dark" => [
                        "default-contrast" => 60,
                        "high-contrast" => 95,
                    ],
                ],
                "background-of" => [],
            ],
            "outline-variant" => [
                "base-color" => "variant",
                "palette" => [
                    "light" => [
                        "default-contrast" => 80,
                        "high-contrast" => 30,
                    ],
                    "dark" => [
                        "default-contrast" => 30,
                        "high-contrast" => 80,
                    ],
                ],
                "background-of" => [],
            ],
            "background" => [
                "base-color" => "neutral",
                "palette" => [
                    "light" => [
                        "default-contrast" => 98,
                        "high-contrast" => 98,
                    ],
                    "dark" => [
                        "default-contrast" => 6,
                        "high-contrast" => 6,
                    ],
                ],
                "background-of" => ["on-background", "role"],
            ],
            "on-background" => [
                "base-color" => "neutral",
                "palette" => [
                    "light" => [
                        "default-contrast" => 10,
                        "high-contrast" => 0,
                    ],
                    "dark" => [
                        "default-contrast" => 90,
                        "high-contrast" => 100,
                    ],
                ],
                "background-of" => [],
            ],
            "surface-bright" => [
                "base-color" => "neutral",
                "palette" => [
                    "light" => [
                        "default-contrast" => 98,
                        "high-contrast" => 98,
                    ],
                    "dark" => [
                        "default-contrast" => 24,
                        "high-contrast" => 24,
                    ],
                ],
                "background-of" => ["on-surface", "surface-tint", "role"],
            ],
            "surface-dim" => [
                "base-color" => "neutral",
                "palette" => [
                    "light" => [
                        "default-contrast" => 87,
                        "high-contrast" => 87,
                    ],
                    "dark" => [
                        "default-contrast" => 6,
                        "high-contrast" => 6,
                    ],
                ],
                "background-of" => ["on-surface", "surface-tint", "role"],
            ],
            "scrim" => [
                "base-color" => "neutral",
                "palette" => [
                    "light" => [
                        "default-contrast" => 0,
                        "high-contrast" => 0,
                    ],
                    "dark" => [
                        "default-contrast" => 0,
                        "high-contrast" => 0,
                    ],
                ],
                "background-of" => [],
            ],
            "shadow" => [
                "base-color" => "neutral",
                "palette" => [
                    "light" => [
                        "default-contrast" => 0,
                        "high-contrast" => 0,
                    ],
                    "dark" => [
                        "default-contrast" => 0,
                        "high-contrast" => 0,
                    ],
                ],
                "background-of" => [],
            ],
        ],
    ];
}
