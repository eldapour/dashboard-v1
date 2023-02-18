<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function store(Request $request)
    {
        return response()->json([
            'url'     => '',
        ]);
    }
}
