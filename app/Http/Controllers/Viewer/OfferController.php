<?php

namespace App\Http\Controllers\Viewer;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function index(Request $request): View
    {
        $offers = Offer::query()
            ->with(['supplier', 'property'])
            ->when($request->string('supplier')->isNotEmpty(), fn ($query) => $query->whereHas(
                'supplier',
                fn ($query) => $query->where('code', $request->string('supplier'))
            ))
            ->when($request->string('city')->isNotEmpty(), fn ($query) => $query->whereHas(
                'property',
                fn ($query) => $query->where('city', $request->string('city'))
            ))
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('viewer.offers.index', ['offers' => $offers]);
    }
}
