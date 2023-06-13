<?php

namespace App\Http\Controllers\Products;

use App\Models\Product;
use Illuminate\Http\Request;

class DeleteProductController
{
    public function __invoke(Request $request, Product $product): \Illuminate\Http\RedirectResponse
    {
        $product->delete();

        return redirect()->back();
    }
}
