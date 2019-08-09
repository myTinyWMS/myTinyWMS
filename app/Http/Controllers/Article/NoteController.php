<?php

namespace Mss\Http\Controllers\Article;

use Mss\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mss\Http\Controllers\Controller;

class NoteController extends Controller
{
    public function store(Article $article, Request $request) {
        $article->articleNotes()->create([
            'content' => $request->get('content'),
            'user_id' => Auth::id()
        ]);

        flash('Notiz gespeichert')->success();

        return redirect()->route('article.show', $article);
    }

    public function delete(Article $article, $note, Request $request) {
        $article->articleNotes()->where('id', $note)->delete();

        flash('Notiz gelöscht')->success();

        return redirect()->route('article.show', $article);
    }
}
