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
        
        $affair = Affair::where([
            'id' => $array['affair_id'], 
            'user_id' => $array['user_id']
        ])->first();
        
        if($affair){
            if($array['status']){
                Check::create(['affair_id' => $affair['id'], 'points' => $affair['points'], 'date' => $array['check_date']]);
            }
            else {
                if($array['check_date']){
                    if($affair['state'] == 3){
                        Check::where('affair_id', $affair['id'])
                                ->delete();
                    }else {
                        Check::where('affair_id', $affair['id'])
                            ->whereDay('date', $array['check_date'])
                            ->delete();
                    }
                }
                else {
                    Check::where('affair_id', $affair['id'])
                    ->whereDay('date', Carbon::today())
                    ->delete();
                }    
            }
        }
        return $array;
    }
}
