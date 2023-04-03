<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Todo;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;


class TodoController extends Controller
{
    public function getTodoes(){
        $user_id = Auth::id();
        $week = [];
        //GET TODOES WITH CHECKS BY DATE BETWEEN TODAY AND +7 DAYS
        $todoes = Todo::with('task.checks')->whereHas('task', function($q) use($user_id){
            $q->where('user_id', $user_id);
        })->whereBetween('date', [Carbon::today(), Carbon::today()->addDays(7)])->get()->toArray();
        //GET WEEK WITH DATA
        for($i = 0; $i < 7;){
            $day = Carbon::today()->addDays($i);
            $week[$i]['date'] = date_format($day,'Y-m-d H:i:s');
            $week[$i]['ymd'] = date_format($day,'Ymd');
            $week[$i]['day'] = date_format($day, 'j');
            $week[$i]['day_name'] = date_format($day, 'l');
            $week[$i]['month_name'] = date_format($day, 'F');
            $week[$i]['month_name_short'] = date_format($day, 'M');
            $week[$i]['todoes'] = [];

            foreach($todoes as $key => $todo){
                $todoDateYmd = date_format(date_create($todo['date']), 'Ymd');
                //ADD TODOES BY WEEK DATE
                if($todoDateYmd === $week[$i]['ymd']) {
                    if($todo['task']['checks']){
                        //ADD CHECKS BY WEEK DATE
                        foreach($todo['task']['checks'] as $check){
                            $checkDateYmd = date_format(date_create($check['date']), 'Ymd');
                            
                            if($checkDateYmd === $week[$i]['ymd']){
                                $todo['task']['check'] = $check;
                                break;
                            }
                        }
                        unset($todo['task']['checks']);
                    }
                    array_push($week[$i]['todoes'], $todo);
                }
            }

            $i++;
        }

        return $week;
    }
    public function addTodo(Request $request){
        $array = $request->all();

        if(isset($array['id'])){
            $validator = Validator($array, [
                'date' => 'required|date_format:Y-m-d H:i:s',
                'id' => 'required|numeric'
            ]);
            if($validator->fails()){
                $response = [
                    'success' => false,
                    'errors' => $validator->errors()
                ];
                return response()->json($response, 400);
            }
            
            $isItYours = Task::where(['id'=> $array['id'], 'user_id' => Auth::id()])->first();
            
            if($isItYours){
                Todo::create(['task_id' => $array['id'], 'date' => $array['date']]);
            }
        }
        else if(isset($array['name']) && isset($array['points'])){
            $validator = Validator($array, [
                'date' => 'required|date_format:Y-m-d H:i:s',
                'name' => 'required|min:1|max:50',
                'points' => 'required|numeric|between:0,5',
            ]);
            if($validator->fails()){
                $response = [
                    'success' => false,
                    'errors' => $validator->errors()
                ];
                return response()->json($response, 400);
            }
            //IS IT YOUR THE GROUP?
            if($array['group_id']){
                $isGroupYours = Group::where(['id' => $array['group_id'], 'user_id' => Auth::id()])->first();

                if(!$isGroupYours) {
                    $response = [
                        'success' => false,
                        'errors' => 'Группа не принадлежит вам'
                    ];
                    return response()->json($response, 400);
                }
            }
            
            $task_id = Task::create(['name' => $array['name'], 
                                         'points' => $array['points'], 
                                         'group_id' => $array['group_id'], 
                                         'user_id' => Auth::id(),
                                         'state' => 1])->id;
            
            if($task_id)
                Todo::create(['task_id' => $task_id, 'date' => $array['date']]);
        }

    }
    public function deleteTodo(Request $request){
        $array['todo_id'] = $request->id;
        $validator = Validator($array, [
            'todo_id' => 'required',
        ]);
        if($validator->fails()){
            $response = [
                'success' => false,
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        //IS IT YOUR THE TODO?
        $todo_id = $array['todo_id'];
        $task = Task::where('user_id', Auth::id())
                    ->whereHas('todo', function($q) use($todo_id){
                        $q->where('id', $todo_id);
                  })->first();
        
        if($task){
            if($task['state'] == 1){
                Task::where('id', $task['id'])->delete();
                Todo::where('id', $todo_id)->delete();
            }
            else if($task['state'] == 2)
                Todo::where('id', $todo_id)->delete();
        }
    }
}