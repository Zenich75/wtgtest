<?php

namespace App\Http\Controllers\Viewer;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Contracts\View\View;

class ReservationController extends Controller
{
    public function index(): View
    {
        $reservations = Reservation::with('offer')->latest('created_at')->paginate(20);

        return view('viewer.reservations.index', ['reservations' => $reservations]);
    }
}
