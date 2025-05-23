<?php

namespace App\Providers;

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
        //
    }
    public function map()
{
    $this->mapWebRoutes();
    $this->mapApiRoutes();
}

protected function mapWebRoutes()
{
    Route::middleware('web')  // <-- sprawdź, czy ta linia istnieje
        ->namespace($this->namespace)
        ->group(base_path('routes/web.php'));
}

}
