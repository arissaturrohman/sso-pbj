<?php

namespace Demak\SsoClient\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Mencocokkan profil dari server SSO dengan user lokal.
 *
 * Kembalikan user lokal yang sudah aktif, atau null kalau tidak
 * ditemukan / tidak aktif (nanti ditolak dengan pesan ramah).
 */
interface SsoUserResolver
{
    public function resolve(array $profile): ?Authenticatable;
}
