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
            return Inertia::render('Feed/Show', [
                'comments' => CommentResource::collection(
                    Comment::query()->orderByDesc('id')->paginate(20),
                ),
            ]);
        });
    });
