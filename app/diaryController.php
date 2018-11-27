<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\pregnants as pregnants;
use App\Models\RecordOfPregnancy as RecordOfPregnancy;
use App\Models\sequents as sequents;
use App\Models\sequentsteps as sequentsteps;
use App\Models\users_register as users_register;
use App\Models\tracker as tracker;
use App\Models\question as question;
use App\Models\quizstep as quizstep;
use App\Models\reward as reward;
use App\Models\doctor as doctor;

use View;
use DB;
use Carbon\Carbon;
use DateTime;

use App\Http\Controllers\Controller;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
//use LINE\LINEBot\Event;
//use LINE\LINEBot\Event\BaseEvent;
//use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use LINE\LINEBot\MessageBuilder\LocationMessageBuilder;
use LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
use LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use LINE\LINEBot\ImagemapActionBuilder;
use LINE\LINEBot\ImagemapActionBuilder\AreaBuilder;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapMessageActionBuilder ;
use LINE\LINEBot\ImagemapActionBuilder\ImagemapUriActionBuilder;
use LINE\LINEBot\MessageBuilder\Imagemap\BaseSizeBuilder;
use LINE\LINEBot\MessageBuilder\ImagemapMessageBuilder;
use LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\DatetimePickerTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\CarouselColumnTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselTemplateBuilder;
use LINE\LINEBot\MessageBuilder\TemplateBuilder\ImageCarouselColumnTemplateBuilder;

// define('LINE_MESSAGE_CHANNEL_SECRET','f571a88a60d19bb28d06383cdd7af631');
// define('LINE_MESSAGE_ACCESS_TOKEN','omL/jl2l8TFJaYFsOI2FaZipCYhBl6fnCf3da/PEvFG1e5ADvMJaILasgLY7jhcwrR2qOr2ClpTLmveDOrTBuHNPAIz2fzbNMGr7Wwrvkz08+ZQKyQ3lUfI5RK/NVozfMhLLAgcUPY7m4UtwVwqQKwdB04t89/1O/w1cDnyilFU=');
define('LINE_MESSAGE_CHANNEL_SECRET','949b099c23a7c9ca8aebe11ad9b43a52');
define('LINE_MESSAGE_ACCESS_TOKEN','qFLN6cTuyvSWdbB1FHgUBEsD9hM66QaW3+cKz/LsNkwzMrBNZrBkH9b1zuCGp9ks0IpGRLuT6W1wLOJSWQFAlnHT/KbDBpdpyDU4VTUdY6qs5o1RTuCDsL3jTxLZnW1qbgmLytIpgi1X1vqKKsYywAdB04t89/1O/w1cDnyilFU=');

class diaryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
   
    public function show_food($id)
    {

        //$user = 'U2dc636d2cd052e82c29f5284e00f69b9';

           $record = tracker::where('user_id',$id)
                               ->whereNull('deleted_at')
                               ->where('created_at', '>=',Carbon::now()->subDays(15))
                               ->get();


        return View::make('food_diary')->with('record',$record);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show_vitamin($user)
    {

        //$user = 'U2dc636d2cd052e82c29f5284e00f69b9';
          
           $record = tracker::where('user_id',$user)
                               ->whereNull('deleted_at')
                               ->where('created_at', '>=',Carbon::now()->subDays(15))
                               ->get();


        return View::make('vitamin_diary')->with('record',$record);
    }

     public function show_exercise($user)
    {

        //$user = 'U2dc636d2cd052e82c29f5284e00f69b9';
          
           $record = tracker::where('user_id',$user)
                               ->whereNull('deleted_at')
                               ->where('created_at', '>=',Carbon::now()->subDays(15))
                               ->get();


        return View::make('exercise_diary')->with('record',$record);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

      public function show_weight($user)
    {

        //$user = 'U2dc636d2cd052e82c29f5284e00f69b9';
         // $user = 'Udb5efc89a4729c093051ce8813454223';
             $pre_weight = users_register::where('user_id', $user)
                     ->whereNull('deleted_at')
                     ->first();
              $record = DB::table('RecordOfPregnancy')
                     ->select('preg_week','preg_weight')
                     ->where('user_id', $user)
                     ->whereNull('deleted_at')
                     ->distinct()
                     ->orderBy('preg_week', 'asc')
                     ->get();

        return View::make('weight_diary')->with('record',$record)->with('pre_weight',$pre_weight);
    }
     public function personal_doctor_confirm($id)
    {

        $doctor =  doctor::where('doctor_id',$id)->first();
   
        return View::make('personal_doctor_confirm')->with('record',$doctor);
    }
       public function p_doctor(Request $request)
    {
        $doctor_id = $request->input('doctor_id'); 
        $roomId = $request->input('roomId'); 
        $user_id_line = $request->input('user_id_line'); 
        $mom_doctor = (new SqlController)->personal_doctor_mom_count($user_id_line);
        $mom_doctor = json_encode($mom_doctor,true); 
                  if($mom_doctor == null){
                     $sequentsteps = (new SqlController)->personal_doctor_mom($user_id_line,$doctor_id);
                  }else{
                     $update = (new SqlController)->personal_doctor_mom_update($user_id_line);
                     $sequentsteps = (new SqlController)->personal_doctor_mom($user_id_line,$doctor_id);
                  }
        
        $httpClient = new CurlHTTPClient(LINE_MESSAGE_ACCESS_TOKEN);
        $bot = new LINEBot($httpClient, array('channelSecret' => LINE_MESSAGE_CHANNEL_SECRET));

     
          $Message1 =  'เรียบร้อยแล้วค่ะ';
          $textMessageBuilder = new TextMessageBuilder($Message1);
          

          $response = $bot->pushMessage( $user_id_line ,$textMessageBuilder);
          $response->getHTTPStatus() . ' ' . $response->getRawBody();



      
    }
      public function weight_warning(Request $request)
    {

        $doctor_id = $request->input('doctor_id');  
        $user_id_line = $request->input('user_id_line'); 
        $text = $request->input('text'); 
        $doctor = (new SqlController)->personal_doctor_select($doctor_id);
        
        $httpClient = new CurlHTTPClient(LINE_MESSAGE_ACCESS_TOKEN);
        $bot = new LINEBot($httpClient, array('channelSecret' => LINE_MESSAGE_CHANNEL_SECRET));

        $textMessageBuilder = new TextMessageBuilder($text);
        $response = $bot->pushMessage( $user_id_line ,$textMessageBuilder);
        $response->getHTTPStatus() . ' ' . $response->getRawBody();  

        $message_type = '05';
        $log_message = (new SqlController)->log_message($user_id_line ,$text,$message_type);
 
    }
      public function disclaimer()
    {
      return View::make('disclaimer');
    }       
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
}
