<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    public function register(Request $request){
        $request = $request->all();
        $thisdate = Carbon::today();
        $thistime = Carbon::now();

        $validator = Validator($request, [
            'login' => 'required|regex:/^[a-zA-Z0-9_]+$/u|min:2|max:20|unique:users,login',
            'password' => 'required|min:2|max:100'
        ],['login.regex' => 'Login must contain only latin letters, numbers and "_".'],);
        
        if($validator->fails()) {
            $response = [
                'success' => false,
                'errors' => $validator->errors()
            ];
            return response()->json($response, 200);
        }

        $user = User::create(['login' => $request['login'], 'password' => bcrypt($request['password'])]);

        if(Auth::attempt(['login' => $request['login'], 'password'=> $request['password'] ], true)){
            $response = [
                'success' => true,
                'token' => $user->createToken('jopapp')->plainTextToken
            ];
            //ADD DEFAULT GROUPS AND REPEATABLE TASKS
            $groupEducation = \Models\Group::create(['name' => 'Образование', 'icon' => 'book', 'color' => 'aad8ee', 'user_id' => $user->id, 'active' => 1]);
            $groupSport = \Models\Group::create(['name' => 'Спорт', 'icon' => 'dumpbell', 'color' => 'aaeeb2', 'user_id' => $user->id, 'active' => 1]);
            $groupHobby = \Models\Group::create(['name' => 'Хобби', 'icon' => 'lightbulb-on', 'color' => 'd6a2ed', 'user_id' => $user->id, 'active' => 1]);
            
            \Models\Task::insert([
                [
                    'name'=> 'Пройти урок по английскому',
                    'points'=> 3,
                    'group_id'=> $groupEducation->id,
                    'user_id' => $user->id,
                    'state' => 2,
                    'active' => 1,
                ],
                [
                    'name'=> 'Прочесть главу той самой книги',
                    'points'=> 2,
                    'group_id'=> $groupEducation->id,
                    'user_id' => $user->id,
                    'state' => 2,
                    'active' => 1,
                ],
                [
                    'name'=> 'Сделать зарядку',
                    'points'=> 2,
                    'group_id'=> $groupSport->id,
                    'user_id' => $user->id,
                    'state' => 2,
                    'active' => 1,
                ],
                [
                    'name'=> 'Выйти на пробежку(и побегать)',
                    'points'=> 4,
                    'group_id'=> $groupSport->id,
                    'user_id' => $user->id,
                    'state' => 2,
                    'active' => 1,
                ],
                [
                    'name'=> 'Сделать 15 перекатышей',
                    'points'=> 5,
                    'group_id'=> $groupSport->id,
                    'user_id' => $user->id,
                    'state' => 2,
                    'active' => 1,
                ],
                [
                    'name'=> 'Практиковать игру на укулеле',
                    'points'=> 3,
                    'group_id'=> $groupHobby->id,
                    'user_id' => $user->id,
                    'state' => 2,
                    'active' => 1,
                ],
                [
                    'name'=> 'Приготовить что-то необычное',
                    'points'=> 2,
                    'group_id'=> $groupHobby->id,
                    'user_id' => $user->id,
                    'state' => 2,
                    'active' => 1,
                ],
            ]);
            //ADD TASK DELETE DEFAULT TASKS AND GROUPS AND ADD TODO ON TODAY
            $taskDeleteDefault = \Models\Task::create([
                'name'=> 'Добавить свои группы и таски и удалить ненужные',
                'points'=> 1,
                'user_id' => $user->id,
                'state' => 1,
                'active' => 1,
            ]);
            \Models\Todo::create(['task_id' => $taskDeleteDefault->id, 'date' => $thisdate]);

            //TARGET CREATED ACCOUNT AND ADD CHECK
            $targetCreated = \Models\Task::create([
                'name'=> 'Создать аккаунт на Jopapp',
                'points'=> 10,
                'user_id' => $user->id,
                'state' => 3,
                'active' => 1,
            ]);
            \Models\Check::create(['task_id' => $targetCreated->id, 'points' => 10, 'date' => $thistime]);
            
            //
            \Models\Note::insert([
                ['text' => 'Сохрани на enter или просто продолжи пользоваться сайтом', 'user_id' => $user->id],
                ['text' => 'Нажми на текст заметки, если хочешь ее изменить', 'user_id' => $user->id],
                ['text' => 'Выполненный таргет пропадет через 7 дней', 'user_id' => $user->id],
            ]);

            return response()->json($response,200);
        }
        else {
            $response = [
                'success' => true,
                'token' => false
            ];
            return response()->json($response,200);
        }
    }
    public function login(Request $request){
        $validator = Validator($request->all(), [
            'login' => 'required|min:2|max:20',
            'password' => 'required' 
        ]);
        if(Auth::attempt(['login' => $request->login,'password'=>$request->password], true)){
            $user = Auth::user();
            
            $success['token'] = $user->createToken('jopapp')->plainTextToken;
            $success['login'] = $user->login;

            $response = [
                'success' => true,
                'data' => $success
            ];
            return response()->json($response,200);
        }
        else {
            $response = [
                'success' => false,
                'message' => 'Login or password are wrong'
            ];
            return response()->json($response,200);
        }
    }
}
