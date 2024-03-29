<?php


namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;


class UserController extends Controller
{


    public $successStatus = 200;


    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        $credentials = request(['email', 'password']);

        if(Auth::attempt($credentials)){
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            return $this->sendResponse(['success' => $success], 'login successfull');
        }
        else{
//            return response()->json(['error'=>'Unauthorised'], 401);
            return $this->sendError('login password or email did not match');

        }
    }


    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);


        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }


        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;


//        return response()->json(['success'=>$success], $this->successStatus);
        return $this->sendResponse(['success'=>$success], 'Registration successfully done');
    }


    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function details()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this->successStatus);
    }
}