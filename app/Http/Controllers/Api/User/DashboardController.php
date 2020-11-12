<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\DetailUser;
use DB; 

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    public function index(){
        $id = Auth::user()->id;
        $detail = DetailUser::where('user_id', $id)->first();
        $name = Auth::user()->username;
        if($detail){
            $name = $detail->first_name .' '.$detail->last_name;
        }
        return response()->json([
            'success'   => true,
            'name'      => $name,
            'email'     => Auth::user()->email,
        ]);
    }
}
