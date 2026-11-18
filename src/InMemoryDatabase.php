<?php

/*
 * Copyright (c) 2026 Andrea Peverelli
 * https://github.com/andreapeverelli/.git
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

declare(strict_types=1);

namespace AndreaPeverelli\Phx;

use Predis\Client;
use Predis\ClientInterface;
use Predis\PredisException;

final class InMemoryDatabase
{
    public private(set) ClientInterface $connection;

    private bool $cache_status;

    public function __construct()
    {
        $endpoint = getenv("IN_MEMORY_ENDPOINT");
        $this->cache_status = getenv("CACHE") === "on" ? true : false;

        $this->connection = new Client($endpoint);
    }

    public function get(string $key): ?string
    {
        if($this->cache_status) {
            try {
                return $this->connection->get($key);
            } catch(PredisException $error) {
                error_log($error->getMessage());
            }
        }

        return null;
    }

    public function setnx(string $key, string $value): bool
    {
        if($this->cache_status) {
            try {
                return $this->connection->setnx($key, $value) === 1 ? true : false;
            } catch(PredisException $error) {
                error_log($error->getMessage());
            }
        }

        return false;
    }
}
