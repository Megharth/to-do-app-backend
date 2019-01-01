<?php

namespace App\Http\Controllers;

use App\Note;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class NoteController extends Controller
{
    public function createNote(Request $request) {
        $note = new Note;
        $note->title = $request['title'];
        $note->content = $request['content'];
        try {
            if(! $user = JWTAuth::parseToken()->authenticate())
                return response()->json(["error"=>"unauthorized"], 403);
        } catch (JWTException $e) {
            return response()->json($e);
        }

        $user->notes()->save($note);

        return response()->json(['message'=>'post created successfully'], 200);
    }

    public function getNotes() {
        try {
            if(! $user = JWTAuth::parseToken()->authenticate())
                return response()->json(["error"=>"unauthorized"], 403);
        } catch (JWTException $e) {
            return response()->json($e);
        }

        $notes = $user->notes;
        return response()->json(compact('notes'));
    }

    public function deleteNote($id) {
        try {
            if(! $currentUser = JWTAuth::parseToken()->authenticate())
                return response()->json(["error"=>"unauthorized"], 403);
        } catch (JWTException $e) {
            return response()->json($e);
        }

        $note = Note::find($id);
        if($note->user != $currentUser)
            return response()->json(['error'=>'unauthorized'], 403);
        else{
            $note->delete();
            return response()->json(['message'=>'post deleted successfully!']);
        }
    }

    public function updateNote(Request $request) {
        try {
            if(! $currentUser = JWTAuth::parseToken()->authenticate())
                return response()->json(["error"=>"unauthorized"], 403);
        } catch (JWTException $e) {
            return response()->json($e);
        }
        $note = Note::find($request->id)->first();

        if($note->user != $currentUser)
            return response()->json(['error'=>'unauthorized'], 403);
        else {
            if($request['title'])
                $note->title = $request['title'];
            if($request['content'])
                $note->content = $request['content'];
            if($request['image_name'])
                $note->image_name = $request['image_name'];
            $note->update();
            return response()->json(['message'=>'Note updated Successfully!']);
        }
    }
}
