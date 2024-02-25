<?php

namespace App\Providers;

use Faker\Generator;
use Illuminate\Support\ServiceProvider;
use Mmo\Faker\PicsumProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $faker = $this->app->make(Generator::class);
        $faker->addProvider(new PicsumProvider($faker));
    }
}
