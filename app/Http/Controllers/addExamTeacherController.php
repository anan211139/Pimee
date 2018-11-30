<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class addExamTeacherController extends Controller
{
  public function index(){
    $subject = DB::table('subjects')
    ->get();
    return view('add_exam_teacher')
    ->with('subject', $subject);
  }

  public function addExamSubmit(Request $request){
      $user_id = session('id', 'default');
    $chapter_id = $request->input('chapter');
    $question = $request->input('exam');
    $choice_a = $request->input('choice1');
    $choice_b = $request->input('choice2');
    $choice_c = $request->input('choice3');
    $choice_d = $request->input('choice4');
    $answer = $request->input('answer');
    $level_id = 2;

    $img = $request->file('image');
    $input['imagename'] = time().'.'.$img->getClientOriginalExtension();
    $destinationpath = public_path('/img/exam');
    $img->move($destinationpath, $input['imagename']);

    $path = 'img/exam/'.$input['imagename'];

    DB::table('exam_news')->insert(
      ['chapter_id' => $chapter_id,
      'question' => $question,
      'choice_a' => $choice_a,
      'choice_b' => $choice_b,
      'choice_c' => $choice_c,
      'choice_d' => $choice_d,
      'answer' => $answer,
      'level_id' => $level_id,
      'local_pic' => $path,
        'parent_id' => $user_id,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
        ]
    );
    return redirect('/addExam_teacher');
  }
}
