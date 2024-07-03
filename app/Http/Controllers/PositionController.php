<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PositionModel;

class PositionController extends Controller
{
    public function PositionView(){

        $result = PositionModel::paginate(10);
        return view('admin.position', ['position'=>$result]);
        
    }

    public function AddPosition(Request $rq){

        $result = new PositionModel();

        $result->Position_Name = $rq->Position_Name;

        $result->save();

        if (isset($result)){
            session(['message'=>'Position added successfully!', 'type'=>'success']);
            return redirect('/admin/position');
        }else{
            session(['message'=>'Position failed to be added!', 'type'=>'danger']);
            return redirect('/admin/position');
        }
        
    }

    public function UpdatePosition(Request $rq){

        $result = PositionModel::find($rq->Position_Id);

        $result->Position_Name = $rq->Position_Name;

        $result->save();

        if (isset($result)){
            session(['message'=>'Position updated successfully!', 'type'=>'success']);
            return redirect('/admin/position');
        }else{
            session(['message'=>'Position failed to be updated!', 'type'=>'danger']);
            return redirect('/admin/position');
        }
        
    }

    public function DeletePosition(Request $rq){
        $deleted = PositionModel::find($rq->position_id);
        $deleted->delete();
        
        if (isset($deleted)){
            session(['message'=>'Position deleted successfully!', 'type'=>'success']);
        }else{
            session(['message'=>'Position failed to be deleted!', 'type'=>'danger']);
        }
    }
}
