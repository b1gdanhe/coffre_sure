<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user() ? $request->user()->load('roles') : $request->user(),
            ],
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'vaults' => function () use ($request) {
                $user = $request->user();

                if (!$user) {
                    return null;
                }

                // Récupérer le coffre actif
                $activeVault = $user->getActiveVault();

                return [
                    'currentVault' => $activeVault ? [
                        'id' => $activeVault->id,
                        'name' => $activeVault->name,
                        'description' => $activeVault->description,
                    ] : null,
                    'list' => $user->vaults()
                        ->select('vaults.id', 'vaults.name')
                        ->get()
                        ->map(function ($vault) {
                            return [
                                'id' => $vault->id,
                                'name' => $vault->name,
                            ];
                        })
                ];
            },
        ];
    }
}
