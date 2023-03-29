<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Note;

class NoteController extends Controller
{
    public function getNotes(){
        $user_id = Auth::id();
        $notes = Note::where('user_id', $user_id)->orderBy('id', 'DESC')->get();

        if($notes) return $notes;
    }
    public function saveNote(Request $request){
        $request = $request->all();
        $request['user_id'] = Auth::id();
        $validator = Validator($request, [
            'text' => 'min:1|max:100',
            'user_id' => 'required'
        ]);
        if($validator->fails()) {
            $response = [
                'success' => false,
                'errors' => $validator->errors()
            ];
            return response()->json($response, 400);
        }
        
        if($request['id'])
            Note::where(['id' => $request['id'], 'user_id' => $request['user_id']])->update(['text' => $request['text']]);
        else 
            Note::create(['text' => $request['text'], 'user_id' => $request['user_id']]);
    }
    public function deleteNote(Request $request){
        $user_id = Auth::id();

        if($request['note_id'] && $user_id)
            return Note::where(['id' => $request['note_id'], 'user_id' => $user_id])->delete();
    }
}
