<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        View::composer('*', function ($view) {
            $theme = 'light';
            if (Auth::check()) {
                $isDark = DB::table('user_settings')
                    ->where('user_id', Auth::id())
                    ->where('option_id', 2)
                    ->exists();

                $theme = $isDark ? 'dark' : 'light';
            }
            $view->with('activeTheme', $theme);
        });

        $lang = request()->query('lang');

        $availableLocales = ['en', 'cz', 'de'];

        if ($lang && in_array($lang, $availableLocales)) {
            app()->setLocale($lang);
        }
    }
}
