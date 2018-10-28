<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use View;


class SqlController extends Controller
{   

    public function leaderboard($id){    

        $top_students = DB::select(
					        DB::raw("SELECT `id`, `name`, `point`, `local_pic`, FIND_IN_SET( `point`, (SELECT GROUP_CONCAT( `point` ORDER BY `point` DESC ) FROM students )) AS rank FROM students ORDER BY rank")
					    );
        // $rank_ms = DB::select(
					   //      DB::raw("SELECT `id`, `name`, `point`, `local_pic`, FIND_IN_SET( `point`, (SELECT GROUP_CONCAT( `point` ORDER BY `point` DESC ) FROM students )) AS rank FROM students")

					   //  )
        //                 ->where('line_code', '=',$id)
        //                 ->orderBy('rank', 'desc')
        //                 ->get(); 
                    
        $rank_ms = DB::select(
                            DB::raw("SELECT `id`, `name`, `point`, `local_pic`, FIND_IN_SET( `point`, (SELECT GROUP_CONCAT( `point` ORDER BY `point` DESC ) FROM students )) AS rank FROM students where `line_code` = :userid ORDER BY rank DESC"),array('userid'=>$id)

                        );
                      


        // dd($rank_ms) ;
        return View::make('leaderboard')->with('top_students',$top_students)->with('rank_ms',$rank_ms);
    }
}
