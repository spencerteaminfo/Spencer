<?php

namespace App\Providers;

use App\Listeners\EventNotificationsSender;
use App\Listeners\GroupNotificationsSender;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

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
        Event::subscribe(GroupNotificationsSender::class);
        Event::subscribe(EventNotificationsSender::class);

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
