<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\BlogCategory;
use App\Blog;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Str;

class BlogCategoryController extends Controller
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
        return BlogCategory::where('user_id', Auth::user()->id)->latest()->paginate(10);
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
            'category_name'     => ['required', 'string'],
        ]);
        DB::beginTransaction();
        try{
            $bc = new BlogCategory();
            $bc->category_name  = ucwords($request->category_name);
            $bc->user_id        = Auth::user()->id;
            $bc->save();
            DB::commit();
            return response()->json([
                'success'       => true,
                'messages'      => 'Save data successfully!'
            ]);
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'success'       => false,
                'messages'      => 'Internal Server Error'
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
            'category_name'     => ['required', 'string'],
        ]);
        DB::beginTransaction();
        try{
            $bc = BlogCategory::findOrFail($id);
            if($bc){
                $bc->category_name  = ucwords($request->category_name);
                $bc->save();
                DB::commit();
                return response()->json([
                    'success'       => true,
                    'messages'      => 'Update data successfully'
                ]);
            }else{
                return response()->json([
                    'success'       => false,
                    'messages'      => 'Blog id is not found!'
                ]);
            }
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'success'       => false,
                'messages'      => 'Internal server error',
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
            $blog = Blog::where('category_id', $id)->get();
            if(count($blog) > 0){
                return response()->json([
                    'success'   => false,
                    'messages'  => 'please delete blog first because this is related to blog'
                ], 200);
            }else{
                $dt = BlogCategory::where('id', $id)->first()->delete();
                DB::commit();
                return response()->json([
                    'success'   => true,
                    'messages'  => 'Delete data successfully!'
                ], 200);
            }
        }catch(\Exception $e){
            DB::rollBack();
            return response()->json([
                'success'   => false,
                'messages'  => 'Internal server error!'
            ], 200);
        }
    }
    public function search(Request $request){
        if($request->search != '' || $request->search != null){
            $search = $request->search;
            $data = BlogCategory::where('user_id', Auth::user()->id)
                        ->where('category_name', 'LIKE', '%'.$search.'%')->paginate(10);

            $count = count($data);
            return response()->json([
                'success'       => true,
                'data'          => $data,
                'count'         => $count
            ]);
        }else{
            return response()->json([
                'success'   => false,
                'messages'  => 'search input empty!'
            ]);
        }
    }
}
