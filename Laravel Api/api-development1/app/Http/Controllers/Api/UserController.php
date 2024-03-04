<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($flag)
    {
        //! flag -> 1(active)
        //! flag -> 0(all)

        $query = User::select('email', 'name');
        if ($flag == 1) {
            $query->where('status', 1);
        } elseif ($flag == 0) {
            // $query->where('status', 0);//! get inactive users
            //empty//?get all users
        } else {
            return response()->json([
                'message' => 'invalid parameter passed ,it can either 0 or 1',
                'status' => 0
            ], 400);
        }

        $users = $query->get();
        if (count($users) > 0) {
            $response = [
                'message' => count($users) . ' users found',
                'status' => 1,
                'data' => $users
            ];
        } else {
            $response = [
                'message' => count($users) . 'users found',
                'status' => 0,
            ];
        }
        return response()->json($response, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = FacadesValidator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
            'password_confirmation' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        } else {
            $data = [
                'name'  => $request->name,
                'email'  => $request->email,
                'password'  =>$request->password,// Hash::make(
                    //),
                    
            ];
            DB::beginTransaction();
            try {

                $user =   User::create($data);
                Db::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                // p($e->getMessage());
                $user = null;
            }
            if ($user != null) {
                return response()->json([
                    'message' => 'User registerd successfully',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'internal server error',
                ], 500);
            }
        }
        // p($request->all());

        // $request->validate([
        //     'name'=>['required'],
        //     'email'=>['required','email'],
        //     'name'=>['required','min:6'],
        // ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            $response = [
                'message' => 'User not found',
                'status' => 0
            ];
        } else {
            $response = [
                'message' => 'User found',
                'status' => 1,
                'data' => $user
            ];
        }
        return response()->json([
            $response
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            $response = [
                'message' => "User does not exist!",
                'status' => 0,
            ];
            $respCode = 404;
        }else { 
            DB::beginTransaction();
            try {
             
                $user->name = $request['name'];
                $user->email = $request['email'];
                $user->contact = $request['contact'];
                $user->pincode = $request['pin'];
                $user->address = $request['address'];
                $user->save();
                DB::commit();
                $response = [
                'message' => 'User updated Successfully',
                'status' => 1,
            ];
            $respCode = 200;
            } catch (\Exception $th) {
                DB::rollBack();
                $response = [
                    'message' => 'Internal Server Error',
                    'errMessage'=> $th->getMessage()
                ];
                $respCode = 500;

            }
        }
       return response()->json($response,$respCode);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            $response = [
                'message' => "Cant't find User",
                'status' => 0,
            ];
            $respCode = 404;
        }else{
            DB::beginTransaction();
            try {
                $user->delete();
                DB::commit();
                $response = [
                'message' => 'User deleted Successfully',
                'status' => 1,
            ];
            $respCode = 200;
            } catch (\Exception $th) {
                DB::rollBack();
                $response = [
                    'message' => 'Internal Server Error',
                ];
                $respCode = 500;

            }
        }
       return response()->json($response,$respCode);
        
    }
    public function change_password(Request $request, string $id){
        
        $user = User::find($id);
        if (is_null($user)) {
            $response = [
                'message' => "User does not exist!",
                'status' => 0,
            ];
            $respCode = 404;
        }else { 
            // todo 'checking Hashed Password'
            //! Hash::check($request['old_password'],'$user->password');
            //? if return 1 than hashed password is matched 

            if ($user->password == $request['old_password']) {
                if ($request['new_password'] == $request['confirm_password']) {
                    DB::beginTransaction();
                    try {
                        $user->password = $request['new_password'];
                        $user->save();
                        DB::commit();
                        $response = [
                        'message' => 'User updated Successfully',
                        'status' => 1,
                    ];
                    $respCode = 200;
                    } catch (\Exception $th) {
                        DB::rollBack();
                        $response = [
                            'message' => 'Internal Server Error',
                            'errMessage'=> $th->getMessage()
                        ];
                        $respCode = 500;
        
                    }
                }else{
                    $response = [
                        'message' => "Confirm password does not match!",
                        'status' => 0,
                    ];
                    $respCode = 400;
                }
            }else{
                $response = [
                    'message' => "Incorrect old password!",
                    'status' => 0,
                ];
                $respCode = 400;
            }
           
        }
       return response()->json($response,$respCode);
    }
}
