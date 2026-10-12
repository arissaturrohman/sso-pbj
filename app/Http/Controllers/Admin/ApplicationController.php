<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Passport\ClientRepository;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $applications = Application::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.applications.index',
            compact('applications')
        );
    }

    public function create()
    {
        return view('admin.applications.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'slug'         => ['required', 'string', 'max:100', 'alpha_dash', 'unique:applications,slug'],
            'description'  => ['nullable', 'string'],
            'redirect_uri' => ['required', 'url'],
        ]);

        DB::transaction(function () use ($validated) {

            $clientRepository = app(ClientRepository::class);

            /*
        |--------------------------------------------------------------------------
        | Buat OAuth Client Passport
        |--------------------------------------------------------------------------
        |
        | Public client (confidential = false), tanpa client secret.
        | Authentication menggunakan Authorization Code + PKCE.
        |
        */

            $client = $clientRepository->createAuthorizationCodeGrantClient(
                name: $validated['name'],
                redirectUris: [$validated['redirect_uri']],
                confidential: false,
                user: auth()->user(),
                enableDeviceFlow: false,
            );

            Application::create([
                'name'               => $validated['name'],
                'slug'               => $validated['slug'],
                'description'        => $validated['description'] ?? null,
                'passport_client_id' => $client->getKey(),
                'redirect_uri'       => $validated['redirect_uri'],
                'status'             => true,
            ]);
        });

        return redirect()
            ->route('admin.applications.index')
            ->with('success', 'Aplikasi berhasil didaftarkan sebagai OAuth Client.');
    }

    public function edit(Application $application)
    {
        return view(
            'admin.applications.edit',
            compact('application')
        );
    }

    public function update(
        Request $request,
        Application $application
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('applications', 'slug')
                    ->ignore($application->id),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'redirect_uri' => [
                'required',
                'url',
            ],

            'status' => [
                'required',
                'boolean',
            ],
        ]);

        $application->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
            'redirect_uri' => $validated['redirect_uri'],
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('admin.applications.index')
            ->with(
                'success',
                'Aplikasi berhasil diperbarui.'
            );
    }

    public function destroy(Application $application)
    {
        DB::transaction(function () use ($application) {

            if ($application->passportClient) {
                $application->passportClient->delete();
            }

            $application->delete();
        });

        return redirect()
            ->route('admin.applications.index')
            ->with('success', 'Aplikasi dan OAuth Client berhasil dihapus.');
    }

    public function toggleStatus(Application $application)
    {
        if (! $application->passport_client_id) {
            return back()->with(
                'error',
                'Aplikasi belum memiliki OAuth Client Passport.'
            );
        }

        $application->update([
            'status' => ! $application->status,
        ]);

        $status = $application->status
            ? 'diaktifkan'
            : 'dinonaktifkan';

        return redirect()
            ->route('admin.applications.index')
            ->with(
                'success',
                "Aplikasi {$application->name} berhasil {$status}."
            );
    }

    // public function regenerateSecret(Application $application)
    // {
    //     $application->update([
    //         'client_secret' => Str::random(64),
    //     ]);

    //     return back()->with(
    //         'success',
    //         'Client secret berhasil dibuat ulang.'
    //     );
    // }
}
