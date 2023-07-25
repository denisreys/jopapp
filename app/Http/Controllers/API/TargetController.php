<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class TargetController extends Controller
{
    public function getTargets(){
        //YOU CAN SEE TARGETS A WEEK AFTER CHECK
        $response = \Models\Task::where(['user_id' => Auth::id(), 'state' => 3, 'active' => 1])
                    ->doesntHave('checkLastWeek')
                    ->orderBy('id')
                    ->with('checkWeek')->get();

        return response()->json($response, 200);            
    }
    public function addTarget(Request $request){
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

        \Models\Task::create($array);
    }
    public function editTarget(Request $request){
        $array = $request->all();

        $validator = Validator($array, [
            'id' => 'required',
            'name' => 'required|min:1|max:50',
            'points' => 'required|numeric|between:10,50',
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
            'points' => $array['points'],
        ]);
    }
    public function deleteTarget(Request $request) {
        $array = $request->all();

        if($array['id']){
            \Models\Task::where([
                'id' => $array['id'],
                'user_id' => Auth::id()
            ])->update([
                'active' => 0,
            ]);
        }
    }
}
