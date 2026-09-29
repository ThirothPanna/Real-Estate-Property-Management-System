<?php

namespace App\Providers;

use App\Models\Notification;
use App\Models\User;
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

        View::composer('layouts.app', function ($view) {
            if (! Auth::check()) {
                $view->with('switchableAccounts', collect());

                return;
            }

            $accountIds = collect(session('account_switch_ids', []))
                ->filter(fn ($id) => is_int($id) || (is_string($id) && ctype_digit($id)))
                ->map(fn ($id) => (int) $id)
                ->push((int) Auth::id())
                ->unique()
                ->values();

            $accounts = User::query()
                ->whereKey($accountIds)
                ->get()
                ->sortBy(fn (User $user) => $accountIds->search($user->getKey()))
                ->values();

            $view->with('switchableAccounts', $accounts);
        });
    }
}