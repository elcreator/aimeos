<?php

namespace Tests\Unit;

use Aimeos\Client\Html\Iface;
use Aimeos\Shop\Facades\Shop;
use App\Http\Controllers\BasketFragmentController;
use Mockery;
use Tests\TestCase;

class BasketFragmentControllerTest extends TestCase
{
    public function test_returns_the_standard_basket_body_without_caching()
    {
        $html = '<div class="aimeos basket-standard">Basket</div>';
        $basket = Mockery::mock(Iface::class);
        $basket->shouldReceive('body')->once()->andReturn($html);
        Shop::shouldReceive('get')->once()->with('basket/standard')->andReturn($basket);

        $response = (new BasketFragmentController())->indexAction();

        $this->assertSame($html, $response->getContent());
        $this->assertSame('no-store, private', $response->headers->get('Cache-Control'));
    }
}
