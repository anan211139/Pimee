<?php

namespace App\Http\Controllers;
use App\Classroom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class manageClassroom extends Controller
{
    public function newclassroom(Request $request){
        $name = $request->input('name');
        $id = session('id', 'default');
        $query = DB::table('classrooms')->select('classroom_code')->get();
        $allofcodeInDB = json_decode($query, true);
        $status = true;
        if($name == ""){
            return redirect('/newclass');
        }
        while($status){
            $code = $this->randomcode();
            if(count($allofcodeInDB)){
                for($i=0;$i<count($allofcodeInDB);$i++){
                    if($allofcodeInDB[$i]['classroom_code'] == $code){
                        continue;
                    }
                }
                $this->insertNewClassroom($name,$code,$id);
                return redirect('/selectclass');
            }else{
                $this->insertNewClassroom($name,$code,$id);
                return redirect('/selectclass');
            }
        }
    }
    public function randomcode(){
        $code = "";
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $characterslen = strlen($characters)-1;
        for ($i = 0; $i < 6; $i++) {
            $code .= $characters[rand(0, $characterslen)];
        }
        return $code;
    }
    public function insertNewClassroom($name,$code,$id){
        $classroom = new Classroom;
        $classroom->name = $name;
        $classroom->classroom_code = $code;
        $classroom->parent_id = $id;
        $classroom->save();
    }
    public function removeclass($id){
        DB::table('classrooms')->where('id', '=', $id)->delete();
        session()->forget('classstatus');
        return redirect('/');
    }
}
