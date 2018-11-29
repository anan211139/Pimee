<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Studentparent;
use App\Student;
use App\Info_classroom;
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

    public function connect(Request $request){
        $classroom_id = $request->input('Code');
        $line_code = session('linecode_connect','default');
        $insert = new Info_classroom;
        $insert->line_code = $line_code;
        $insert->classroom_id = $classroom_id;
        $insert->save(); 
        return redirect('/');
    }
    public function includedata(Request $request){
        $name = $request->input('name');
        $line_code = $request->input('line_code');
        // $surname = $request->input('surname');
        // $school = $request->input('school');

        App\Student::where('line_code', $line_code)
          ->update(['name' => $name]);
        return "Done";
    }
    
}
