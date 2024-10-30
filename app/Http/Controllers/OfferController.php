<?php

namespace App\Http\Controllers;

use App\Http\Requests\Dashboard\OfferRequest;
use App\Models\Offer;
use Inertia\Inertia;

class OfferController extends Controller
{
    public function store(OfferRequest $request)
    {
        return redirect()->back()->with('offer', [
            'data' => Offer::create([
                'user_id' => auth()->id(),
                'data' => $request->get('data'),
            ]),
        ]);
    }

    public function show(Offer $offer)
    {
        return \inertia('OfferShow', ['offer' => $offer]);
    }
}
