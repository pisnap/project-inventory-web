<?php

namespace App\Providers;

use App\Models\Borrowing;
use App\Models\Returning;
use App\Models\Stock_item;
use App\Policies\StockItemPolicy;
use App\Policies\BorrowPolicy;
use App\Policies\ReturnPolicy;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use PhpParser\Node\Stmt\Return_;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Stock_item::class => StockItemPolicy::class,
        Borrowing::class => BorrowPolicy::class,
        Returning::class => ReturnPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
