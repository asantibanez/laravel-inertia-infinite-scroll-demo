<?php

use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['inertia'])
    ->group(function () {
        Route::get('/feed', function () {
            ray(request('hello'));
            return Inertia::render('Feed/Show', [
                'comments' => CommentResource::collection(
                    Comment::query()->orderByDesc('id')->paginate(20),
                ),
                'updated_comment' => request()->has('updated_comment_id')
                    ? CommentResource::make(Comment::find(request('updated_comment_id')))
                    : null
                ,
            ]);
        })->name('feed');

        Route::post('/comments/{commentId}/like', function () {
            $comment = Comment::find(request('commentId'));
            $comment->liked = true;
            $comment->save();

            $backUrl = route('feed', [
                'page' => request('current_page'),
                'updated_comment_id' => $comment->id,
            ]);

            return redirect()->to($backUrl);
        });

        Route::delete('/comments/{commentId}/like', function () {
            $comment = Comment::find(request('commentId'));
            $comment->liked = false;
            $comment->save();

            $backUrl = route('feed', [
                'page' => request('current_page'),
                'updated_comment_id' => $comment->id,
            ]);

            return redirect()->to($backUrl);
        });
    });
