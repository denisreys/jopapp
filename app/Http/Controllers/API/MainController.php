<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\Affair;
use App\Models\Check;

class MainController extends Controller
{
    public function getCalendar(){
        $user_id = Auth::id();
        $data['month'] = date('n');
        $data['year'] = date('Y');
        $data['month_name'] = date('F');
        $data['today'] = date('j');

        $checks = Check::whereHas('affair', function($q) use($user_id) {
                        $q->where(['user_id' => $user_id]);
                        $q->where('state', '!=' , 3);
                     })
                     ->orderBy('date', 'ASC')
                     ->get();

        if($checks) $start = strtotime('1 '.Carbon::parse($checks['0']['date'])->format('M Y'));
        else $start = strtotime('this month');
        
        $finish = strtotime('Dec 31');

        $calendar = array();
            for($i=$start; $i<$finish; $i+=86400){
            list($year,$month,$day) = explode("|",date("Y|n|j",$i));
            $data['calendar'][$year][$month]['year'] = $year;
            $data['calendar'][$year][$month]['month'] = $month;
            $data['calendar'][$year][$month]['month_name'] = date("F",$i);
            $data['calendar'][$year][$month]['days'][$day]['day'] = $day;
            $data['calendar'][$year][$month]['days'][$day]['points'] = 0;

            if($day == '1') $data['calendar'][$year][$month]['offset'] = date('N', $i) - 1;
        }

        if($checks){
            foreach($checks as $check){
                $year = Carbon::parse($check['date'])->format('Y');
                $month = Carbon::parse($check['date'])->format('n');
                $day = Carbon::parse($check['date'])->format('d');
    
                $data['calendar'][$year][$month]['days'][$day]['points'] += $check['points'];
            }
        }

        return $data;
    }
}
