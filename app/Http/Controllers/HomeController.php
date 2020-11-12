<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\DetailUser;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $dt = DetailUser::where('user_id', Auth::user()->id)->first();
        $data = [
            'user'      => Auth::user()->username,
            'images'    => '/img/avatar.png',
        ];
        if($dt){
            $data = [
                'user'      => $dt->first_name .' '. $dt->last_name,
                'images'    => isset($dt->file)? $dt->file : '/img/avatar.png',
            ];
        }
        return view('pages.dashboard.index', $data);
    }

}
