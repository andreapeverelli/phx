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
use DateTimeImmutable;
use DateTimeZone;

final class Auth
{
    public static function authenticate(PDO $connection): void
    {
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $user_id = $_SESSION["user_id"] ?? false;

        if(!$user_id) {
            $token = $_COOKIE["session_token"] ?? null;

            if($token) {
                $token_hash = hash("sha512", $token, true);

                $statement = $connection->prepare(<<<SQL
                SELECT user_id
                FROM sessions
                WHERE
                    token = :token
                    AND expires_at > CURRENT_TIMESTAMP
                LIMIT 1
                SQL);

                $statement->execute(["token" => $token_hash]);

                $user_id = $statement->fetchColumn();

                if($user_id) {
                    session_regenerate_id(true);
                    $_SESSION["user_id"] = $user_id;
                }
            }
        }

        if($user_id && $_SERVER["REQUEST_URI"] === "/login") header("Location: /", true, 303);
        if(!$user_id && $_SERVER["REQUEST_URI"] !== "/login") header("Location: /login", true, 303);
    }

    public static function login(
        PDO $connection,
        string $username,
        string $password,
        string $form_id,
        string $csfr_token,
    ): bool
    {
        if(session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if(
            !isset($_SESSION["csfr_token_form_$form_id"])
            || $csfr_token !== $_SESSION["csfr_token_form_$form_id"]
        ) return false;

        $password_hash = hash("sha512", $password, true);

        $statement = $connection->prepare(<<<SQL
        SELECT id
        FROM users
        WHERE
            username = :username
            AND password = :password
        LIMIT 1
        SQL);

        $statement->execute(["username" => $username, "password" => $password_hash]);

        $user_id = $statement->fetchColumn();

        if(!$user_id) return false;

        $session_token = bin2hex(random_bytes(32));
        $session_token_hash = hash("sha512", $session_token, true);

        $expires_at = new DateTimeImmutable("+30 days", new DateTimeZone("UTC"));

        $statement = $connection->prepare(<<<SQL
        INSERT INTO sessions(user_id, token, expires_at)
        VALUES (:user_id, :token, :expires_at)
        SQL);

        $result = $statement->execute(["user_id" => $user_id, "token" => $session_token_hash, "expires_at" => $expires_at->format("Y-m-d H:i:s")]);

        if(!$result) return false;

        session_regenerate_id(true);
        $_SESSION["user_id"] = $user_id;
        unset($_SESSION["csfr_token_form_$form_id"]);

        setcookie("session_token", $session_token, [
            "expires" => $expires_at->getTimestamp(),
            "path" => "/",
            "secure" => true,
            "httponly" => true,
            "samesite" => "Lax",
        ]);
    }
}
