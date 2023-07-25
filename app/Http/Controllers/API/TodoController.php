<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class TodoController extends Controller
{
    public function getTodoes(Request $request){
        $user_id = Auth::id();
        $data = [];
        
        if($request['date']) $sourceDate = Carbon::createFromFormat('Y-m-d H:i:s',  $request['date']);
        else $sourceDate = Carbon::today();

        if($request['action'] == 'next'){
            $firstDay = $sourceDate->addDays(4);
            $lastDay = $sourceDate->copy()->addDays(3);
        }
        else if($request['action'] == 'last'){
            $lastDay = $sourceDate->subDays(1);
            $firstDay = $sourceDate->copy()->subDays(3);
        }
        else {
            $firstDay = $sourceDate;
            $lastDay = $sourceDate->copy()->addDays(3);
        }

        //GET TODOES WITH CHECKS BY DATE BETWEEN TODAY AND +4 DAYS
        $todoes = \Models\Todo::with('task.checks')->whereHas('task', function($q) use($user_id){
            $q->where('user_id', $user_id);
        })->whereBetween('date', [$firstDay, $lastDay])->get()->toArray();
        
        //Get 4 days with todoes and checks
        for($i = 0; $i < 4;){
            if($i) $day = $firstDay->addDays(1);
            else $day = $firstDay;

            $data[$i]['date'] = date_format($day,'Y-m-d H:i:s');
            $data[$i]['ymd'] = date_format($day,'Ymd');
            $data[$i]['day'] = date_format($day, 'j');
            $data[$i]['day_name'] = date_format($day, 'l');
            $data[$i]['month_name'] = date_format($day, 'F');
            $data[$i]['month_name_short'] = date_format($day, 'M');
            $data[$i]['todoes'] = [];

            foreach($todoes as $key => $todo){
                $todoDateYmd = date_format(date_create($todo['date']), 'Ymd');
                //ADD TODOES BY DATE
                if($todoDateYmd === $data[$i]['ymd']) {
                    if($todo['task']['checks']){
                        //ADD CHECKS BY DATE
                        foreach($todo['task']['checks'] as $check){
                            $checkDateYmd = date_format(date_create($check['date']), 'Ymd');
                            
                            if($checkDateYmd === $data[$i]['ymd']){
                                $todo['task']['check'] = $check;
                                break;
                            }
                        }
                        unset($todo['task']['checks']);
                    }
                    array_push($data[$i]['todoes'], $todo);
                }
            }

            $i++;
        }

        return $data;
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
            
            $isItYours = \Models\Task::where(['id'=> $array['id'], 'user_id' => Auth::id()])->first();
            
            if($isItYours){
                \Models\Todo::create(['task_id' => $array['id'], 'date' => $array['date']]);
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
                $isGroupYours = \Models\Group::where(['id' => $array['group_id'], 'user_id' => Auth::id()])->first();

                if(!$isGroupYours) {
                    $response = [
                        'success' => false,
                        'errors' => 'Группа не принадлежит вам'
                    ];
                    return response()->json($response, 400);
                }
            }
            
            $task_id = \Models\Task::create(['name' => $array['name'], 
                                         'points' => $array['points'], 
                                         'group_id' => $array['group_id'], 
                                         'user_id' => Auth::id(),
                                         'state' => 1])->id;
            
            if($task_id)
            \Models\Todo::create(['task_id' => $task_id, 'date' => $array['date']]);
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
        $task = \Models\Task::where('user_id', Auth::id())
                    ->whereHas('todo', function($q) use($todo_id){
                        $q->where('id', $todo_id);
                  })->first();
        
        if($task){
            if($task['state'] == 1){
                \Models\Task::where('id', $task['id'])->delete();
                \Models\Todo::where('id', $todo_id)->delete();
            }
            else if($task['state'] == 2)
            \Models\Todo::where('id', $todo_id)->delete();
        }
    }
}