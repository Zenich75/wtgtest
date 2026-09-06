<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'WTG Spain API',
    description: 'API for supplier offer imports, property availability search, and reservations.',
)]
abstract class Controller
{
    //
}
