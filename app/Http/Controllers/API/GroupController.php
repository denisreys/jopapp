<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    public function createGroup(Request $request){
        $group = $request->all();
        $group['user_id'] = Auth::id();
        $group['state'] = 2;

        $validator = Validator($group, [
            'name' => 'required|min:1|max:50',
            'icon' => 'required',
            'color' => 'required'
        ]);
        if($validator->fails()) {
            $response = [
                'success' => false,
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        return \Models\Group::create($group);
        
    }
    public function editGroup(Request $request){
        $group = $request->all();

        $validator = Validator($group, [
            'name' => 'required|min:1|max:50',
            'icon' => 'required',
            'color' => 'required',
            'group_id' => 'required'
        ]);
        if($validator->fails()) {
            $response = [
                'success' => false,
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        \Models\Group::where([
            'id' => $group['group_id'],
            'user_id' => Auth::id()
        ])->update([
            'name' => $group['name'],
            'icon' => $group['icon'],
            'color' => $group['color']
        ]);
    }
    public function deleteGroup(Request $request){
        $group = $request->all();
        if($group['group_id']){
            \Models\Group::where([
                'id' => $group['group_id'],
                'user_id' => Auth::id()])
                 ->update([
                    'active' => 0
                ]);
        }
    }
    public function getGroups(){
        return \Models\Group::where(['user_id' => Auth::id(), 'active' => 1])
                    ->orderBy('id')
                    ->with('tasks.check')
                    ->get();
    }
}
