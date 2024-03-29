<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class StatisticsController extends Controller
{
    public function getStatistics(Request $request){
        $request = $request->all();
        $page = $request['page'];
        $user_id = Auth::id();
        
        $data['month'] = date('n');
        $data['year'] = date('Y');
        $data['month_name'] = date('F');
        $data['today'] = date('j');
        $data['request'] = $request;
        $data['checks'] = \Models\Check::whereHas('task', function($q) use($user_id) {
                                $q->where(['user_id' => $user_id]);
                                $q->where('state', '!=' , 3);
                            })
                            ->with('task.group')
                            ->orderBy('date', 'ASC')
                            ->get();
        //CALENDAR STARTS 1 JAN *YEAR WHEN YOU DO YOUR FIRST CHECK                                    
        if(count($data['checks']))  
            $start = strtotime('1-1-'.Carbon::parse($data['checks']['0']['date'])->format('Y'));
        else $start = strtotime(date('Y-m-01'));        
        $finish = strtotime('Dec 31');
        //TO FORM THE CALENDAR
        $dateFor = ['year'=> 0, 'month' => 0];
        for($i=$start; $i<$finish; $i+=86400){
            list($year,$month,$day) = explode("|",date("Y|n|j",$i));
            //ADD IF THE MONTH IS NOT EQUALS $DATEFOR
            if($dateFor['month'] != $month){
                $dateFor['month'] = $month;

                $data['calendar'][$year]['months'][$month]['year'] = $year;
                $data['calendar'][$year]['months'][$month]['month'] = $month;
                $data['calendar'][$year]['months'][$month]['month_name'] = date("F",$i);
                $data['calendar'][$year]['months'][$month]['month_name_short'] = date("M",$i);
                $data['calendar'][$year]['months'][$month]['points'] = 0;
                
                //YOU KNOW
                if($dateFor['year'] != $year){
                    $dateFor['year'] = $year;
                    $data['calendar'][$year]['points'] = 0;
                }
            }
            
            $data['calendar'][$year]['months'][$month]['days'][$day]['day'] = $day;
            $data['calendar'][$year]['months'][$month]['days'][$day]['points'] = 0;
            
            if($page == 'dashboard')
                if($day == '1') $data['calendar'][$year]['months'][$month]['offset'] = date('N', $i) - 1;
        }

        if($page == 'stats') 
            $data = $this->addTodoesToCalendar($data);

        if($data['checks']){
            foreach($data['checks'] as $check){
                $date['full'] = Carbon::parse($check['date']);
                $date['year'] = $date['full']->format('Y');
                $date['month'] = $date['full']->format('n');
                $date['day'] = $date['full']->format('j');
                $date['time'] = $date['full']->format('G:i');

                $check['year'] = $date['year'];
                $check['month'] = $date['full']->format('M');
                $check['day'] = $date['day'];
                $check['time'] = $date['time'];

                $data = $this->addPointsToStats($date, $data, $check);

                if($page == 'stats') {
                    $data = $this->addTodoesChecksToStats($date, $data, $check);
                    $data = $this->addPointsByGroupsToStats($date, $data, $check);
                }
            }
        }
        return $data;
    }
    function addPointsToStats($date, $data, $check){
        $data['calendar'][$date['year']]['points'] += $check['points'];
        $data['calendar'][$date['year']]['months'][$date['month']]['points'] += $check['points'];
        $data['calendar'][$date['year']]['months'][$date['month']]['days'][$date['day']]['checks'][] = $check;
        $data['calendar'][$date['year']]['months'][$date['month']]['days'][$date['day']]['points'] += $check['points'];
        
        return $data;
    }
    function addTodoesChecksToStats($date, $data, $check){
        if(isset($data['calendar'][$date['year']]['months'][$date['month']]['days'][$date['day']]['todoes'])){
            foreach($data['calendar'][$date['year']]['months'][$date['month']]['days'][$date['day']]['todoes'] as $todo){
                if($todo['task_id'] == $check['task_id'])
                    $data['calendar'][$date['year']]['months'][$date['month']]['days'][$date['day']]['todoes_checks'][] = $check;
            }
        }
        return $data;
    }
    function addPointsByGroupsToStats($date, $data, $check){
        $group_id = $check['task']['group_id'];

        if($group_id){
            if(!isset($data['calendar'][$date['year']]['months'][$date['month']]['groups_diagram'][$group_id])){
                $data['calendar'][$date['year']]['months'][$date['month']]['groups_diagram'][$group_id]['points'] = 0;
                $data['calendar'][$date['year']]['months'][$date['month']]['groups_diagram'][$group_id]['id'] = $group_id;
                $data['calendar'][$date['year']]['months'][$date['month']]['groups_diagram'][$group_id]['name'] = $check['task']['group']['name'];
                $data['calendar'][$date['year']]['months'][$date['month']]['groups_diagram'][$group_id]['color'] = $check['task']['group']['color'];
            }
            $data['calendar'][$date['year']]['months'][$date['month']]['groups_diagram'][$group_id]['points'] += $check['points'];
        }
        else {
            if(!isset($data['calendar'][$date['year']]['months'][$date['month']]['groups_diagram'][0])){
                $data['calendar'][$date['year']]['months'][$date['month']]['groups_diagram'][0]['points'] = 0;
                $data['calendar'][$date['year']]['months'][$date['month']]['groups_diagram'][0]['id'] = 0;
                $data['calendar'][$date['year']]['months'][$date['month']]['groups_diagram'][0]['name'] = 'Other';
                $data['calendar'][$date['year']]['months'][$date['month']]['groups_diagram'][0]['color'] = 'f1f1f1';
            }
            $data['calendar'][$date['year']]['months'][$date['month']]['groups_diagram'][0]['points'] += $check['points'];
        }
  
        return $data;
    }
    function getCountPointsByGroups($dateFrom, $dateTo){
        $user_id = Auth::id();
        $response['total'] = 0;
        $response['diagram'] = [];

        $checks = \Models\Check::whereHas('task', function($q) use($user_id){
                                    $q->where('user_id', $user_id);
                                    $q->where('state', '!=' , 3);
                                })->whereBetween('date', [$dateFrom, $dateTo])
                                ->with('task.group')->get();                     

        foreach($checks as $check){
            $response['total'] += $check['points'];
            
            if($check['task']['group_id']){
                if(!isset($response['diagram'][$check['task']['group_id']])){
                    $response['diagram'][$check['task']['group_id']]['points'] = 0;
                    $response['diagram'][$check['task']['group_id']]['id'] = $check['task']['group_id'];
                    $response['diagram'][$check['task']['group_id']]['name'] = $check['task']['group']['name'];
                    $response['diagram'][$check['task']['group_id']]['color'] = $check['task']['group']['color'];
                }
                $response['diagram'][$check['task']['group_id']]['points'] += $check['points'];
            }
            else {//POINTS WITHOUT GROUP
                if(!isset($stat['diagram'][0])){
                    $response['diagram'][0]['points'] = 0;
                    $response['diagram'][0]['id'] = 0;
                    $response['diagram'][0]['name'] = 'Other';
                    $response['diagram'][0]['color'] = 'f1f1f1';
                }
                $response['diagram'][0]['points'] += $check['points'];
            }
        }
        
        usort($response['diagram'], function($a,$b){
            return ($b['points'] - $a['points']);
        });
  
        return $response;
    }
    function getDashboardStats(){
        $dateFrom = Carbon::now()->subDays(30);
        $dateTo = Carbon::now();
        
        return $this->getCountPointsByGroups($dateFrom, $dateTo);
    }
    function addTodoesToCalendar($data){
        $user_id = Auth::id();
        $data['todoes'] = \Models\Todo::with('task.checks')->whereHas('task', function($q) use($user_id){
            $q->where('user_id', $user_id);
        })->whereDate('date', '<', Carbon::now())->get();

        if($data['todoes']){
            foreach($data['todoes'] as $todo){
                $date = Carbon::parse($todo['date']);
                $year = $date->format('Y');
                $month = $date->format('n');
                $day = $date->format('j');

                $data['calendar'][$year]['months'][$month]['days'][$day]['todoes'][] = $todo;
            }
        }
        if($data['checks']){
            foreach($data['checks'] as $check){
                $date = Carbon::parse($check['date']);
                $year = $date->format('Y');
                $month = $date->format('n');
                $day = $date->format('j');

                if(isset($data['calendar'][$year]['months'][$month]['days'][$day]['todoes'])){
                    foreach($data['calendar'][$year]['months'][$month]['days'][$day]['todoes'] as $todo){
                        if($todo['task_id'] == $check['task_id'])
                            $data['calendar'][$year]['months'][$month]['days'][$day]['todoes_checks'][] = $check;
                    }
                }
            }
        }

        return $data;
    }
}