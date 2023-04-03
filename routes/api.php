<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GroupController;
use App\Http\Controllers\API\CheckController;
use App\Http\Controllers\API\MainController;
use App\Http\Controllers\API\StatisticsController;
use App\Http\Controllers\API\TodoController;
use App\Http\Controllers\API\NoteController;
use App\Http\Controllers\API\TargetController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::post('creategroup', [GroupController::class, 'createGroup']);
Route::post('editgroup', [GroupController::class, 'editGroup']);
Route::post('deletegroup', [GroupController::class, 'deleteGroup']);

Route::get('getgroups', [GroupController::class, 'getGroups']);
Route::post('createtask', [GroupController::class, 'createTask']);
Route::post('edittask', [GroupController::class, 'editTask']);
Route::post('deletetask', [GroupController::class, 'deleteTask']);

Route::post('createorupdatecheck', [CheckController::class, 'createOrUpdateCheck']);

Route::post('addtodo', [TodoController::class, 'addTodo']);
Route::post('deletetodo', [TodoController::class, 'deleteTodo']);
Route::get('gettodoes', [TodoController::class, 'getTodoes']);

Route::post('savenote', [NoteController::class, 'saveNote']);
Route::post('deletenote', [NoteController::class, 'deleteNote']);
Route::get('getnotes', [NoteController::class, 'getNotes']);

Route::get('getdashboardstats', [StatisticsController::class, 'getDashboardStats']);
Route::get('getdashboardcalendar', [StatisticsController::class, 'getDashboardCalendar']);

Route::post('getstatistics', [StatisticsController::class, 'getStatistics']);

Route::post('addtarget', [TargetController::class, 'addTarget']);
Route::post('edittarget', [TargetController::class, 'editTarget']);
Route::post('deletetarget', [TargetController::class, 'deleteTarget']);
Route::get('gettargets', [TargetController::class, 'getTargets']);