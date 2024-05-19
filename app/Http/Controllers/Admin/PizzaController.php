<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Pizza;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PizzaController extends Controller
{
     //direct admin pizza
     public function pizza()
     {
        if(Session::has('PIZZA_SEARCH')){
         Session::forget('PIZZA_SEARCH');
        }
        $data = Pizza::paginate(7);

        if(count($data) == 0){
         $emptyStatus =0;
        }else{
         $emptyStatus =1;
        }
        return view('admin.pizza.list')->with(['pizza'=>$data, 'status'=>$emptyStatus]);
     }

     public function createPizza()
     {
        $category = Category::get();
        return view('admin.pizza.create')->with(['category'=>$category]);
     }

     //insert pizza data
     public function insertPizza(Request $request)
     {
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'image' => 'required',
            'price' => 'required',
            'publish' => 'required',
            'category' => 'required',
            'discount' => 'required',
            'buyOnegetOne' => 'required',
            'waitingTime' => 'required',
            'description' => 'required',
        ]);
        if($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }

        $file = $request->file('image');
        $fileName = uniqid().'_'.$file->getClientOriginalName();
        $file->move(public_path().'/uploads/',$fileName);

        $data = $this->requestPizzaData($request,$fileName);
        Pizza::create($data);
        return redirect()->route('admin#pizza')->with(['createSuccess'=>'Pizza Created!']);
     }

     public function deletePizza($id)
     {
         //get old image
         $data = Pizza::select('image')->where('pizza_id',$id)->first();
         $fileName = $data['image'];

        Pizza::where('pizza_id',$id)->delete();

        //delete image in uploads folder
        if(File::exists(public_path().'/uploads/'.$fileName))
        {
         File::delete(public_path().'/uploads/'.$fileName);
        }
        return back()->with(['deleteSuccess'=>'Delete Success']);
     }

     //pizza info
     public function pizzaInfo($id)
     {
      $data = Pizza::where('pizza_id',$id)->first();
      return view('admin.pizza.info')->with(['pizza'=>$data]);
     }

     //edit pizza
     public function editPizza($id)
     {
      $category = Category::get();

      $data = Pizza::select('pizzas.*','categories.category_id','categories.category_name')
               ->join('categories','categories.category_id','=','pizzas.category_id')
               ->where('pizza_id',$id)
               ->first();

      return view('admin.pizza.edit')->with(['pizza'=>$data, 'category'=>$category]);
     }

     //update pizza info
     public function updatePizza($id,Request $request)
     {
      $validator = Validator::make($request->all(),[
         'name' => 'required',
         'price' => 'required',
         'publish' => 'required',
         'category' => 'required',
         'discount' => 'required',
         'buyOnegetOne' => 'required',
         'waitingTime' => 'required',
         'description' => 'required',
     ]);
     if($validator->fails()){
         return back()->withErrors($validator)->withInput();
     }
      $updateData = $this->requestUpdatePizzaDate($request);

      if(isset($updateData['image'])){
         //get old image
         $data = Pizza::select('image')->where('pizza_id',$id)->first();
         $fileName = $data['image'];

         //delete old image
         if(File::exists(public_path().'/uploads/'.$fileName))
         {
          File::delete(public_path().'/uploads/'.$fileName);
         }

         $file = $request->file('image');
         $fileName = uniqid().'_'.$file->getClientOriginalName();
         $file->move(public_path().'/uploads/',$fileName);

         $updateData['image'] = $fileName;
      }
       //update
         Pizza::where('pizza_id',$id)->update($updateData);
         return redirect()->route('admin#pizza')->with(['updateSuccess'=>'Pizza Updated!']);
     }

     //search pizza
     public function searchPizza(Request $request)
     {
      $searchKey = $request->table_search;
      $searchData = Pizza::orWhere('pizza_name','like','%'.$searchKey.'%')  //where is and gate, orWhere is or gate
                           ->orWhere('price','like','%'.$searchKey.'%')
                           ->paginate(7);

      Session::put('PIZZA_SEARCH',$searchKey);
      $searchData->appends($request->all());//fix search error of paginate

      if(count($searchData) == 0){
         $emptyStatus =0;
        }else{
         $emptyStatus =1;
      }
      return view('admin.pizza.list')->with(['pizza'=>$searchData,'status'=>$emptyStatus]);
     }

     //download pizza
     public function pizzaDownload(){
      if(Session::has('PIZZA_SEARCH')){
         $pizza = Pizza::orWhere('pizza_name','like','%'.Session::get('PIZZA_SEARCH').'%')  //where is and gate, orWhere is or gate
                        ->orWhere('price','like','%'.Session::get('PIZZA_SEARCH').'%')
                        ->get();
     }else{
         $pizza = Pizza::get();
     }

     $csvExporter = new \Laracsv\Export();

     $csvExporter->build($pizza, [
         'pizza_id' => 'No',
         'pizza_name' => 'Pizza Name',
         'price' => 'Pizza Price',
         'publish_status' => 'Publish Date',
         'buy_one_get_one_status' =>'Buy One Get One',
         'created_at' => 'Created Date',
         'updated_at' => 'Updated Date',
     ]);
     $csvReader = $csvExporter->getReader();

     $csvReader->setOutputBOM(\League\Csv\Reader::BOM_UTF8);

     $filename = 'pizzaList.csv';

     return response((string) $csvReader)
         ->header('Content-Type', 'text/csv; charset=UTF-8')
         ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
     }

     //look category item
     public function categoryItem($id){
      $data = Pizza::select('pizzas.*','categories.category_name as categoryName')
               ->join('categories','categories.category_id','pizzas.category_id')
               ->where('categories.category_id',$id)->paginate(5);
      return view('admin.category.item')->with(['pizza'=>$data]);
     }


     private function requestUpdatePizzaDate($request)
     {
      $arr = [
            'pizza_name' => $request->name,
            'price' => $request->price,
            'publish_status' => $request->publish,
            'category_id' => $request->category,
            'discount_price' => $request->discount,
            'buy_one_get_one_status' => $request->buyOnegetOne,
            'waiting_time' => $request->waitingTime,
            'description' => $request->description,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
      ];

      if(isset($request->image)){
         $arr['image'] = $request->image;
      }
      
      return $arr;
     }

     private function requestPizzaData($request,$fileName)
     {
        return[
            'pizza_name' => $request->name,
            'image' => $fileName,
            'price' => $request->price,
            'publish_status' => $request->publish,
            'category_id' => $request->category,
            'discount_price' => $request->discount,
            'buy_one_get_one_status' => $request->buyOnegetOne,
            'waiting_time' => $request->waitingTime,
            'description' => $request->description,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
     }
}
