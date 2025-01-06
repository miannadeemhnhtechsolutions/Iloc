<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsController extends Controller
{
  public function index()
  {
      $data=NewsData::all();
      $response = [
          'status' => true,
          'data'=>$data,
          'message' => "Success",
      ];
      return response()->json($response, 200);
  }
  public function store(Request $request)
  {
      $validator = Validator::make($request->all(), [

          "info"=>"required|string",
          "title"=>"required|string",

          "flyer"=>'required|mimes:jpg,jpeg,png,gif,webp,bmp,svg|max:90048',
          "author"=>"required|string",
          "description"=>"required|string",
          "link"=>"nullable|string",
      ]);

      if($validator->fails()){

          $response = [
              'status' => false,
              'errors'    => $validator->errors(),
              'message' => "Validation Fails",
          ];


          return response()->json($response, 404);

      }

      $news=new NewsData();
      if ($request->hasFile('flyer')) {
          $file = $request->file('flyer');
          $originalName = $file->getClientOriginalName();
          $file_Name = uniqid() . '.' . time() . '.' . $file->getClientOriginalExtension();
          $file->move(public_path('admin/news'), $file_Name);
          $imagePath = asset('/admin/news/' . $file_Name);
          $news->flyer=$imagePath;
      }
      $news->info=$request->info;
      $news->title=$request->title;
      $news->author=$request->author;
      $news->description=$request->description;
      $news->link=$request->link;
      $news->save();
      $response = [
          'status' => true,
          'data'=>$news,
          'message' => "Success",
      ];
      return response()->json($response, 200);



  }

  public function edit($id)
  {
      $news=NewsData::where('id',$id)->first();
      if (!$news){
          $response = [
              'status' => false,
              'errors'=>['id'=>["The selected id is invalid."]],
              'message' => "failed",
          ];
          return response()->json($response, 404);
      }
      $response = [
          'status' => true,
          'data'=>$news,
          'message' => "Success",
      ];
      return response()->json($response, 200);

  }

  public function update(Request $request)
  {
      $validator = Validator::make($request->all(), [

          "info"=>"required|string",
          "title"=>"required|string",

          "flyer"=>'nullable|mimes:jpg,jpeg,png,gif,webp,bmp,svg|max:90048',
          "author"=>"required|string",
          "description"=>"required|string",
          "link"=>"nullable|string",
          'id'=>"numeric|required|exists:news_data,id",
      ]);

      if($validator->fails()){

          $response = [
              'status' => false,
              'errors'    => $validator->errors(),
              'message' => "Validation Fails",
          ];


          return response()->json($response, 404);

      }

      $news=NewsData::where('id',$request->id)->first();
      if ($request->hasFile('flyer')) {
          $file = $request->file('flyer');
          $originalName = $file->getClientOriginalName();
          $file_Name = uniqid() . '.' . time() . '.' . $file->getClientOriginalExtension();
          $file->move(public_path('admin/news'), $file_Name);
          $imagePath = asset('/admin/news/' . $file_Name);
          $news->flyer=$imagePath;
      }
      $news->info=$request->info;
      $news->title=$request->title;
      $news->author=$request->author;
      $news->description=$request->description;
      $news->link=$request->link;
      $news->save();
      $response = [
          'status' => true,
          'data'=>$news,
          'message' => "Success",
      ];
      return response()->json($response, 200);

  }

  public function destroy($id)
  {
      $news=NewsData::where('id',$id)->first();
      if (!$news){
          $response = [
              'status' => false,
              'errors'=>['id'=>["The selected id is invalid."]],
              'message' => "failed",
          ];
          return response()->json($response, 404);
      }
      $news->delete();
      $response = [
          'status' => true,
          'message' => "Success",
      ];
      return response()->json($response, 200);
  }

}
