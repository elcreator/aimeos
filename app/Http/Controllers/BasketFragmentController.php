<?php

namespace App\Http\Controllers;

use Aimeos\Shop\Facades\Shop;
use Illuminate\Support\Facades\Response;

class BasketFragmentController extends Controller
{
    /**
     * Returns the updated standard basket without rendering the full shop page.
     */
    public function indexAction()
    {
        return Response::make(Shop::get('basket/standard')->body())
            ->header('Cache-Control', 'no-store, private');
    }
}
