<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\App;

class LanguageServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Share language data with all views
        View::composer('*', function ($view) {
            $locale = App::getLocale();
            $languages = config('language.supported_locales');
            
            $view->with('currentLocale', $locale);
            $view->with('currentLanguage', $languages[$locale] ?? []);
            $view->with('languages', $languages);
        });
    }
}
