<?php

namespace Demak\SsoClient\Resolvers;

use Demak\SsoClient\Contracts\SsoUserResolver;
use Illuminate\Contracts\Auth\Authenticatable;

class DefaultUserResolver implements SsoUserResolver
{
    public function resolve(array $profile): ?Authenticatable
    {
        $model = config('sso-client.user_model', 'App\\Models\\User');

        if (!is_string($model) || !class_exists($model)) {
            return null;
        }

        // NIP diprioritaskan; email dipakai kalau profil tidak bawa NIP.
        if (!empty($profile['nip'])) {
            $user = $model::where('nip', $profile['nip'])->first();
        } else {
            $email = mb_strtolower(trim((string) ($profile['email'] ?? '')));

            if ($email === '') {
                return null;
            }

            $user = $model::whereRaw('LOWER(email) = ?', [$email])->first();
        }

        if (!$user) {
            return null;
        }

        // Terima status 'active' (string) maupun true/1 (boolean).
        $status = $user->status ?? null;
        $active = $status === 'active' || $status === 1 || $status === true;

        return $active ? $user : null;
    }
}
