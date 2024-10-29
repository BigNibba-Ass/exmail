<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SearchRequest;
use App\Models\Offer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index(SearchRequest $request)
    {
        return inertia('Admin/Offer/Index', [
            'offers' => Offer::with('user')->when($request->name, function (Builder $query) use ($request) {
                return $query->whereHas('user', function ($query) use ($request) {
                    return $query->where('name', 'like', '%' . $request->name . '%');
                });
            })->paginate(20)
        ]);
    }

    public function create()
    {
    }

    public function store(Request $request)
    {
    }

    public function show($id)
    {
    }

    public function edit($id)
    {
    }

    public function update(Request $request, $id)
    {
    }

    public function destroy($id)
    {
    }
}
