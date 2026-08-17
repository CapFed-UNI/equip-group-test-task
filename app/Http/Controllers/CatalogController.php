<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Product;
use App\Services\CatalogService;
use App\Services\GroupTreeService;
use App\Support\CatalogQuery;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request, CatalogService $catalog): View
    {
        return $this->listingResponse($request, $catalog, null);
    }

    public function group(Request $request, Group $group, CatalogService $catalog): View
    {
        return $this->listingResponse($request, $catalog, $group);
    }

    public function product(Product $product, GroupTreeService $tree): View
    {
        $product->load(['price', 'group']);

        return view('catalog.product', [
            'product' => $product,
            'breadcrumbs' => $tree->breadcrumbs($product->group),
        ]);
    }

    private function listingResponse(Request $request, CatalogService $catalog, ?Group $group): View
    {
        $data = $catalog->listing($group, CatalogQuery::fromRequest($request));

        if ($request->ajax()) {
            return view('catalog.partials.listing', $data);
        }

        return view('catalog.index', $data);
    }
}
