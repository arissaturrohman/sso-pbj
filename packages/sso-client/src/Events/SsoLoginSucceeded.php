<?php

namespace Demak\SsoClient\Events;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Dipicu setiap login SSO berhasil. Pasang listener untuk
 * mencatat log aktivitas / update last_login kustom, mis:
 *
 *   Event::listen(SsoLoginSucceeded::class, LogSsoLogin::class);
 */
class SsoLoginSucceeded
{
    public function __construct(
        public readonly Authenticatable $user,
        public readonly array $profile,
    ) {}
}
