<?php

namespace Azmolla\Shoppingcart\Contracts;
use Illuminate\Database\Eloquent\Casts\Attribute;

interface HasCart
{
    /**
     * Get the identifier of the Buyable item.
     *
     * @return int|string
     */
    public function getCartIdentifier(): string|int;
}
