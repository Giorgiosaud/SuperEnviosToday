<?php

namespace App\Http\Controllers;

use App\Role;

class RolesController extends Controller
{
    public function index()
    {
        return Role::all();
    }

    //
}
