<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

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
        // return [
        //     ...parent::share($request),
        //     //
        // ];
        $user = $request->user();
        $menus = [
            [
                'title' => 'Dashboard',
                'url'   => '/',
                'icon'  => 'mdi-view-dashboard',
            ],
            [
                'title' => 'Penjualan',
                'url'   => '/penjualan',
                'icon'  => 'mdi-cash-register',
            ],
            [
                'title' => 'Pembayaran',
                'url'   => '/pembayaran',
                'icon'  => 'mdi-credit-card',
            ],
        ];

        if ($user && $user->role_id === 1) { 
            $menus[] = [
                'title' => 'Master',
                'icon'  => 'mdi-folder-multiple',
                'children' => [
                    [
                        'title' => 'User',
                        'url'   => '/master/user',
                        'icon'  => 'mdi-account-group',
                    ],
                    [
                        'title' => 'Item',
                        'url'   => '/master/item',
                        'icon'  => 'mdi-package-variant',
                    ],
                ],
            ];
        }
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user, 
            ],
            'menus' => $menus,
            
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],
        ]);
    }
}
