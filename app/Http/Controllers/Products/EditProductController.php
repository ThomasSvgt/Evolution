<?php

namespace App\Http\Controllers\Products;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\EditProduct;

class EditProductController
{
    public function __invoke(EditProduct $request, Product $product): \Illuminate\Http\RedirectResponse
    {
        $input = $request->validated();

        $product->update($input);

        return redirect()->back();
    }
}
