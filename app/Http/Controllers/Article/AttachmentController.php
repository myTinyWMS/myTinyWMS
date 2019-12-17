<?php

namespace Mss\Http\Controllers\Article;

use Mss\Models\Article;
use Webpatser\Uuid\Uuid;
use Illuminate\Http\Request;
use Mss\Http\Controllers\Controller;

class AttachmentController extends Controller
{
    public function upload(Article $article, Request $request) {
        $file = $request->file('file');
        $filename = $article->id.'_'.Uuid::generate(4)->string;
        $upload_success = $file->storeAs('article_files', $filename);
        if ($upload_success) {
            $files = $article->files;
            $files[] = [
                'orgName' => $file->getClientOriginalName(),
                'mimeType' => $file->getClientMimeType(),
                'storageName' => $filename
            ];
            $article->files = $files;
            $article->save();
            return response()->json($upload_success, 200);
        } else {
            return response()->json('error', 400);
        }
    }

    public function download(Article $article, $file) {
        $attachment = $article->files[$file];
        return response()->download(storage_path('app/article_files/'.$attachment['storageName']), $attachment['orgName'], ['Content-Type' => $attachment['mimeType']]);
    }

    public function delete(Article $article, $file) {
        $files = $article->files;
        $attachment = $files[$file];
        unset($files[$file]);

        $article->files = array_values($files);
        $article->save();

        @unlink(storage_path('app/article_files/'.$attachment['storageName']));

        flash('Datei gelöscht')->success();

        return response()->redirectToRoute('article.show', [$article]);
    }
}
