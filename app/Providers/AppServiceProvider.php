<?php

namespace App\Providers;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            static $count = null;

            if ($count === null) {
                $count = Auth::check()
                    ? Notification::where('user_id', Auth::id())->whereNull('read_at')->count()
                    : 0;
            }

            $view->with('unreadCount', $count);
        });
    }
}