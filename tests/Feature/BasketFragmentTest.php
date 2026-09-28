<?php

namespace Tests\Feature;

use Aimeos\Shop\Facades\Shop;
use Mockery;
use Tests\TestCase;

class BasketFragmentTest extends TestCase
{
    public function test_returns_only_the_standard_basket_fragment()
    {
        $basket = Mockery::mock(\Aimeos\Client\Html\Iface::class);
        $basket->shouldReceive('body')->once()->andReturn('<div class="aimeos basket-standard">Basket</div>');

        Shop::shouldReceive('get')->once()->with('basket/standard')->andReturn($basket);

        $this->get('/en/default/basket-fragment')
            ->assertOk()
            ->assertSee('class="aimeos basket-standard"', false)
            ->assertHeader('Cache-Control', 'no-store, private');
    }
}
