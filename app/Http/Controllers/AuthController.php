<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
	public function register(Request $request){

		$userData=$request->input();
// dd($userData);
// print_r($userData);
		$rules = [
			'name' => 'required|string|max:255',
			'email' => 'required|string|email|unique:users,email',
			'password' => 'required|string|min:6|confirmed',
		];
		

		$validator = Validator::make($userData, $rules);
		if ($validator->fails()) {



			return response()->json([
					'errors'=>[
					'message' =>$validator->messages()
					]
				], 422);
		}

		$user=new User();
		$user->name=$userData['name'];
		$user->email=$userData['email'];
		$user->password=bcrypt($userData['password']);

		if ($user->save()) {

			$token = $user->createToken('API Token')->plainTextToken;
			$code = 200;
			return response()->json([

				'status' => 'Success',
				'message' => 'successfull registered',
				'data' => $token
			],$code);



		}

	}

	public function login(Request $request){

		// if (empty($header['Authorization'])) {
		// 		$message = "You are not Autherized for this api";
		// 		return response()->json(['status'=>false, 'message'=>$message], 422);
		// 	}else{
		// 		User::where('access_tocken', $header['Authorization'])->update(['access_tocken'=> NULL]);
		// 		return response()->json(['status'=>true,'message'=>'User Logout'], 201);
		// 	}

		$attr = $request->validate([
			
			'email' => 'required|string|email|',
			'password' => 'required|string|min:6|',
		]);

		if(!Auth::attempt($attr)){
			$message = "Your's email or password not match";
				return response()->json(['status'=>false, 'message'=>$message], 422);
		}

		$token = auth()->user()->createToken('API Token')->plainTextToken;
		$code = 200;
		return response()->json([
			'status' => 'Success',
			'message' => 'successfull Login',
			'data' => $token

		],$code);

	}

	public function logout()
	{
		auth()->user->tokens()->delete();

		$code = 200;
		return response()->json([
			'status' => 'Success',
			'message' => 'successfull Logout',


		],$code);
	}
}
