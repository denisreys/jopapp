<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Affair;
use App\Models\Check;
use App\Models\Group;

class StatisticsController extends Controller
{
    public function statListUpdate(){
        $user_id = Auth::id();
        $dateFrom = Carbon::now()->subDays(30);
        $dateTo = Carbon::now();

        $checks = Check::whereHas('affair', function($q) use($user_id){
                                    $q->where('user_id', $user_id);
                                })->whereBetween('date', [$dateFrom, $dateTo])
                                ->with('affair.group')->get();

        $stat['total'] = 0;
        
        $stat['diagram'] = [];

        foreach($checks as $check){
            $stat['total'] += $check['points'];
            
            if($check['affair']['state'] == 3){
                if(!isset($stat['diagram']['task'])){
                    $stat['diagram']['task']['points'] = 0;
                    $stat['diagram']['task']['id'] = 'task';
                    $stat['diagram']['task']['name'] = 'Tasks';
                    $stat['diagram']['task']['color'] = 'FFD700';
                }
                $stat['diagram']['task']['points'] += $check['points'];
            }
            else if($check['affair']['group_id']){
                if(!isset($stat['diagram'][$check['affair']['group_id']])){
                    $stat['diagram'][$check['affair']['group_id']]['points'] = 0;
                    $stat['diagram'][$check['affair']['group_id']]['id'] = $check['affair']['group_id'];
                    $stat['diagram'][$check['affair']['group_id']]['name'] = $check['affair']['group']['name'];
                    $stat['diagram'][$check['affair']['group_id']]['color'] = $check['affair']['group']['color'];
                }
                $stat['diagram'][$check['affair']['group_id']]['points'] += $check['points'];
            }
            else {
                if(!isset($stat['diagram'][0])){
                    $stat['diagram'][0]['points'] = 0;
                    $stat['diagram'][0]['id'] = 0;
                    $stat['diagram'][0]['name'] = 'Other';
                    $stat['diagram'][0]['color'] = 'f8f8f8';
                }
                $stat['diagram'][0]['points'] += $check['points'];
            }
        }
        //WIDTH THE LINES
        foreach($stat['diagram'] as $key => $group)
            $stat['diagram'][$key]['width'] = round(($group['points']/$stat['total'])*100, 2);
        
        usort($stat['diagram'], function($a,$b){
            return ($b['points'] - $a['points']);
        });
  

        return $stat;
    }
}
