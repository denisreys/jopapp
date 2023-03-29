<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Affair;
use App\Models\Diary;
use App\Models\Group;
use Illuminate\Support\Facades\Auth;


class DiaryController extends Controller
{
    
    public function getDiary(){
        $user_id = Auth::id();
        $week = [];

        $diaries = Diary::with('affair.checks')->whereHas('affair', function($q) use($user_id){
            $q->where('user_id', $user_id);
        })->whereBetween('date', [Carbon::today(), Carbon::today()->addDays(7)])->get()->toArray();
        
        for($i = 0; $i < 7;){
            $day = Carbon::today()->addDays($i);
            $week[$i]['date'] = date_format($day,'Y-m-d H:i:s');
            $week[$i]['ymd'] = date_format($day,'Ymd');
            $week[$i]['day'] = date_format($day, 'j');
            $week[$i]['day_name'] = date_format($day, 'l');
            $week[$i]['month_name'] = date_format($day, 'F');
            $week[$i]['month_name_short'] = date_format($day, 'M');
            $week[$i]['todoes'] = [];

            foreach($diaries as $key => $diary){
                $diaryDateYmd = date_format(date_create($diary['date']), 'Ymd');
                
                if($diaryDateYmd === $week[$i]['ymd']) {
                    if($diary['affair']['checks']){
                        foreach($diary['affair']['checks'] as $check){
                            $checkDateYmd = date_format(date_create($check['date']), 'Ymd');
                            
                            if($checkDateYmd === $week[$i]['ymd']){
                                $diary['affair']['check'] = $check;
                                break;
                            }
                        }
                        unset($diary['affair']['checks']);
                    }
                    array_push($week[$i]['todoes'], $diary);
                }
            }

            $i++;
        }

        return $week;
    }
    public function addTodo(Request $request){
        $array = $request->all();
        $array['user_id'] = Auth::id();

        if(isset($array['id'])){
            $validator = Validator($array, [
                'date' => 'required|date_format:Y-m-d H:i:s',
                'id' => 'required|numeric',
                'user_id' => 'required'
            ]);
            if($validator->fails()){
                $response = [
                    'success' => false,
                    'errors' => $validator->errors()
                ];
                return response()->json($response, 400);
            }
            
            $isItYours = Affair::where(['id'=> $array['id'], 'user_id' => $array['user_id']])->first();
            
            if($isItYours){
                Diary::create(['affair_id' => $array['id'], 'date' => $array['date']]);
            }
        }
        else if(isset($array['name']) && isset($array['points'])){
            $validator = Validator($array, [
                'user_id' => 'required',
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

            if($array['group_id']){
                $isGroupYours = Group::where(['id' => $array['group_id'], 'user_id' => $array['user_id']])->first();

                if(!$isGroupYours) {
                    $response = [
                        'success' => false,
                        'errors' => 'Группа не принадлежит вам'
                    ];
                    return response()->json($response, 400);
                }
            }
            
            $affair_id = Affair::create(['name' => $array['name'], 
                                         'points' => $array['points'], 
                                         'group_id' => $array['group_id'], 
                                         'user_id' => $array['user_id'],
                                         'state' => 1])->id;
            
            if($affair_id)
                Diary::create(['affair_id' => $affair_id, 'date' => $array['date']]);
        }

    }
    public function deleteTodo(Request $request){
        $array['user_id'] = Auth::id();
        $array['diary_id'] = $request->id;
        $diary_id = $array['diary_id'];

        $validator = Validator($array, [
            'user_id' => 'required',
            'diary_id' => 'required',
        ]);
        if($validator->fails()){
            $response = [
                'success' => false,
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }

        $affair = Affair::where('user_id', $array['user_id'])
            ->whereHas('diary', function($q) use($diary_id){
                $q->where('id', $diary_id);
            })->first();
        
        if($affair){
            if($affair['state'] == 1){
                Affair::where('id', $affair['id'])->delete();
                Diary::where('id', $diary_id)->delete();
            }
            else if($affair['state'] == 2)
                Diary::where('id', $diary_id)->delete();
        }
    }
}
