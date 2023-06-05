<?php

namespace App\Http\Controllers\Products;

use App\Facades\Pagination;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class ShowProductListViewController
{
    /**
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        return Inertia::render('products/ProductList', [
            'datatable' => fn() => $this->getProducts($request)
        ]);
    }

    private function getProducts(Request $request)
    {
        $input = Validator::validate($request->all(), [
            'search' => ['sometimes', 'nullable', 'string'],
            'filters' => ['sometimes', 'nullable', 'array'],
        ]);

        $search = $input['search'] ?? null;
        $price = $input['filters']['prices']['value'] ?? null;

        $query = DB::table('products as p')
            ->whereSearch($search, ['p.name', 'p.description'])
            ->when($price !== null, function (Builder $query) use ($price) {
                $query->where('p.price', '=', $price);
            })
            ->select([
                'id',
                'name',
                'price',
                'description'
            ]);

        return Pagination::get($request, $query);
    }
}
