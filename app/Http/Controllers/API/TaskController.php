<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function createTask(Request $request){
        $array = $request->all();
        $array['user_id'] = Auth::id();
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
        
        $isGroupYours = \Models\Group::where(['id' => $array['group_id'], 'user_id' => Auth::id()])->first();
        
        if($isGroupYours){
            return \Models\Task::create($array);
        }
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

        \Models\Task::where([
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
            \Models\Task::where([
                'id' => $array['id'],
                'user_id' => Auth::id()])
                ->update([
                'active' => 0,
            ]);
        }
    }
}
