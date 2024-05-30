<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoryModel;

class CategoryController extends Controller
{
    public function CategoryView(){
        $result = CategoryModel::paginate(10);
        return view('admin.category', ['category'=>$result]);
    }

    public function AddCategory(Request $rq){

        $result = new CategoryModel();

        $result->Category_Name = $rq->Category_Name;

        $result->save();

        if (isset($result)){
            session(['message'=>'Category added successfully!', 'type'=>'success']);
            return redirect('/admin/category');
        }else{
            session(['message'=>'Category failed to be added!', 'type'=>'danger']);
            return redirect('/admin/category');
        }
        
    }

    public function UpdateCategory(Request $rq){

        $result = CategoryModel::find($rq->Category_Id);

        $result->Category_Name = $rq->Category_Name;

        $result->save();

        if (isset($result)){
            session(['message'=>'Category updated successfully!', 'type'=>'success']);
            return redirect('/admin/category');
        }else{
            session(['message'=>'Category failed to be updated!', 'type'=>'danger']);
            return redirect('/admin/category');
        }
        
    }

    public function DeleteCategory(Request $rq){
        $deleted = CategoryModel::find($rq->category_id);
        $deleted->delete();
        
        if (isset($deleted)){
            session(['message'=>'Category deleted successfully!', 'type'=>'success']);
        }else{
            session(['message'=>'Category failed to be deleted!', 'type'=>'danger']);
        }
    }
}
