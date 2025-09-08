<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;    
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;   

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if(config('app.env') === 'production') {
        URL::forceScheme('https');
        }
        Vite::prefetch(concurrency: 3);
        
        Inertia::share([
        'auth' => function () {
            if ($user = Auth::user()) {
                return [
                    'user' => [
                        'id'    => $user->id,
                        'name'  => $user->name,
                        'email' => $user->email,
                        'role'  => $user->role, // suppose que tu as une colonne "role"
                    ],
                ];
            }
            return [];
        },
    ]);

    Inertia::share([
    'auth' => fn () => [
        'user' => Auth::user(),
        'unread_notifications' => Auth::user()?->unreadNotifications()->count() ?? 0,
    ],
]);
    }
}
