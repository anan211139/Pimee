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
Route::get('/', 'Pagecontroller@gethome');
Route::get('/Laravel', 'Pagecontroller@getLaravelpage');
Route::get('/logout', 'logoutcontroller@logout');
Route::get('/connectchild/{id}','Pagecontroller@addchild');
Route::get('/addchild','Pagecontroller@addchildpage');
Route::get('/user', 'Pagecontroller@gethome');
Route::get('/choose', 'Pagecontroller@getchoosepage'); //subset of /user
Route::get('/step','Pagecontroller@getsteppage'); //subset of /user
Route::get('/userpage','Pagecontroller@getuserpage'); //subset of /user
Route::get('/choosechild/{id}','addchildcontroller@sessionaddchild');
Route::get('/selectoverall/{id}','graph_overallController@main');
Route::get('/selectchapter/{subject}/{chapter}','graph_chapterController@main');
Route::get('/selectsubject/{id}','graph_subjectController@main');
Route::get('/dashboard','Pagecontroller@dashboard');
//Route::get('/studentinfo/{id}','Pagecontroller@studentinfo');
Route::get('/studentinfo','Pagecontroller@studentinfo');
Route::post('/loginsubmit', 'checklogin@pslogin');
Route::post('/loginsubmitinaddchild', 'checklogin@pslogininaddchild');
Route::post('/regissubmit', 'regisController@process');
Route::post('/regissubmitinaddchild', 'regisController@processinaddchild');
Route::post('/addchildsubmit','addchildcontroller@addchild');




Route::get('/leaderboard/{id}','SqlController@leaderboard');
Route::get('/homeworkpage','SqlController@callhomeworkpage');

Route::post('/homework','SqlController@homework')->name('homework_value');
Route::post('/homework2','SqlController@homework2')->name('homework_value2');

Route::get('/detail_homework/{id}/{send_group_id}','SqlController@detail_homework');
// Route::get('/leaderboard','SqlController@rank_ms');
// Route::get('/homework/{id}','SqlController@homework');
// Route::post('/homework','BotController@homework');


Route::resource('Line','LineController');  //call path ที่กำหนด

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/noti','BotController@notification');

Route::get('/testDomain', function(){
        return "Successfully Test Website";
});

