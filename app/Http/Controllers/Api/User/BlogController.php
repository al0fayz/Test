<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Blog;
use App\BlogCategory;
use App\DetailUser;
use DB;
use Intervention\Image\Facades\Image;
use DOMDocument;
use Illuminate\Support\Str;

class BlogController extends Controller
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
        $blog   = Blog::where('user_id', Auth::user()->id)->latest()->paginate(9);
        $images = '';
        $user   = Auth::user()->username;
        $detail = DetailUser::where('user_id', Auth::user()->id)->first();
        if($detail){
            $user = $detail->first_name .' '.$detail->last_name;
            if(isset($detail->file)){
                $images = $detail->file;
            }
        }
        return response()->json([
            'success'   => true,
            'blog'      => $blog,
            'images'    => $images,
            'user'      => $user,
        ]);
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
            'title'         => ['required', 'string', 'regex:/^[a-zA-Z0-9 ]*$/'],
            'file'          => ['required_without:cover', 'nullable'],
            'content'       => ['required', 'string'],
            'category_id'   => ['required'],
            'cover'         => ['required_without:file','nullable', 'string', 'regex:/^[a-zA-Z0-9 ]*$/', 'max:20'],
        ]);
        DB::beginTransaction();
        try{
            //cek if category_id is exist
            $ct = BlogCategory::where('user_id', Auth::user()->id)->where('id', $request->category_id)->first();
            if(!$ct){
                return response()->json([
                    'success'       => false,
                    'messages'      => 'Category id not found for this user!'
                ], 200);
            }
            $cover = '';

            $dir_name = public_path('/img/images/'.Auth::user()->username);
            //check if directory not exist and make it 
            if(!is_dir($dir_name)){
                mkdir($dir_name);
            }

            if(isset($request->select_image)){
                if($request->select_image){
                    //save image cover
                    $name = time().'.' . explode('/', explode(':', substr($request->file, 0, strpos($request->file, ';')))[1])[1];
                    $cover = '/img/images/'.Auth::user()->username.'/'.$name;
                    $img = Image::make($request->file)->resize(320, 200)->save(public_path($cover));
                }else{
                    $cover = $request->cover;
                }
            }else{
                //save image cover
                $name = time().'.' . explode('/', explode(':', substr($request->file, 0, strpos($request->file, ';')))[1])[1];
                $cover = '/img/images/'.Auth::user()->username.'/'.$name;
                $img = Image::make($request->file)->resize(320, 200)->save(public_path($cover));
            }

            //change sumernote image base 64 save to db and change to url
            $content = $request->input('content');
            $dom = new \DomDocument();
            libxml_use_internal_errors(true);
            $dom->loadHtml($request->content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);    
            $elements = $dom->getElementsByTagName('img'); 
            if($elements instanceof \DOMNodeList){
                foreach($elements as $key => $value){
                    //get atributte src
                    $cont = $value->getAttribute('src');
                    $explode = explode(';', $cont)[0]; 
                    
                    if($explode  == 'data:image/png' || $explode == 'data:image/gif' || $explode == 'data:image/jpg' || $explode == 'data:image/jpeg'){
                        //save image
                        $nm = time().$key.'.' . explode('/', explode(':', substr($cont, 0, strpos($cont, ';')))[1])[1];
                        $path = '/img/images/'.Auth::user()->username.'/'.$nm;
                        $img_v = Image::make($cont)->resize(320, 200)->save(public_path($path));
                        
                        //delete data in src
                        $value->removeAttribute("src");   
                        $value->setAttribute("class", "img-fluid");
                        //change src with url
                        $value->setAttribute("src", $path);
                        
                    }
                }
            }
            $content = $dom->saveHTML();
            libxml_clear_errors ();
            
            $blog                   = new Blog();
            $blog->title            = ucwords($request->title);
            $blog->user_id          = Auth::user()->id;
            $blog->images           = $cover;
            $blog->category_id      = $ct->id;
            $blog->content          = $content;
            $blog->slug             = $this->slug($request->title);
            $blog->save();
            DB::commit();
            return response()->json([
                'success'       => true,
                'messages'      => 'Upload blog is successfully!'
            ], 200);
        }catch(\Exception $e){
            DB::rollBack(); 
            throw $e;
            return response()->json([
                'success'       => false,
                'messages'      => 'internal server error!'
            ], 200);
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
        $blog   = Blog::where('user_id', Auth::user()->id)->findOrFail($id);
        return response()->json([
            'success'   => true,
            'data'      => $blog
        ]);
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
            'content'   => ['required'],
            'title'     => ['required', 'string'], // 'regex:/^[a-zA-Z0-9 ]*$/'],
        ]);
        DB::beginTransaction();
        try{
            $blog = Blog::find($id);
            if(!$blog){
                return response()->json([
                    'success'   => false
                ]);
            }else{
                $dir_name = public_path('/img/images/'.Auth::user()->username);
                //check if directory not exist and make it 
                if(!is_dir($dir_name)){
                    mkdir($dir_name);
                }
                //change images base 64 to url
                $content = $request->input('content');
                $dom = new \DomDocument();
                libxml_use_internal_errors(true);
                $dom->loadHtml($request->content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);    
                $elements = $dom->getElementsByTagName('img');
                if($elements instanceof \DOMNodeList){
                    foreach($elements as $key => $value){
                        //get atributte src
                        $cont = $value->getAttribute('src');
                        $explode = explode(';', $cont)[0]; 
                    
                        if($explode  == 'data:image/png' || $explode == 'data:image/gif' || $explode == 'data:image/jpg' || $explode == 'data:image/jpeg'){
                            //save image
                            $nm = time().$key.'.' . explode('/', explode(':', substr($cont, 0, strpos($cont, ';')))[1])[1];
                            $path = '/img/images/'.Auth::user()->username.'/'.$nm;
                            
                            $img_v = Image::make($cont)->resize(300, 200)->save(public_path($path));
                            //delete data in src
                            $value->removeAttribute('src'); 
                            
                            $value->removeAttribute("class");   
                            $value->setAttribute("class", "img-fluid");
                            //change src with url
                            $value->setAttribute('src', $path);
                        }else{
                            $value->removeAttribute("class");   
                            $value->setAttribute("class", "img-fluid");
                        }
                        
                    }
                }
                $content = $dom->saveHTML();
                libxml_clear_errors ();
                if(isset($request->select_image)){
                    if($request->select_image){
                        //if change images
                        if($request->file != null){
                            //delete image
                            $path = public_path($blog->images);
                            if(file_exists($path)){
                                unlink($path);
                            }
                            //save image
                            $name = time().'.' . explode('/', explode(':', substr($request->file, 0, strpos($request->file, ';')))[1])[1];
                            $path_new = '/img/images/'.Auth::user()->username.'/'.$name;

                            $img = Image::make($request->file)->resize(320, 200)->save(public_path($path_new));

                            //insert DB
                            $blog->images           = $path_new;
                        }
                    }else{
                        if($request->cover != null){
                            $blog->images           = $request->cover;
                        }
                    }
                }
                $blog->content = $content;
                $blog->title   = $request->title;
                $blog->slug             = $this->slug($request->title);
                if(isset($request->category_id)){
                    $blog->category_id = $request->category_id;
                }
                $blog->save();
                DB::commit();
                return response()->json([
                    'success'   => true,
                    'messages'  => 'Upload blog Successfully!'
                ]);
            }
        }catch(\Exception $e){
            DB::rollBack(); throw $e;
            return response()->json([
                'success'   => false,
                'messages'  => 'Internal server error'
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
            $blog = Blog::findOrFail($id);
            if(!$blog){
                return response()->json([
                    'success'   => false,
                    'messages'  => 'Blog is not exist!'
                ]);
            }
            //delete images
            $path = public_path($blog->images);
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
            DB::rollBack();
            return response()->json([
                'success'   => false,
                'messages'  => 'Internal server error'
            ]);
        }
    }
    public function getCategory(){
        $dt = BlogCategory::where('user_id', Auth::user()->id)->get();
        return $dt;
    }
    public function search(Request $request){
        if($request->search != '' || $request->search != null){
            $search = $request->search;
            $data = Blog::where('user_id', Auth::user()->id)
                        ->where('title', 'LIKE', '%'.$search.'%')->paginate(9);

            $count = count($data);
            return response()->json([
                'success'       => true,
                'data'          => $data,
                'count'         => $count,
            ]);
        }else{
            return response()->json([
                'success'   => false,
                'messages'  => 'search input empty!'
            ]);
        }
    }
    public function images(Request $request){
        $name = time().'.' . explode('/', explode(':', substr($request->file, 0, strpos($request->file, ';')))[1])[1];
        $img = Image::make($request->file)->save(public_path('img/blog/').$name);
        return response()->json([
            'success'   => true,
            'url'       => "/img/blog/".$name
        ]);
    }
    function slug($string){
        $slug   = Str::of($string)->slug('-');
        $random = $this->random();

        return $slug .'-'. $random;
    }
    function random($length = 5){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
