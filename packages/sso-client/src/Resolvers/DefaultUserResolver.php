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

        $nip = trim((string) ($profile['nip'] ?? ''));
        $email = mb_strtolower(trim((string) ($profile['email'] ?? '')));

        // Prioritaskan NIP.
        if ($nip !== '') {
            $user = $model::where('nip', $nip)->first();
        } elseif ($email !== '') {
            $user = $model::whereRaw('LOWER(email) = ?', [$email])->first();
        } else {
            return null;
        }

        if (!$user) {
            return null;
        }

        $status = strtolower(trim((string) ($user->status ?? '')));

        $active = in_array($status, ['aktif', 'active', '1', 'true'], true);

        return $active ? $user : null;
    }
}