<?php

/*
 * Copyright (c) 2026 Andrea Peverelli
 * https://github.com/andreapeverelli/phx.git
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

declare(strict_types=1);

namespace AndreaPeverelli\Phx;

use PDO;

final class RelationalDatabase
{
    public private(set) PDO $connection;

    public function __construct()
    {
        $host = getenv("DB_HOST");
        $name = getenv("DB_NAME");
        $user = getenv("DB_USER");
        $password = getenv("DB_PASSWORD");

        $this->connection = new PDO(
            "mysql:host=$host;dbname=$name;charset=utf8mb4",
            $user,
            $password,
        );
    }
}
