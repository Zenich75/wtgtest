<?php

namespace App\Http\Controllers\Viewer;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Contracts\View\View;

class PropertyController extends Controller
{
    public function index(): View
    {
        $properties = Property::withCount('offers')->latest('created_at')->paginate(20);

        return view('viewer.properties.index', ['properties' => $properties]);
    }
}
