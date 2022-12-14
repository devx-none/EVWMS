<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $addHttpCookie = true;
 

    protected $except = [
        //
        'stripe/*',
        'http://127.0.0.1:8000/products',
        'http://127.0.0.1:8000/products/*',
        'http://127.0.0.1:8000/deliveries',
        'http://127.0.0.1:8000/deliveries/*',
        'http://127.0.0.1:8000/receipts',
        'http://127.0.0.1:8000/receipts/*',
    ];
}
