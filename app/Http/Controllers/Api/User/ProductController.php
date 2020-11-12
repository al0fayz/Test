<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use App\CategoryProduct;
use Illuminate\Support\Facades\Auth;
use DB;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
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
        return Product::with('category')->latest()->paginate(10);
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
            'nama'  => ['required', 'string'],
            'deskripsi'    => 'required',
            'category_id'    => 'required',
            'stok'    => 'required',
            'file'    => 'required',
        ]);
        DB::beginTransaction();
        try{
            //save images
            $dir_name = public_path('/img/images/'.Auth::user()->username);
            //check if directory not exist and make it 
            if(!is_dir($dir_name)){
                mkdir($dir_name);
            }
            $name = time().'.' . explode('/', explode(':', substr($request->file, 0, strpos($request->file, ';')))[1])[1];
            $cover = '/img/images/'.Auth::user()->username.'/'.$name;
            $img = Image::make($request->file)->resize(320, 200)->save(public_path($cover));

            //save data
            $dt = new Product();
            $dt->nama = $request->nama;
            $dt->deskripsi = $request->deskripsi;
            $dt->stok = (int)$request->stok;
            $dt->category_id = (int)$request->category_id;
            $dt->file = $cover;

            $dt->save();

            DB::commit();
            return response()->json([
                'success'   => true
            ], 200);
        }catch(\Exception $e){
            DB::rollBack(); dd($e);
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
            'nama'  => ['required', 'string'],
            'deskripsi'    => 'required',
            'category_id'    => 'required',
            'stok'    => 'required',
            'file'    => 'required',
        ]);
        DB::beginTransaction();
        try{
            $dt = Product::findOrFail($id);
            //save images
            $dir_name = public_path('/img/images/'.Auth::user()->username);
            //check if directory not exist and make it 
            if(!is_dir($dir_name)){
                mkdir($dir_name);
            }
            if($request->file != null){
                $p = public_path($dt->file);
                if(file_exists($p)){
                    unlink($p);
                }
                $name = time().'.' . explode('/', explode(':', substr($request->file, 0, strpos($request->file, ';')))[1])[1];
                $cover = '/img/images/'.Auth::user()->username.'/'.$name;
                $img = Image::make($request->file)->resize(320, 200)->save(public_path($cover));
            }

            //save data
            $dt->nama = $request->nama;
            $dt->deskripsi = $request->deskripsi;
            $dt->stok = (int)$request->stok;
            $dt->category_id = (int)$request->category_id;
            $dt->file = $cover;

            $dt->save();

            DB::commit();
            return response()->json([
                'success'   => true
            ], 200);
        }catch(\Exception $e){
            DB::rollBack(); dd($e);
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
        DB::beginTransaction();
        try{
            $blog = Product::findOrFail($id);
            $path = public_path($blog->file);
            if(file_exists($path)){
                unlink($path);
            }
            $blog->delete();
            DB::commit();
            return response()->json([
                'success'   => true,
                'messages'  => 'delete data successfully!'
            ]); 
        }catch(\Exception $e){
            DB::rollBack(); dd($e);
            return response()->json([
                'success'   => false,
                'messages'  => 'Internal server error!'
            ], 200);
        }
    }
}
