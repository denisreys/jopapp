<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\Task;
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

        return Group::create($group);
        
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

        Group::where([
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
            Group::where([
                'id' => $group['group_id'],
                'user_id' => Auth::id()])
                 ->update([
                    'active' => 0
                ]);
        }
    }
    public function getGroups(){
        return Group::where(['user_id' => Auth::id(), 'active' => 1])
                    ->orderBy('id')
                    ->with('tasks.check')
                    ->get();
    }
    public function createTask(Request $request){
        $array = $request->all();
        $array['state'] = 2;

        $validator = Validator($array, [
            'name' => 'required|min:1|max:50',
            'points' => 'required|numeric|between:1,5',
            'group_id' => 'required'
        ]);
        if($validator->fails()) {
            $response = [
                'success' => false,
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        $isGroupYours = Group::where(['id' => $array['group_id'], 'user_id' => Auth::id()])->first();
        
        if($isGroupYours)
            return Task::create($array);
    }
    public function editTask(Request $request){
        $array = $request->all();

        $validator = Validator($array, [
            'id' => 'required',
            'name' => 'required|min:1|max:50',
            'group_id' => 'required',
            'points' => 'required|numeric|between:1,5',
        ]);
        if($validator->fails()) {
            $response = [
                'success' => false,
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        Task::where([
                'id' => $array['id'],
                'user_id' => Auth::id()
            ])->update([
                'name' => $array['name'],
                'group_id' => $array['group_id'],
                'points' => $array['points'],
        ]);
    }
    public function deleteTask(Request $request) {
        $array = $request->all();

        if($array['id']){
            Task::where([
                'id' => $array['id'],
                'user_id' => Auth::id()])
                ->update([
                'active' => 0,
            ]);
        }
    }
}
