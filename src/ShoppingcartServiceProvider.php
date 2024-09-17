<?php

namespace Azmolla\Shoppingcart;

use Azmolla\Shoppingcart\Contracts\HasCart;
use Illuminate\Auth\Events\Logout;
use Illuminate\Session\SessionManager;
use Illuminate\Support\ServiceProvider;

class ShoppingcartServiceProvider extends ServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('cart', Cart::class);

        $config = __DIR__ . '/../config/cart.php';
        $this->mergeConfigFrom($config, 'cart');

        $this->publishes([__DIR__ . '/../config/cart.php' => config_path('cart.php')], 'config');

        $this->app['events']->listen(Logout::class, function () {
            if ($this->app['config']->get('cart.destroy_on_logout')) {
                $this->app->make(SessionManager::class)->forget('cart');
            }
        });

        $this->app->terminating(function () {
            if ($this->app['config']->get('cart.auto_save_to_user') && auth()->user() && auth()->user() instanceof HasCart) {
                $this->app->make('cart')->storeOrUpdate(auth()->user()->getCartIdentifier());
            }
        });

        if ( ! class_exists('CreateShoppingcartTable')) {
            // Publish the migration
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__ . '/../database/migrations/0000_00_00_000000_create_shopping_cart_table.php' => database_path('migrations/'.$timestamp.'_create_shoppingcart_table.php'),
            ], 'migrations');
        }
    }
}
