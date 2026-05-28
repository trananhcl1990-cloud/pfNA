<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\JsonCmsStore;

class DashboardController extends Controller
{
    public function __invoke(JsonCmsStore $cms)
    {
        return view('admin.dashboard', [
            'posts' => $cms->posts(),
            'experiences' => $cms->experiences(),
        ]);
    }
}
