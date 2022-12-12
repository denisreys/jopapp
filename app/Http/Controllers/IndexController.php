<?php

namespace App\Http\Controllers;

class IndexController extends Controller
{
     /**
     * Provision a new web server.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        return view(view: 'index');
    }
}