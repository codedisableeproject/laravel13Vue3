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
        return array_merge(parent::share($request), [
            // Data otentikasi (bawaan)
            'auth' => [
                'user' => $request->user(),
                // 'user' => [
                //     'name'  => 'Budi Santoso',
                //     'email' => 'budi.santoso@gmail.com',
                // ],
            ],
            
            // INI YANG KITA TAMBAHKAN: Data Menu Dinamis
            'menus' => [
                [
                    'title' => 'Dashboard',
                    'url'   => '/',
                    'icon'  => 'mdi-view-dashboard',
                ],
                [
                    'title' => 'Profil Saya',
                    'url'   => '/profile',
                    'icon'  => 'mdi-account',
                ],
                [
                    'title' => 'About',
                    'url'   => '/about',
                    'icon'  => 'mdi-information',
                ],
                // TAMBAHKAN INI UNTUK TES:
                [
                    'title' => 'Google Search',
                    'url'   => 'https://google.com',
                    'icon'  => 'mdi-google',
                ],
                // Jika nanti ada halaman baru, cukup tambah di sini!
                // [
                //     'title' => 'Data Barang',
                //     'url'   => '/barang',
                //     'icon'  => 'mdi-package-variant',
                // ],
            ],
        ]);
        
    }
}
