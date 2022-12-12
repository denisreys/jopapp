<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Affair;
use App\Models\Check;

class CheckController extends Controller
{
    public function createOrUpdateCheck(Request $request){
        $array['user_id'] = Auth::id();
        $array['affair_id'] = $request['affair_id'];
        $array['status'] = $request['status'];

        if(!$request['date']) $array['check_date'] = Carbon::now();
        else $array['check_date'] = date_create($request['date']);
        
        $isThisAffairsYours = Affair::where([
            'id' => $array['affair_id'], 
            'user_id' => $array['user_id']
        ])->first();
        
        if($isThisAffairsYours){
            if($array['status']){
                Check::create(['affair_id' => $array['affair_id'], 'date' => $array['check_date']]);
            }
            else {
                if($array['check_date']){
                    Check::where('affair_id', $array['affair_id'])
                        ->whereDay('date', $array['check_date'])
                        ->delete();
                }
                else {
                    Check::where('affair_id', $array['affair_id'])
                        ->whereDay('date', Carbon::today())
                        ->delete();
                }    
            }
        }
        return $array;
    }
}
