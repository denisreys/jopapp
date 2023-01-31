<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Affair;

class TaskController extends Controller
{
    public function getTasks(){
        $user_id = Auth::id();

        if($user_id){
            $jopa = Affair::where(['user_id' => $user_id, 'state' => 3, 'active' => 1])
                        ->doesntHave('checkLastWeek')
                        ->orderBy('id')
                        ->with('checkWeek')->get();

            return response()->json($jopa, 200);            
        }
    }
    public function addTask(Request $request){
        $array = $request->all();
        $array['user_id'] = Auth::id();
        $array['state'] = 3;

        $validator = Validator($array, [
            'name' => 'required|min:1|max:50',
            'points' => 'required|numeric|between:10,50',
            'user_id' => 'required'
        ]);
        if($validator->fails()) {
            $response = [
                'success' => false,
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        Affair::create($array);
    }
    public function editTask(Request $request){
        $array = $request->all();
        $array['user_id'] = Auth::id();

        $validator = Validator($array, [
            'id' => 'required',
            'name' => 'required|min:1|max:50',
            'points' => 'required|numeric|between:10,50',
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
            'points' => $array['points'],
        ]);
    }
    public function deleteTask(Request $request) {
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
