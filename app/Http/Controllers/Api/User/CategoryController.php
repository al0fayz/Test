<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\CategoryProduct;
use DB;

class CategoryController extends Controller
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
        return CategoryProduct::latest()->paginate(10);
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
            'category_name'  => ['required', 'string'],
            'desc'    => 'required',
        ]);
        DB::beginTransaction();
        try{
            //save data
            $dt = new CategoryProduct();
            $dt->category_name = $request->category_name;
            $dt->desc = $request->desc;
            $dt->save();

            DB::commit();
            return response()->json([
                'success'   => true
            ], 200);
        }catch(\Exception $e){
            DB::rollBack(); //dd($e);
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
            'category_name'  => ['required', 'string'],
            'desc'    => 'required',
        ]);
        DB::beginTransaction();
        try{
            //save data
            $dt = CategoryProduct::findOrFail($id);
            $dt->category_name = $request->category_name;
            $dt->desc = $request->desc;
            $dt->save();

            DB::commit();
            return response()->json([
                'success'   => true
            ], 200);
        }catch(\Exception $e){
            DB::rollBack(); //dd($e);
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
            $cat = CategoryProduct::findOrFail($id);
            $cat->delete();
            return response()->json([
                'success'   => true,
                'messages'  => 'Delete data successfully'
            ]);
        }catch(\Exception $e){

        }
    }
}
