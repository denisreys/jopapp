<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class RegularController extends Controller
{
    public function createGroup(){
        return 1;
        /*if($request){
            $group_name = trim($request['name']);
            $group_icon = trim($request['icon']);
            $group_color = trim($request['color']);
            $user_id = User::id();
        }*/
    }
    public function editGroup(Request $request){

    }
    public function deleteGroup(Request $request){

    }
}
