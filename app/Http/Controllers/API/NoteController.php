<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function getNotes(){
        $notes = \Models\Note::where('user_id', Auth::id())->orderBy('id', 'DESC')->get();

        if($notes) return $notes;
    }
    public function saveNote(Request $request){
        $request = $request->all();
        $validator = Validator($request, [
            'text' => 'min:1|max:100',
        ]);
        if($validator->fails()) {
            $response = [
                'success' => false,
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        
        if($request['id'])
        \Models\Note::where(['id' => $request['id'], 'user_id' => Auth::id()])->update(['text' => $request['text']]);
        else 
        \Models\Note::create(['text' => $request['text'], 'user_id' => Auth::id()]);
    }
    public function deleteNote(Request $request){
        if($request['note_id'] && Auth::id())
            return \Models\Note::where(['id' => $request['note_id'], 'user_id' => Auth::id()])->delete();
    }
}
