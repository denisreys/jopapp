<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Affair;
use Illuminate\Support\Facades\Auth;

class RegularController extends Controller
{
    public function createGroup(Request $request){
        $group = $request->all();
        $group['user_id'] = Auth::id();
        $group['state'] = 2;

        $validator = Validator($group, [
            'name' => 'required|min:1|max:50',
            'icon' => 'required',
            'color' => 'required',
            'user_id' => 'required'
        ]);
        if($validator->fails()) {
            $response = [
                'success' => false,
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        return Group::create($group);
        
    }
    public function editGroup(Request $request){
        $group = $request->all();
        $group['user_id'] = Auth::id();

        $validator = Validator($group, [
            'name' => 'required|min:1|max:50',
            'icon' => 'required',
            'color' => 'required',
            'user_id' => 'required',
            'group_id' => 'required'
        ]);
        if($validator->fails()) {
            $response = [
                'success' => false,
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        Group::where([
            'id' => $group['group_id'],
            'user_id' => $group['user_id']
        ])->update([
            'name' => $group['name'],
            'icon' => $group['icon'],
            'color' => $group['color']
        ]);
    }
    public function deleteGroup(Request $request){
        $group = $request->all();
        $group['user_id'] = Auth::id();
        if($group['group_id'] && $group['user_id']){
            Group::where([
                'id' => $group['group_id'],
                'user_id' => $group['user_id']
            ])->update([
                'active' => 0,
            ]);
        }
    }
    public function getRegular(){
        $user_id = Auth::id();

        if($user_id){
            return Group::where(['user_id' => $user_id, 'active' => 1])
                ->orderBy('id')
                ->with('affairs.check')
                ->get();
        }     
    }
    public function createAffair(Request $request){
        $array = $request->all();
        $array['user_id'] = Auth::id();
        $array['state'] = 2;

        $validator = Validator($array, [
            'name' => 'required|min:1|max:50',
            'points' => 'required|numeric|between:1,5',
            'user_id' => 'required',
            'group_id' => 'required'
        ]);
        if($validator->fails()) {
            $response = [
                'success' => false,
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        $isGroupYours = Group::where(['id' => $array['group_id'], 'user_id' => $array['user_id']])->first();
        
        if($isGroupYours){
            return Affair::create($array);
        }
    }
    public function editAffair(Request $request){
        $array = $request->all();
        $array['user_id'] = Auth::id();

        $validator = Validator($array, [
            'id' => 'required',
            'name' => 'required|min:1|max:50',
            'group_id' => 'required',
            'points' => 'required|numeric|between:1,5',
            'user_id' => 'required'
        ]);
        if($validator->fails()) {
            $response = [
                'success' => false,
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        Affair::where([
                'id' => $array['id'],
                'user_id' => $array['user_id']
            ])->update([
                'name' => $array['name'],
                'group_id' => $array['group_id'],
                'points' => $array['points'],
        ]);
    }
    public function deleteAffair(Request $request) {
        $array = $request->all();
        $array['user_id'] = Auth::id();

        if($array['id'] && $array['user_id']){
            Affair::where([
                'id' => $array['id'],
                'user_id' => $array['user_id']
            ])->update([
                'active' => 0,
            ]);
        }
    }
}
