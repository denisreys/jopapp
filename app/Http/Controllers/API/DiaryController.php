<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Affair;
use App\Models\Diary;
use Illuminate\Support\Facades\Auth;

class DiaryController extends Controller
{
    public function getDiary(){
        $user_id = Auth::id();
        $week = [];

        $todoes = Diary::with('affair.checks')->whereHas('affair', function($q) use($user_id){
            $q->where('user_id', $user_id);
        })->whereBetween('date', [Carbon::today(), Carbon::today()->addDays(7)])->get();

        for($i = 0; $i < 7;){
            $day = Carbon::today()->addDays($i);
            $week[$i]['date'] = date_format($day,'Y-m-d H:i:s');
            $week[$i]['day'] = date_format($day, 'j');
            $week[$i]['day_name'] = date_format($day, 'l');
            $week[$i]['month_name'] = date_format($day, 'F');
            $week[$i]['month_name_short'] = date_format($day, 'M');
            $week[$i]['todoes'] = [];

            foreach($todoes as $todo){
                $todoDateYmd = date_format(date_create($todo['date']), 'Ymd');
                $dayDateYmd = date_format($day, 'Ymd');

                if($todo['affair']['checks']){
                    foreach($todo['affair']['checks'] as $check){
                        if(date_format(date_create($check['date']), 'Ymd') === $todoDateYmd){
                            $todo['affair']['check'] = $check;
                            break;
                        }
                    }
                    unset($todo['affair']['checks']);
                }
                if($todoDateYmd === $dayDateYmd){
                    array_push($week[$i]['todoes'], $todo['affair']);
                }
            }

            $i++;
        }

        return $week;
    }
}
