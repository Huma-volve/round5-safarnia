<?php

namespace App\Http\Controllers;

use App\Http\Resources\ApiResource;
use App\Models\Category;
use Illuminate\Http\Request;

class ApiController extends Controller
{

// categories//
    public function all (){
    $categories= Category::all();
    return ApiResource::collection($categories);
}
}
