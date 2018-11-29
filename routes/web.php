<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/test', 'Pagecontroller@getLaravelpage');
Route::get('/', 'Pagecontroller@gethome');
Route::get('/Laravel', 'Pagecontroller@getLaravelpage');
Route::get('/logout', 'logoutcontroller@logout');
Route::get('/connectchild/{id}','Pagecontroller@addchild');  //old
Route::get('/addchild','Pagecontroller@addchildpage');       //old
Route::get('/user', 'Pagecontroller@gethome');
Route::get('/choose', 'Pagecontroller@getchoosepage'); //subset of /user
Route::get('/step','Pagecontroller@getsteppage'); //subset of /user
Route::get('/userpage','Pagecontroller@getuserpage'); //subset of /user
Route::get('/choosechild/{id}','addchildcontroller@sessionaddchild');
Route::get('/selectoverall/{id}','graph_overallController@main'); //don't use
Route::get('/selectchapter/{subject}/{chapter}','graph_chapterController@main');
Route::get('/selectsubject/{id}','graph_subjectController@main');
Route::get('/dashboard','Pagecontroller@dashboard');
Route::get('/studentinfo','Pagecontroller@studentinfo');       //liff
Route::get('/connect','Pagecontroller@connectpage');  //liff
Route::get('/selectclass','Pagecontroller@selectclass');
Route::get('/selectclass/select/{id}','Pagecontroller@chooesclassroom');
Route::get('/newclass','Pagecontroller@newclass');
Route::get('/status','Pagecontroller@status');
Route::get('/error','Pagecontroller@error');            
Route::get('/removeclass/{id}','manageClassroom@removeclass');
Route::get('/aboutexam','Pagecontroller@aboutexam'); 
Route::get('/Admin','Pagecontroller@loginadminpage'); 
Route::get('/Adminpage','Pagecontroller@adminpage'); 
Route::get('/detailchapter','Pagecontroller@detailchapter');
Route::get('/detailN','Pagecontroller@detailN'); 
Route::get('/detailN/{id}','detail_new_Controller@calldetailN');
Route::get('/detailchapter/{id}','detail_new_Controller@calldetailchapter');
Route::get('/newgroupexam','Pagecontroller@newgroupexam');
Route::get('/mail/deleteEachfeedback', 'AdminPageController@deleteEachFeedback') ->name('delete.eachfeedback');
Route::get('/mail/deletefeedback', 'AdminPageController@deleteFeedback') ->name('delete.feedback');
Route::get('/addExam_admin', 'AdminPageController@addExam');
Route::get('/addExam_teacher', 'addExamTeacherController@index');
Route::get('/mail/readmail/{id}', 'AdminPageController@readMail');
Route::get('/mail', 'AdminPageController@index') ;//เรียกหน้าจดหมายของแอดมิน

Route::post('/loginsubmit', 'checklogin@pslogin');
Route::post('/loginadminsubmit', 'checklogin@adminlogin');
Route::post('/loginsubmitinaddchild', 'checklogin@pslogininaddchild');
Route::post('/regissubmit', 'regisController@process');
Route::post('/regissubmitinaddchild', 'regisController@processinaddchild');
Route::post('/addchildsubmit','addchildcontroller@addchild');
Route::post('/newclassroom', 'manageClassroom@newclassroom');
Route::post('/includedatastu/sent', 'addchildcontroller@includedata');
Route::post('/sent_connectroom','Ajaxcontroller@sentconnectpageByajax')->name('sent_connectroom');
Route::post('/checkrommcode','Ajaxcontroller@checkrommcode')->name('checkrommcode');
Route::post('/sent_studentdata','Ajaxcontroller@sentstudataByajax')->name('sent_studentdata');
Route::post('/selectexam','Ajaxcontroller@selectexam')->name('selectexam');
Route::post('/selectchapterAjax','Ajaxcontroller@selectchapterAjax')->name('selectchapterAjax');
Route::post('/updateexamlist','Ajaxcontroller@updateexamlist')->name('updateexamlist');
Route::post('/Ajaxsendreport','Ajaxcontroller@Ajaxsendreport')->name('Ajaxsendreport');
Route::post('/Ajaxquerygroupexam','Ajaxcontroller@Ajaxquerygroupexam')->name('Ajaxquerygroupexam');
Route::post('/addtogroup','groupexamController@addtogroup');
Route::post('/change_chapter', 'detail_new_Controller@change_chapter');
Route::post('/queryhomeworkresult','Ajaxcontroller@queryhomeworkresult')->name('queryhomeworkresult');
Route::post('/sendexamtoroom', 'groupexamController@sendexamtoroom');
Route::post('/newgroup', 'groupexamController@newgroup');
Route::post('/addFeedback', 'PageController@addFeedback') ->name('addFeedback');
Route::post('/addExamSubmit_teacher', 'addExamTeacherController@addExamSubmit');
Route::post('/addExam_admin/subject', 'AdminPageController@subject') ->name('dropdown.addExam_subject');
Route::post('/addExamSubmit', 'AdminPageController@addExamSubmit');
Route::post('/mail/sort', 'AdminPageController@sort') ->name('dropdown.mail_ad'); //เลือกoptionหน้าจดหมายของแอดมิน


Route::get('/leaderboard/{id}','SqlController@leaderboard');
Route::get('/homeworkpage','SqlController@callhomeworkpage');
Route::post('/homework','SqlController@homework')->name('homework_value');
Route::post('/homework2','SqlController@homework2')->name('homework_value2');
Route::get('/detail_homework/{id}/{send_group_id}','SqlController@detail_homework');


Route::resource('Line','LineController');  //call path ที่กำหนด

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/noti','BotController@notification');

Route::get('/testDomain', function(){
        return "Successfully Test Website";
});

