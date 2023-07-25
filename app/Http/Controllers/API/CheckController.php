<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
//use App\Models\Task;
use App\Models\Check;

class CheckController extends Controller
{
    public function createOrUpdateCheck(Request $request){
        $array['user_id'] = Auth::id();
        $array['task_id'] = $request['task_id'];
        $array['status'] = $request['status'];

        if(!$request['date']) $array['check_date'] = Carbon::now();
        else $array['check_date'] = date_create($request['date']);
        
        $task = \Models\Task::where([
            'id' => $array['task_id'], 
            'user_id' => $array['user_id']
        ])->first();
        
        if($task){
            if($array['status']){
                \Models\Check::create(['task_id' => $task['id'], 'points' => $task['points'], 'date' => $array['check_date']]);
            }
            else {
                if($array['check_date']){
                    if($task['state'] == 3){
                        \Models\Check::where('task_id', $task['id'])
                                ->delete();
                    }else {
                        \Models\Check::where('task_id', $task['id'])
                            ->whereDay('date', $array['check_date'])
                            ->delete();
                    }
                }
                else {
                    \Models\Check::where('task_id', $task['id'])
                    ->whereDay('date', Carbon::today())
                    ->delete();
                }    
            }
        }
        return $array;
    }
}
