<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class SetUserLocale
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $languageOption = Auth::user()->settings()
                ->whereHas('setting', fn($q) => $q->where('name', 'language'))
                ->first();

            if ($languageOption) {
                $dbValue = $languageOption->option_data;

                $locale = match($dbValue) {
                    'czech'   => 'cz',
                    'english' => 'en',
                    'german' => 'de',
                    default   => 'en',
                };

                app()->setLocale($locale);
            }
        }

        return $next($request);
    }
}
