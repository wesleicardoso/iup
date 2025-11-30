<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

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
        Vite::prefetch(concurrency: 3);
        // Forçar HTTPS se estiver rodando em produção ou Ngrok
       if ($this->app->environment('production') || $this->app->environment('local')) {

            // Detecta se é Ngrok
            if (str_contains(request()->getHost(), 'ngrok') || env('FORCE_HTTPS', false)) {
                // 1. Força HTTPS
                URL::forceScheme('https');

                // 2. A MÁGICA: Força o domínio raiz ser o do Ngrok (que está no .env)
                // Isso corrige o erro de Invalid Signature
                URL::forceRootUrl(config('app.url'));
            }
        }
    }
}
