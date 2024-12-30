<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\Table;

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
        Table::configureUsing(function (Table $table) {
            $table->paginated([10, 25, 50, 100]);
        });
        
        FilamentView::registerRenderHook(
            'panels::scripts.after',
            fn(): string => Blade::render('
        <script>
            if(localStorage.getItem(\'theme\') === null) {
                localStorage.setItem(\'theme\', \'dark\')
            }
        </script>'),
        );
    }
}
