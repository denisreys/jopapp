<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Affair;
use App\Models\Diary;
use App\Models\Check;
use App\Models\Group;

class StatisticsController extends Controller
{
    public function totalUpdate(){
        $user_id = Auth::id();
        $dateFrom = Carbon::now()->subDays(30);
        $dateTo = Carbon::now();

        $checks = Check::whereHas('affair', function($q) use($user_id){
                                    $q->where('user_id', $user_id);
                                })->whereBetween('date', [$dateFrom, $dateTo])
                                ->with('affair.group')->get();
        $diaries = Diary::with('affair.checks')->whereHas('affair', function($q) use($user_id){
            $q->where('user_id', $user_id);
        })->get();                        

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
                    $stat['diagram'][0]['color'] = 'f1f1f1';
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

    function getBasicCalendarDates(){
        $user_id = Auth::id();
        $data['month'] = date('n');
        $data['year'] = date('Y');
        $data['month_name'] = date('F');
        $data['today'] = date('j');

        $data['checks'] = Check::whereHas('affair', function($q) use($user_id) {
                        $q->where(['user_id' => $user_id]);
                        $q->where('state', '!=' , 3);
                     })
                     ->orderBy('date', 'ASC')
                     ->get();
        $diaries = Diary::with('affair.checks')->whereHas('affair', function($q) use($user_id){
            $q->where('user_id', $user_id);
        })->get();

        if($data['checks']) $start = strtotime('1-1-'.Carbon::parse($data['checks']['0']['date'])->format('Y'));
        else $start = strtotime('this month');
        
        $finish = strtotime('Dec 31');

        for($i=$start; $i<$finish; $i+=86400){
            list($year,$month,$day) = explode("|",date("Y|n|j",$i));
            $data['calendar'][$year]['months'][$month]['year'] = $year;
            $data['calendar'][$year]['months'][$month]['month'] = $month;
            $data['calendar'][$year]['months'][$month]['month_name'] = date("F",$i);
            $data['calendar'][$year]['months'][$month]['month_name_short'] = date("M",$i);
            $data['calendar'][$year]['months'][$month]['days'][$day]['day'] = $day;
            $data['calendar'][$year]['points'] = 0;
            $data['calendar'][$year]['months'][$month]['points'] = 0;
            $data['calendar'][$year]['months'][$month]['days'][$day]['points'] = 0;

            if($day == '1') $data['calendar'][$year]['months'][$month]['offset'] = date('N', $i) - 1;
        }

        return $data;
    }
    function addPointsToCalendar($data){
        if($data['checks']){
            foreach($data['checks'] as $check){
                $date = Carbon::parse($check['date']);
                $year = $date->format('Y');
                $month = $date->format('n');
                $day = $date->format('j');

                $data['calendar'][$year]['points'] += $check['points'];
                $data['calendar'][$year]['months'][$month]['points'] += $check['points'];
                $data['calendar'][$year]['months'][$month]['days'][$day]['points'] += $check['points'];
            }
        }

        return $data;
    }
    public function addDiaryToCalendar($data){
        $user_id = Auth::id();
        $data['diaries'] = Diary::with('affair.checks')->whereHas('affair', function($q) use($user_id){
            $q->where('user_id', $user_id);
        })->whereDate('date', '<', Carbon::today())->get();

        if($data['diaries']){
            foreach($data['diaries'] as $diary){
                $date = Carbon::parse($diary['date']);
                $year = $date->format('Y');
                $month = $date->format('n');
                $day = $date->format('j');

                //if(isset($data['calendar'][$year]['diaries_count'])) 
                //    $data['calendar'][$year]['diaries_count']++;
                //else $data['calendar'][$year]['diaries_count'] = 0;
                //$data['calendar'][$year]['months'][$month]['points'] += $check['points'];
                $data['calendar'][$year]['months'][$month]['days'][$day]['diaries'][] = $diary;
            }
        }
        if($data['checks']){
            foreach($data['checks'] as $check){
                $date = Carbon::parse($check['date']);
                $year = $date->format('Y');
                $month = $date->format('n');
                $day = $date->format('j');

                if(isset($data['calendar'][$year]['months'][$month]['days'][$day]['diaries'])){
                    foreach($data['calendar'][$year]['months'][$month]['days'][$day]['diaries'] as $diary){
                        if($diary['affair_id'] == $check['affair_id']){
                            $data['calendar'][$year]['months'][$month]['days'][$day]['diary_checks'][] = $check;
                        }
                    }
                }
                
                //$data['calendar'][$year]['months'][$month]['days'][$day]['diaries'] += $check['points'];
            }
        }

        return $data;
    }

    public function getdDashboardCalendar(){
        $user_id = Auth::id();
        $data['month'] = date('n');
        $data['year'] = date('Y');
        $data['month_name'] = date('F');
        $data['today'] = date('j');

        $data['checks'] = Check::whereHas('affair', function($q) use($user_id) {
                        $q->where(['user_id' => $user_id]);
                        $q->where('state', '!=' , 3);
                     })
                     ->orderBy('date', 'ASC')
                     ->get();
        $diaries = Diary::with('affair.checks')->whereHas('affair', function($q) use($user_id){
            $q->where('user_id', $user_id);
        })->get();

        if($data['checks']) $start = strtotime('1-1-'.Carbon::parse($data['checks']['0']['date'])->format('Y'));
        else $start = strtotime('this month');
        
        $finish = strtotime('Dec 31');

        for($i=$start; $i<$finish; $i+=86400){
            list($year,$month,$day) = explode("|",date("Y|n|j",$i));
            $data['calendar'][$year]['months'][$month]['year'] = $year;
            $data['calendar'][$year]['months'][$month]['month'] = $month;
            $data['calendar'][$year]['months'][$month]['month_name'] = date("F",$i);
            $data['calendar'][$year]['months'][$month]['month_name_short'] = date("M",$i);
            $data['calendar'][$year]['months'][$month]['days'][$day]['day'] = $day;
            $data['calendar'][$year]['months'][$month]['days'][$day]['points'] = 0;
            $data['calendar'][$year]['points'] = 0;
            $data['calendar'][$year]['months'][$month]['points'] = 0;

            if($day == '1') $data['calendar'][$year]['months'][$month]['offset'] = date('N', $i) - 1;
        }

        if($diaries){

        }
        if($data['checks']){
            foreach($data['checks'] as $check){
                $date = Carbon::parse($check['date']);
                $year = $date->format('Y');
                $month = $date->format('n');
                $day = $date->format('j');

                $data['calendar'][$year]['points'] += $check['points'];
                $data['calendar'][$year]['months'][$month]['points'] += $check['points'];
                $data['calendar'][$year]['months'][$month]['days'][$day]['points'] += $check['points'];
            }
        }
        
        return $data;
    }
    public function getDashboardCalendar(){
        $data = $this->getBasicCalendarDates();
        $data = $this->addPointsToCalendar($data);

        return $data;
    }
    public function getStatistics(){
        $data = $this->getBasicCalendarDates();
        $data = $this->addPointsToCalendar($data);
        $data = $this->addDiaryToCalendar($data);

        return $data;
    }
}
