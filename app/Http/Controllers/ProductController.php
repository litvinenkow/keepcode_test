<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): ProductResource
    {
        return new ProductResource(
            Product::orderBy($request->input('orderBy') ?? 'id', $request->input('orderDir') ?? 'asc')
                ->paginate(
                    $request->input('perPage') ?? 30,
                    ['*'],
                    'page',
                    $request->input('page') ?? 1,
                )
        );
    }

    public function show(int $id): ProductResource
    {
        return new ProductResource(Product::findOrFail($id));
    }
}
