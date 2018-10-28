<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Studentparent;
use App\Student;
use Illuminate\Support\Facades\DB;
use Session;

class addchildcontroller extends Controller
{
    public function addchild(Request $request){
        $linecode = $request->input('code');
        $name = $request->input('nickname');
        $id = Session::get('id','default');

        $querysta = DB::table('studentparents')
        ->where('line_code','=',$linecode)
        ->where('parent_id','=',$id)
        ->get();
        // return $querysta;
        if(count($querysta)){
            return "มีการเชื่อมต่ออยู่แล้ว";
        }else{
            $insert = new Studentparent;
            $insert->line_code = $linecode;
            $insert->parent_id = $id;
            $insert->save();

            $update_nickname = new Student;
            $update_nickname::whereraw("line_code = '$linecode'")->update(['name' => $name]);
            return redirect('/');
        }
        
    }
    public function sessionaddchild($id){
      Session::put('choosechild',$id);
      return redirect('/userpage');
    }
}
