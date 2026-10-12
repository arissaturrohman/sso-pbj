<?php

namespace App\Models\Passport;

use App\Models\Application;
use Laravel\Passport\Client as BaseClient;

class Client extends BaseClient
{
  public function application()
  {
    return $this->hasOne(
      Application::class,
      'passport_client_id',
      'id'
    );
  }

  public function isActive(): bool
  {
    // Client tanpa baris Application (client lama/test) dianggap aktif
    // selama tidak di-revoke; yang eksplisit dinonaktifkan tetap diblokir.
    if ($this->application === null) {
      return !$this->revoked;
    }

    return $this->application->status === true;
  }

  public function skipsAuthorization(
    \Illuminate\Contracts\Auth\Authenticatable $user,
    array $scopes
  ): bool {
    // First-party SSO: lewati layar consent untuk semua client aktif.
    // Hanya tampilkan consent bila aplikasi eksplisit dinonaktifkan.
    return $this->isActive();
  }
}
