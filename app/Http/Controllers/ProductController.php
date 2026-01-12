<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::query()
            ->orderBy('name')
            ->get(['id', 'name', 'image_url', 'price', 'stock_quantity']);

        return Inertia::render('Products/Index', [
            'products' => $products,
        ]);
    }
}
