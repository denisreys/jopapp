<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Note;

class NoteController extends Controller
{
    public function getNotes(){
        $notes = Note::where('user_id', Auth::id())->orderBy('id', 'DESC')->get();

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
            Note::where(['id' => $request['id'], 'user_id' => Auth::id()])->update(['text' => $request['text']]);
        else 
            Note::create(['text' => $request['text'], 'user_id' => Auth::id()]);
    }
    public function deleteNote(Request $request){
        if($request['note_id'] && Auth::id())
            return Note::where(['id' => $request['note_id'], 'user_id' => Auth::id()])->delete();
    }
}
