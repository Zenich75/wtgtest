<?php

namespace App\Http\Controllers\Viewer;

use App\Http\Controllers\Controller;
use App\Models\Import;
use Illuminate\Contracts\View\View;

class ImportController extends Controller
{
    public function index(): View
    {
        $imports = Import::with('supplier')->latest('created_at')->paginate(20);

        return view('viewer.imports.index', ['imports' => $imports]);
    }
}
