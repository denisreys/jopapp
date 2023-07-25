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
use App\Http\Controllers\API\TaskController;

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



//AUTH
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login'])->name('login');
//DASHBOARD
    //groups
    Route::post('creategroup', [GroupController::class, 'createGroup']);
    Route::post('editgroup', [GroupController::class, 'editGroup']);
    Route::post('deletegroup', [GroupController::class, 'deleteGroup']);
    Route::post('getgroups', [GroupController::class, 'getGroups']);

    //tasks
    Route::post('createtask', [TaskController::class, 'createTask']);
    Route::post('edittask', [TaskController::class, 'editTask']);
    Route::post('deletetask', [TaskController::class, 'deleteTask']);

    //checks
    Route::post('createorupdatecheck', [CheckController::class, 'createOrUpdateCheck']);

    //todoes
    Route::post('addtodo', [TodoController::class, 'addTodo']);
    Route::post('deletetodo', [TodoController::class, 'deleteTodo']);
    Route::post('gettodoes', [TodoController::class, 'getTodoes']);

    //notes
    Route::post('savenote', [NoteController::class, 'saveNote']);
    Route::post('deletenote', [NoteController::class, 'deleteNote']);
    Route::post('getnotes', [NoteController::class, 'getNotes']);

    //targets
    Route::post('addtarget', [TargetController::class, 'addTarget']);
    Route::post('edittarget', [TargetController::class, 'editTarget']);
    Route::post('deletetarget', [TargetController::class, 'deleteTarget']);
    Route::post('gettargets', [TargetController::class, 'getTargets']);

//STATS
    Route::post('getstatistics', [StatisticsController::class, 'getStatistics']);
    Route::post('getdashboardstats', [StatisticsController::class, 'getDashboardStats']);