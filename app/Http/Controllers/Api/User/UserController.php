<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Detail;
use App\DetailUser;
use DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::with('detail')->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'email'  => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'first_name'    => 'required',
            'last_name'     => 'required',
            'jenis_kelamin' => 'required',
            'tanggal_lahir' => 'required',
            'password'      => 'required'
        ]);
        DB::beginTransaction();
        try{
            //save data
            $dt = new User();
            $dt->username = 'user' . rand(10, 1000);
            $dt->email = $request->email;
            $dt->password = Hash::make($request->password);
            $dt->save();
            $d = new DetailUser();
            $d->first_name = $request->first_name;
            $d->last_name = $request->last_name;
            $d->jenis_kelamin = $request->jenis_kelamin;
            $d->tanggal_lahir = $request->tanggal_lahir;
            $d->user_id = $dt->id;
            $d->save();

            DB::commit();
            return response()->json([
                'success'   => true
            ], 200);
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'success'   => false
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'email'  => ['required', 'string', 'email', 'max:255'],
            'first_name'    => 'required',
            'last_name'     => 'required',
            'jenis_kelamin' => 'required',
            'tanggal_lahir' => 'required',
            'password'      => 'required'
        ]);
        DB::beginTransaction();
        try{
            //save data
            $dt = User::findOrFail($id);
            $dt->email = $request->email;
            $dt->password = Hash::make($request->password);
            $dt->save();

            $d = DetailUser::where('user_id', $id)->latest()->first();
            $d->first_name = $request->first_name;
            $d->last_name = $request->last_name;
            $d->jenis_kelamin = $request->jenis_kelamin;
            $d->tanggal_lahir = $request->tanggal_lahir;
            $d->user_id = $dt->id;
            $d->save();

            DB::commit();
            return response()->json([
                'success'   => true
            ], 200);
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'success'   => false
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $user = User::findOrFail($id);
            $detail = DetailUser::where('user_id', $id)->latest()->first();
            $detail->delete();
            $user->delete();
            
            return response()->json([
                'success'   => true,
                'messages'  => 'Delete data successfully'
            ]);
        }catch(\Exception $e){

        }
    }
}
