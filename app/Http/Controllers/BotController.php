<?php

namespace App\Http\Controllers;

use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Event;
use LINE\LINEBot\Event\BaseEvent;
use LINE\LINEBot\Event\MessageEvent;
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
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;
use Carbon\Carbon;
use App\Prize;
use App\Exam_New;

define('LINE_MESSAGE_CHANNEL_ID', '1602719598');
define('LINE_MESSAGE_CHANNEL_SECRET', 'adc5d09e0446060bdba4cbf68a877ee9');
define('LINE_MESSAGE_ACCESS_TOKEN', 'iU3Z5u+f3Aj+nbHhqkb1NCuoXaI71Z1MUFyUfg2u8Nqb6hxMpsQw0eKEL0W2j6tFEX7XqG5tKq8RmNgkbBwcYlaBeq0l1V29lklaLNXOU6g+lDhRC2SNAhzc1b9C4SgRxUCLuIXFxH5iCyrFr5yTEQdB04t89/1O/w1cDnyilFU=');
define('SERV_NAME', 'https://pimee.softbot.ai/');
class BotController extends Controller
{
    public function index()
    {
        // เชื่อมต่อกับ LINE Messaging API
        $httpClient = new CurlHTTPClient(LINE_MESSAGE_ACCESS_TOKEN);
        $bot = new LINEBot($httpClient, array('channelSecret' => LINE_MESSAGE_CHANNEL_SECRET));
        // คำสั่งรอรับการส่งค่ามาของ LINE Messaging API
        $content = file_get_contents('php://input');
        $events = json_decode($content, true);
   
        if(!is_null($events)){
        	
            foreach ($events['events'] as $event) {

                // ถ้ามีค่า สร้างตัวแปรเก็บ replyToken ไว้ใช้งาน
                $replyInfo = $event['type'];
                $userId = $event['source']['userId'];
                 
                if ($replyInfo == "postback") {
                    $replyToken = $event['replyToken'];

                    $postbackData = $event['postback']['data'];
                    list($postback_action_part, $postback_id_part) = explode("&", $postbackData, 2);
                    list($postback_title, $postback_action) = explode("=", $postback_action_part);

                    DB::table('chat_sequences')->insert([
                        'send' => $userId,
                        'type_reply' => 4,
                        'to' => "PM",
                        'detail' => $postbackData,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                    if ($postback_action == "exchange") {

                        DB::table('user_sequences')
                            ->where('line_code', $userId)
                            ->update(['type' => "other"]);

                        list($postback_title, $postback_id) = explode("=", $postback_id_part);
                        $selected = DB::table('prizes')
                            ->where('id', $postback_id)
                            ->first();
                        $student = DB::table('students')
                            ->where('line_code', $userId)
                            ->first();
                        if ($student->point >= $selected->point) {
                            DB::table('students')
                                ->where('line_code', $userId)
                                ->update(['point' => $student->point - $selected->point]);
                            if ($selected->type_id === 1) {
                                $avail_code = DB::table('codes')
                                    ->where('prize_id', $selected->id)
                                    ->where('status', 0)
                                    ->first();
                                DB::table('codes')
                                    ->where('id', $avail_code->id)
                                    ->update(['status' => 1]);
                                DB::table('exchanges')->insert([
                                    'line_code' => $userId,
                                    'send' => 1,
                                    'code_id' => $avail_code->id,
                                    'time' => Carbon::now()
                                ]);
                                $replyData = "เก่งมาก นำโค้ดนี้ไปใช้นะ " . $avail_code->code;
                            } elseif ($selected->type_id === 2) {
                                DB::table('exchanges')->insert([
                                    'line_code' => $userId,
                                    'send' => 1,
                                    'time' => Carbon::now()
                                ]);
                                $replyData = "รอส่งสินค้านะจ๊ะ";
                            }
                        } else {
                            $replyData = "แต้มไม่พอนี่นา แลกไม่ได้นะเนี่ย";
                        }
                        DB::table('chat_sequences')->insert([
                            'send' => "PM",
                            'type_reply' => 1,
                            'to' => $userId,
                            'detail' => $replyData,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);
                        $bot->replyMessage($replyToken, new TextMessageBuilder($replyData));
                    }
                    continue;
                }
                else if ($replyInfo == "message") {
                    $replyToken = $event['replyToken'];
                    $typeMessage = $event['message']['type'];
                    
                    if($event['message']['type'] == 'sticker'){

                        DB::table('chat_sequences')->insert([
                            'send' => $userId,
                            'type_reply' => 2,
                            'to' => "PM",
                            'detail' => "packageId:".$event['message']['packageId'].",stickerId:".$event['message']['stickerId'],
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);

                        $randpack = rand(1,3);
                        if($randpack == 1){
                            $randsub = rand(1,3);
                            if($randsub == 1){
                                $sticker_id = rand(1,21); 
                            }else if ($randsub == 2) {
                                $sticker_id = rand(100,139); 
                            }else{
                                $sticker_id = rand(401,430); 
                            }
                        }else if($randpack == 2){
                            $randsub = rand(1,3);
                            if($randsub == 1){
                                $sticker_id = rand(18,47); 
                            }else if ($randsub == 2) {
                                $sticker_id = rand(140,179); 
                            }else{
                                $sticker_id = rand(501,527); 
                            }
                        }else{
                            $sticker_id = rand(180,259); 
                        }

                        DB::table('user_sequences')
                            ->where('line_code', $userId)
                            ->update(['type' => "other"]);

                        $stickerID = $sticker_id;
                        $packageID = $randpack;
                        $replyData = new StickerMessageBuilder($packageID,$stickerID);

                        DB::table('chat_sequences')->insert([
                            'send' => "PM",
                            'type_reply' => 2,
                            'to' => $userId,
                            'detail' => "packageId:".$packageID.",stickerId:".$stickerID,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);
                    }
                    else if($event['message']['type'] == 'text'){
                        $userMessage = $event['message']['text'];

                        DB::table('chat_sequences')->insert([
                            'send' => $userId,
                            'type_reply' => 1,
                            'to' => "PM",
                            'detail' => $userMessage,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);
                        if ($userMessage == "เปลี่ยนวิชา") {
                            $this->replymessage7($replyToken,'flex_message_sub',$userId);
                            $replyData = new TextMessageBuilder("วิชา");

                            DB::table('user_sequences')
                                ->where('line_code', $userId)
                                ->update(['type' => "other"]);

                            DB::table('chat_sequences')->insert([
                                'send' => "PM",
                                'type_reply' => 5,
                                'to' => $userId,
                                'detail' => "[เลือกวิชา]",
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);
                        }
                        else if($userMessage == "เปลี่ยนหัวข้อ" ){
                                $sub_name_id = DB::table('students')
                                    ->where('line_code', $userId)
                                    ->first();
                                $this->replymessage_chap($replyToken,'flex_message_chap',$sub_name_id->subject_id,$userId);
                                $replyData = new TextMessageBuilder("หัวข้อ");
                            DB::table('user_sequences')
                                ->where('line_code', $userId)
                                ->update(['type' => "other"]);
                            
                        }
                        else if($userMessage =="ดูคะแนน"){
                            $arr_replyData = array();
                            $arr_replyData[] = $this->declare_point($userId);
                            
                            $actionBuilder = array(
                                new UriTemplateActionBuilder(
                                    'ดูคะแนนย้อนหลัง', // ข้อความแสดงในปุ่ม
                                    SERV_NAME.'selectoverall/'.$userId
                                ),   
                            );
                            $imageUrl = 'https://github.com/anan211139/NECTECinternship/blob/master/img/graph.png?raw=true/700';
                            $arr_replyData[] = new TemplateMessageBuilder('Button Template',
                                new ButtonTemplateBuilder(
                                        'ดูคะแนนย้อนหลัง', // กำหนดหัวเรื่อง
                                        'หากต้องการดูคะแนนย้อนหลังทั้งหมดสามารถดูได้จากด้านล่างเลยจ้าาา', // กำหนดรายละเอียด
                                        $imageUrl, // กำหนด url รุปภาพ
                                        $actionBuilder  // กำหนด action object
                                )
                            );              
                            $multiMessage = new MultiMessageBuilder;
                            foreach ($arr_replyData as $arr_Reply) {
                                $multiMessage->add($arr_Reply);
                            }
                            $replyData = $multiMessage;

                            DB::table('user_sequences')
                                ->where('line_code', $userId)
                                ->update(['type' => "other"]);

                            DB::table('chat_sequences')->insert([
                                'send' => "PM",
                                'type_reply' => 14,
                                'to' => $userId,
                                'detail' => "[ดูคะแนนย้อนหลัง]",
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);
                        }
                        else if ($userMessage == "แลกของรางวัล") {
                            $score = DB::table('students')
                                ->where('line_code', $userId)
                                ->first();
                            $point_st = $score->point;
                            $actionBuilder = array(
                                new MessageTemplateActionBuilder(
                                    'แลกของรางวัล', // ข้อความแสดงในปุ่ม
                                    "[แลกของรางวัล]"
                                )
                            );
                            $replyData = new TemplateMessageBuilder('Button Template',
                                new ButtonTemplateBuilder(
                                    'ดูแต้มกันดีกว่า', // กำหนดหัวเรื่อง
                                    'ตอนนี้น้องมีแต้มทั้งหมด ' . $point_st . 'แต้มจ้า', // กำหนดรายละเอียด
                                    'https://github.com/anan211139/NECTECinternship/blob/master/img/score.png?raw=true/700', // กำหนด url รุปภาพ
                                    $actionBuilder  // กำหนด action object
                                )
                            );

                            DB::table('user_sequences')
                                ->where('line_code', $userId)
                                ->update(['type' => "other"]);

                            DB::table('chat_sequences')->insert([
                                'send' => "PM",
                                'type_reply' => 14,
                                'to' => $userId,
                                'detail' => "[ดูแต้มสะสม]",
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);
                        }
                        else if ($userMessage == "[แลกของรางวัล]") {
                            $re_prizes = Prize::all()->toArray();
                            $columnTemplateBuilders = array();
                            foreach ($re_prizes as $prize) {
                                $columnTemplateBuilder = new CarouselColumnTemplateBuilder(
                                    $prize['name'],
                                    'ใช้ ' . $prize['point'] . ' แต้มในการแลก',
                                    SERV_NAME.$prize['local_pic'],
                                    [
                                        new PostbackTemplateActionBuilder('แลก', http_build_query(array('action' => 'exchange', 'id' => $prize['id'])))
                                        ,]
                                );
                                array_push($columnTemplateBuilders, $columnTemplateBuilder);
                            }
                            $carouselTemplateBuilder = new CarouselTemplateBuilder($columnTemplateBuilders);
                            $replyData = new TemplateMessageBuilder('รายการ Sponser', $carouselTemplateBuilder);

                            DB::table('user_sequences')
                                ->where('line_code', $userId)
                                ->update(['type' => "other"]);

                            DB::table('chat_sequences')->insert([
                                'send' => "PM",
                                'type_reply' => 15,
                                'to' => $userId,
                                'detail' => "[แลกของรางวัล]",
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);
                        }
                        else if ($userMessage == "เพิ่มห้องเรียน") {
                            $actionBuilder = array(
                                new UriTemplateActionBuilder(
                                    'เพิ่มห้องเรียน', // ข้อความแสดงในปุ่ม
                                    'line://app/1602719598-5XVZGJ3x'
                                ),   
                            );
                            $imageUrl = null;
                            $replyData = new TemplateMessageBuilder('Button Template',
                                new ButtonTemplateBuilder(
                                        'เพิ่มห้องเรียน', // กำหนดหัวเรื่อง
                                        'น้องๆสามารถกรองรหัส เพื่อทำการเพิ่มห้องเรียนได้เลยจ้า', // กำหนดรายละเอียด
                                        $imageUrl, // กำหนด url รุปภาพ
                                        $actionBuilder  // กำหนด action object
                                )
                            );    

                            DB::table('user_sequences')
                                ->where('line_code', $userId)
                                ->update(['type' => "other"]);

                            DB::table('chat_sequences')->insert([
                                'send' => "PM",
                                'type_reply' => 14,
                                'to' => $userId,
                                'detail' => "[เพิ่มห้องเรียน]",
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);
                        }
                        else if ($userMessage == "เกี่ยวกับพี่หมี") {
                            $arr_replyData = array();
                            $textReplyMessage = "\t  สวัสดีครับน้องๆ พี่มีชื่อว่า \" พี่หมีติวเตอร์ \" ซึ่งพี่หมีจะมาช่วยน้องๆทบทวนบทเรียน\n\t โดยจะมาเป็นติวเตอร์ส่วนตัวให้กับน้องๆ ซึ่งน้องๆสามารถเลือกบทเรียนได้เอง \n\t  จะทบทวนบทเรียนตอนไหนก็ได้ตามความสะดวก ในการทบทวนบทเรียนในเเต่ละครั้ง \n\t  พี่หมีจะมีการเก็บคะแนนน้องๆไว้ เพื่อมอบของรางวัลให้น้องๆอีกด้วย \n\t  เห็นข้อดีอย่างนี้เเล้ว น้องๆจะรออะไรอยู่เล่า มาเริ่มทบทวนบทเรียนกันเถอะ!!!";
                            $arr_replyData[] = new TextMessageBuilder($textReplyMessage);
                            $textReplyMessage = "https://www.youtube.com/embed/Yad6t_EgwVw";
                            $arr_replyData[] = new TextMessageBuilder($textReplyMessage);
                            $multiMessage = new MultiMessageBuilder;
                            foreach ($arr_replyData as $arr_Reply) {
                                $multiMessage->add($arr_Reply);
                            }
                            $replyData = $multiMessage;

                            DB::table('user_sequences')
                                ->where('line_code', $userId)
                                ->update(['type' => "other"]);

                            DB::table('chat_sequences')->insert([
                                'send' => "PM",
                                'type_reply' => 1,
                                'to' => $userId,
                                'detail' => "เกี่ยวกับพี่หมี",
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);
                        } 
                        else if ($userMessage == '1' || $userMessage == '2' || $userMessage == '3' || $userMessage == '4') {
                            $seq = DB::table('user_sequences')
                                ->where('line_code', $userId)
                                ->first();
                            if($seq->type == "exam"){
                                $multiMessage = new MultiMessageBuilder;
                                $std = DB::table('students')
                                    ->where('line_code', $userId)
                                    ->first();
                                $urgroup = DB::table('groups')
                                    ->where('line_code', $userId)
                                    ->where('chapter_id', $std->chapter_id)
                                    ->orderBy('id', 'DESC')
                                    ->first();
                                $currentlog = DB::table('logChildrenQuizzes')
                                    ->where('group_id', $urgroup->id)
                                    ->orderBy('id', 'DESC')
                                    ->first();
                                $ans = DB::table('exam_news')
                                    ->where('id', $currentlog->exam_id)
                                    ->orderBy('id', 'DESC')
                                    ->first();
                                $count_quiz = DB::table('logChildrenQuizzes')
                                    ->where('group_id', $urgroup->id)
                                    ->count();
                                $ans_status = $currentlog->is_correct;
                                $sec_chance = $currentlog->second_chance;
                                $arr_replyData = array();
                                $check_st_end = false;
                                if ($ans_status === null) {
                                    if ((int)$userMessage == $ans->answer) {
                                        // ตอบถูกครั้งแรก
                                        $arr_replyData[] = new TextMessageBuilder("ถูกต้อง! เก่งจังเลย");
                                        $text_reply = "ถูกต้อง! เก่งจังเลย";
                                        $ansst = true;
                                        $check_st_end = true;
                                        DB::table('logChildrenQuizzes')
                                            ->where('id', $currentlog->id)
                                            ->update(['answer' => $userMessage, 'is_correct' => $ansst, 'time' => Carbon::now()]);
                                        if ($count_quiz < 20) {
                                            $arr_replyData[]
                                             = $this->randQuiz($replyToken,$ans->chapter_id, $ans->level_id, $urgroup->id,$text_reply,$userId);
                                        } 
                                    } else {
                                        $ansst = false;
                                        DB::table('logChildrenQuizzes')
                                            ->where('id', $currentlog->id)
                                            ->update(['answer' => $userMessage, 'is_correct' => $ansst, 'time' => Carbon::now()]);

                                        $this->replymessage_princ($replyToken,'flex_principle',$ans->principle_id,$userId);
                                        $arr_replyData[] = new TextMessageBuilder("น้องลองตอบใหม่อีกครั้งสิจ๊ะ");
                                    }
                                } else if ($ans_status == 0 && $sec_chance == 0) {
                                    $check_st_end = true;
                                    if ((int)$userMessage == $ans->answer) {
                                        $textReplyMessage = "ถูกต้อง! เก่งจังเลย";
                                        $text_reply = "ถูกต้อง! เก่งจังเลย";
                                        $arr_replyData[] = new TextMessageBuilder("ถูกต้อง! เก่งจังเลย");
                                        $ansst = true;
                                    } else {
                                        $textReplyMessage = "ยังผิดอยู่เลย ไปแก้ตัวที่ข้อต่อไปกันดีกว่า";
                                        $text_reply = "ยังผิดอยู่เลย ไปแก้ตัวที่ข้อต่อไปกันดีกว่า";
                                        $arr_replyData[] = new TextMessageBuilder("ยังผิดอยู่เลย ไปแก้ตัวที่ข้อต่อไปกันดีกว่า");
                                        $ansst = false;
                                    }
                                    DB::table('logChildrenQuizzes')
                                        ->where('id', $currentlog->id)
                                        ->update(['second_chance' => 1, 'is_correct_second' => $ansst]);
                                    if ($count_quiz < 20) {
                                        $arr_replyData[] = $this->randQuiz($replyToken,$ans->chapter_id, $ans->level_id, $urgroup->id,$text_reply,$userId);
                                    } 
                                }else{
                                    $arr_replyData[] = new TextMessageBuilder("ข้อสอบไม่เพียงพอ");
                                    DB::table('chat_sequences')->insert([
                                        'send' => "PM",
                                        'type_reply' => 1,
                                        'to' => $userId,
                                        'detail' => "ข้อสอบไม่เพียงพอ",
                                        'created_at' => Carbon::now(),
                                        'updated_at' => Carbon::now()
                                    ]);
                                }
                                if($count_quiz == 20 && $check_st_end == true){
                                    $arr_replyData[] = $this->close_group($urgroup->id);
                                }
                                foreach ($arr_replyData as $arr_Reply) {
                                    $multiMessage->add($arr_Reply);
                                }
                                $replyData =  $multiMessage;
                                DB::table('user_sequences')
                                    ->where('line_code', $userId)
                                    ->update(['type' => "exam"]);
                            }
                            else if($seq->type == "homework"){
                                $current_group_hw = DB::table('students')
                                    ->where('line_code', $userId)
                                    ->first();
                                $currentlog_hw = DB::table('homework_logs')
                                    ->where('line_code', $userId)
                                    ->where('group_hw_id',$current_group_hw->hw_group_id)
                                    ->where('send_groups_id',$current_group_hw->send_groups_id)
                                    ->orderBy('id', 'DESC')
                                    ->first();
                                DB::table('user_sequences')
                                    ->where('line_code', $userId)
                                    ->update(['type' => "homework"]);
                                $ans = DB::table('exam_news')
                                    ->where('id', $currentlog_hw->exam_id)
                                    ->orderBy('id', 'DESC')
                                    ->first();
                                
                                if ((int)$userMessage == $ans->answer) {
                                    $replyData = new TextMessageBuilder("ถูก");
                                    $ansst = true;
                                } 
                                else {
                                    $replyData = new TextMessageBuilder("ผิด");
                                    $ansst = false;       
                                }
                                DB::table('homework_logs')
                                        ->where('line_code', $currentlog_hw->line_code)
                                        ->where('group_hw_id', $currentlog_hw->group_hw_id)
                                        ->where('exam_id', $currentlog_hw->exam_id)
                                        ->where('send_groups_id',$currentlog_hw->send_groups_id)
                                        ->update(['answer' => $userMessage, 'is_correct' => $ansst, 'created_at' => Carbon::now()]);
                                $next_hw = $this->query_next_hw($replyToken,$currentlog_hw->send_groups_id,$currentlog_hw->group_hw_id,$userId);
                                $replyData = $next_hw;
                            }
                            else{
                                $replyData = new TextMessageBuilder("น้องๆยังไม่ได้อยู่ในขั้นตอนการตอบคำถาม");
                                DB::table('user_sequences')
                                    ->where('line_code', $userId)
                                    ->update(['type' => "other"]);
                                DB::table('chat_sequences')->insert([
                                    'send' => "PM",
                                    'type_reply' => 1,
                                    'to' => $userId,
                                    'detail' => "น้องๆยังไม่ได้อยู่ในขั้นตอนการตอบคำถาม",
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now()
                                ]);
                            }   
                        }
                        else if ($userMessage == "content") {
                            $replyData = new TextMessageBuilder($content);

                            DB::table('user_sequences')
                                ->where('line_code', $userId)
                                ->update(['type' => "other"]);

                            DB::table('chat_sequences')->insert([
                                'send' => "PM",
                                'type_reply' => 1,
                                'to' => $userId,
                                'detail' => $content,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);
                        }
                        else if ($userMessage == "[เพิ่มห้องเรียบร้อยแล้ว]") {

                            $replyData = new TextMessageBuilder("ขอบคุณครับบ");
                            DB::table('user_sequences')
                                ->where('line_code', $userId)
                                ->update(['type' => "other"]);

                            DB::table('chat_sequences')->insert([
                                'send' => "PM",
                                'type_reply' => 1,
                                'to' => $userId,
                                'detail' => "ขอบคุณครับบ",
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);
                        }
                        else if($userMessage == "[ลงทะเบียนเรียบร้อยแล้ว]"){
                            DB::table('user_sequences')->insert([
                                'line_code' => $userId,
                            ]);

                            $this->replymessage6($replyToken,'flex_message_sub',$userId);
                            $replyData = new TextMessageBuilder("flex_sub");

                            DB::table('user_sequences')
                                ->where('line_code', $userId)
                                ->update(['type' => "other"]);
                            DB::table('chat_sequences')->insert([
                                'send' => "PM",
                                'type_reply' => 6,
                                'to' => $userId,
                                'detail' => "เอาล่ะถ้าพร้อมแล้ว + [เลือกวิชา]",
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);
                        }
                        else if(strpos($userMessage,"[homework:") !== false){
                            $sub_string = substr($userMessage,10,-1);//2,3
                            $data = explode(',',$sub_string,2);
                            
                            $examgroup_id = $data[0];
                            $send_groups_id = $data[1];

                            DB::table('students')
                                ->where('line_code', $userId)
                                ->update(['send_groups_id' =>$send_groups_id,'hw_group_id' => $examgroup_id]);
                            DB::table('user_sequences')
                                ->where('line_code',$userId)
                                ->update(['type' => "homework"]);

                            $textReplyMessage = $this->start_homework($replyToken,$userId,$send_groups_id,$examgroup_id);
                            $replyData = new TextMessageBuilder($textReplyMessage);     
                        }
                        else if($userMessage == "จัดอันดับ"){
                            $actionBuilder = array(
                                new UriTemplateActionBuilder(
                                    'ดูลำดับ', // ข้อความแสดงในปุ่ม
                                    SERV_NAME.'leaderboard/'.$userId
                                ),
                            );
                            $imageUrl = null;
                            $replyData = new TemplateMessageBuilder('Button Template',
                                new ButtonTemplateBuilder(
                                        'จัดอันดับ', // กำหนดหัวเรื่อง
                                        'ดูลำดับคะแนนของน้องๆ ของทั้งระบบ และภายในห้องเรียน', // กำหนดรายละเอียด
                                        $imageUrl, // กำหนด url รุปภาพ
                                        $actionBuilder  // กำหนด action object
                                )
                            );  
                            DB::table('user_sequences')
                                ->where('line_code', $userId)
                                ->update(['type' => "other"]); 

                            DB::table('chat_sequences')->insert([
                                'send' => "PM",
                                'type_reply' => 14,
                                'to' => $userId,
                                'detail' => "[จัดอันดับ]",
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);    
                        }
                        else if($userMessage == "การบ้าน"){
                            $actionBuilder = array(
                                new UriTemplateActionBuilder(
                                    'ดูการบ้านทั้งหมด', // ข้อความแสดงในปุ่ม
                                    'line://app/1602719598-1A6ZJ3Pb'
                                ),
                            );
                            $imageUrl = null;
                            $replyData = new TemplateMessageBuilder('Button Template',
                                new ButtonTemplateBuilder(
                                        'การบ้าน', // กำหนดหัวเรื่อง
                                        'น้องๆสามารดูรายการ การบ้านทั้งหมด จากเมนูด้านล่างเลยจ้า', // กำหนดรายละเอียด
                                        $imageUrl, // กำหนด url รุปภาพ
                                        $actionBuilder  // กำหนด action object
                                )
                            ); 
                            DB::table('user_sequences')
                                ->where('line_code', $userId)
                                ->update(['type' => "other"]); 

                            DB::table('chat_sequences')->insert([
                                'send' => "PM",
                                'type_reply' => 14,
                                'to' => $userId,
                                'detail' => "[การบ้าน]",
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);       
                        }
                        else if ($userMessage == "ถัดไป") {
                            $replyData = new TextMessageBuilder("เปลี่ยนหน้าเรียบร้อยแล้วครับบบ");

                            DB::table('chat_sequences')->insert([
                                'send' => "PM",
                                'type_reply' => 1,
                                'to' => $userId,
                                'detail' => "เปลี่ยนหน้าเรียบร้อยแล้วครับบบ",
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]); 

                            $access_token = LINE_MESSAGE_ACCESS_TOKEN;

                            $sh = <<< EOF
                            curl -X POST \
                            -H 'Authorization: Bearer $access_token' \
                            -H 'Content-Length: 0' \
                            https://api.line.me/v2/bot/user/$userId/richmenu/richmenu-497a0063e8f64379da843bd4e59b3076
EOF;
                            $result = json_decode(shell_exec(str_replace('\\', '', str_replace(PHP_EOL, '', $sh))), true);
                        }
                        else if ($userMessage == "ก่อนหน้า") {
                            $replyData = new TextMessageBuilder("เปลี่ยนหน้าเรียบร้อยแล้วครับบบ");

                            DB::table('chat_sequences')->insert([
                                'send' => "PM",
                                'type_reply' => 1,
                                'to' => $userId,
                                'detail' => "เปลี่ยนหน้าเรียบร้อยแล้วครับบบ",
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]); 

                            $access_token = LINE_MESSAGE_ACCESS_TOKEN;

                            $sh = <<< EOF
                            curl -X POST \
                            -H 'Authorization: Bearer $access_token' \
                            -H 'Content-Length: 0' \
                            https://api.line.me/v2/bot/user/$userId/richmenu/richmenu-0fca5ca998edd876b6db81a1925aa403
EOF;
                            $result = json_decode(shell_exec(str_replace('\\', '', str_replace(PHP_EOL, '', $sh))), true);
                        }
                        else {
                            $chap_name_count = DB::table('chapters')
                                ->where('name',$userMessage)
                                ->count();
                            $sub_name_count = DB::table('subjects')
                                ->where('name',$userMessage)
                                ->count();
                            if($chap_name_count==1){
                                $chap_name_id = DB::table('chapters')
                                    ->where('name',$userMessage)
                                    ->first();
                                DB::table('students')
                                    ->where('line_code', $userId)
                                    ->update(['chapter_id' => $chap_name_id->id]);
                                DB::table('students')
                                    ->where('line_code', $userId)
                                    ->update(['subject_id' => $chap_name_id->subject_id]);
                              
                                $textReplyMessage = $this->start_exam($replyToken,$userId, $chap_name_id->id);
                                $replyData = new TextMessageBuilder($textReplyMessage);
                            }
                            else if($sub_name_count == 1){
                                $sub_name_id = DB::table('subjects')
                                    ->where('name',$userMessage)
                                    ->first();
                                DB::table('students')
                                    ->where('line_code', $userId)
                                    ->update(['subject_id' => $sub_name_id->id]);
                                $this->replymessage_chap($replyToken,'flex_message_chap',$sub_name_id->id,$userId);
                                DB::table('user_sequences')
                                    ->where('line_code', $userId)
                                    ->update(['type' => "other"]);
                                $replyData = new TextMessageBuilder("หัวข้อ");
                            }
                            else{
                                DB::table('user_sequences')
                                    ->where('line_code', $userId)
                                    ->update(['type' => "other"]);
                                
                                $text =  json_encode($userMessage, JSON_UNESCAPED_UNICODE );
                                $text1 = str_replace('"', "", $text);
                                $projectId = 'newagent-1-59441';
                                $sessionId = '123456';
                                $languageCode = 'th';
                                $userMessage =  $this->detect_intent_texts($projectId,$text1, $sessionId,$languageCode);
                                // detect_intent_texts('your-project-id','hi','123456');
                                $replyData = new TextMessageBuilder($userMessage); 

                                DB::table('chat_sequences')->insert([
                                    'send' => "PM",
                                    'type_reply' => 1,
                                    'to' => $userId,
                                    'detail' => $userMessage,
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now()
                                ]); 
                            } 
                        }
                    }
                    // ส่วนของคำสั่งตอบกลับข้อความ
                    $response = $bot->replyMessage($replyToken,$replyData);
                    
                }
                else if ($replyInfo == "unfollow") {
                    DB::table('chat_sequences')->insert([
                        'send' => $userId,
                        'type_reply' => 11,
                        'to' => "PM",
                        'detail' => "unfollow",
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                }
                else if ($replyInfo == "follow") {
                    $replyToken = $event['replyToken'];
                    DB::table('chat_sequences')->insert([
                        'send' => $userId,
                        'type_reply' => 10,
                        'to' => "PM",
                        'detail' => "follow",
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                    $multiMessage = new MultiMessageBuilder;
                    //--------INSERT AND CHECK DB--------
                    $checkIMG = DB::table('students')
                    ->where('line_code', $userId)
                    ->count();
                    if ($checkIMG == 0) {
                        $response = $bot->getProfile($userId);
                        $stdprofile = $response->getJSONDecodedBody();
                        $arr_replyData[] = new TextMessageBuilder("สวัสดีจ้านี่พี่หมีเอง\nยินดีที่เราได้เป็นเพื่อนกันนะน้อง ".$stdprofile['displayName']."\nก่อนเริ่มบทเรียน น้องๆควรดูคลิปวิธีการใช้งานด้านล่างนี้ก่อนนะ \nhttps://www.youtube.com/watch?v=ub39BTOdjeo&feature=youtu.be");
                        $actionBuilder = array(
                            new UriTemplateActionBuilder(
                                'ลงทะเบียน', // ข้อความแสดงในปุ่ม
                                //'line://app/1602719598-A6k9BE7W' // tutorbot
                                'line://app/1602719598-pK6r8oGm' // softbot
                            ),   
                        );
                        $imageUrl = SERV_NAME.'/img/img/card_regis.png';
                        $arr_replyData[] = new TemplateMessageBuilder('Button Template',
                            new ButtonTemplateBuilder(
                                'ละทะเบียนเข้าใช้งาน', // กำหนดหัวเรื่อง
                                'ก่อนจะเข้าใช้งานพี่หมีติวเตอร์ พี่หมีขอรบกวนน้องๆ กรอกข้อมูลให้พี่หมีหน่อยนะครับ', 
                                $imageUrl, // กำหนด url รุปภาพ
                                $actionBuilder  // กำหนด action object
                            )
                        );  
                        foreach ($arr_replyData as $arr_Reply) {
                            $multiMessage->add($arr_Reply);
                        }
                        $replyData = $multiMessage;
                        $url_to_image = $stdprofile['pictureUrl'];
                        // $url_to_image = 'https://profile.line-scdn.net/0m0318358a7251806587cf63da2132106605b327667a41';
                        $my_save_dir = public_path(). '/img/';
                        $filename = basename($url_to_image);
                        $complete_save_loc = $my_save_dir . $filename;
                        file_put_contents($complete_save_loc, file_get_contents($url_to_image));

                        DB::table('students')->insert([
                            'line_code' => $userId,
                            'name' => $stdprofile['displayName'],
                            'local_pic' => 'img/'.$filename,
                        ]);
                        DB::table('chat_sequences')->insert([
                            'send' => "PM",
                            'type_reply' => 6,
                            'to' => $userId,
                            'detail' => "ก่อนเริ่ม + [ลงทะเบียน]",
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);
                    }
                    // ส่วนของคำสั่งตอบกลับข้อความ
                    $response = $bot->replyMessage($replyToken,$replyData);
                }
            }
        }
    }
    public function query_next_hw($replyToken,$send_groups_id,$group_hw,$userId)
    {   
        $count_quiz = DB::table('homework_logs')
            ->where('group_hw_id', $group_hw)
            ->where('send_groups_id',$send_groups_id)
            ->where('line_code',$userId)
            ->count();
        $next = DB::table('info_examgroups')
            ->where('examgroup_id', $group_hw)
            ->offset($count_quiz)
            ->first();
        //dd($next); // query $next->exam_id ข้อสอบหมด $next จะให้ null
        if($next === null){
            $count_quiz_true = DB::table('homework_logs')
                ->where('group_hw_id', $group_hw)
                ->where('send_groups_id',$send_groups_id)
                ->where('line_code',$userId)
                ->where('is_correct', 1)
                ->count();
            DB::table('user_sequences')
                ->where('line_code', $userId)
                ->update(['type' => "other"]);
            DB::table('exam_test_groups')
                ->where('examgroup_id', $group_hw)
                ->where('send_groups_id',$send_groups_id)
                ->update(['status' => 1]);
        
            $textReplyMessage = "น้องๆทำการบ้านชุดนี้เสร็จเรียบร้อยแล้วครับ เก่งจังเลย\n พี่หมีจะบอกคะแนนและเฉลยตอนวันหมดเขตส่งนะจ๊ะ รอพี่หมีหน่อยนะ";
            DB::table('chat_sequences')->insert([
                'send' => "PM",
                'type_reply' => 1,
                'to' => $userId,
                'detail' => $textReplyMessage,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            $replyData = new TextMessageBuilder($textReplyMessage);
            return $replyData;
        }else{
            DB::table('homework_logs')->insert([
                    'line_code' => $userId,
                    'send_groups_id' => $send_groups_id,
                    'group_hw_id' => $group_hw,
                    'exam_id' => $next->exam_id,
                    'created_at' => Carbon::now()
                ]);
            $this->replymessage_hw($replyToken,($count_quiz+1),$next->exam_id,$userId);
        }
    }
    public function randQuiz($replyToken,$chapter_id, $level_id, $group_id,$text_reply,$userId){
        $num_group = DB::table('groups')
            ->where('id', $group_id)
            ->orderBy('id','DESC')
            ->first();
        $count_quiz = DB::table('logChildrenQuizzes')
            ->where('group_id', $group_id)
            ->count();
        if ($count_quiz % 5 == 0) {
            $count_quiz_true = DB::table('logChildrenQuizzes')
                ->where('group_id', $group_id)
                ->offset($count_quiz-5)
                ->limit(5)
                ->get();
            $count_num_true=0;
            foreach ($count_quiz_true as $count_true) {
                if($count_true->is_correct == 1){
                    $count_num_true++;
                }
            }
            if ($count_num_true >= 3 && $level_id < 3) {
                $level_id = $level_id + 1;
            }
            else if ($count_num_true < 3 && $level_id > 1) {
                $level_id = $level_id - 1;
            }
            $group_r = DB::table('groupRandoms')
                ->where('group_id', $group_id)
                ->first();
            $group_rand = $group_r->listlevelid;
            $concat_level = $group_rand.$level_id.',';
            DB::table('groupRandoms')
                ->where('group_id', $num_group->id)
                ->update(['listlevelid' => $concat_level]);
        }

        //declare the next quiz
        $arr_replyData = array();
        $textReplyMessage = "ข้อที่ ".($count_quiz+1);
        $arr_replyData[] = new TextMessageBuilder($textReplyMessage);
        //random the new quiz and update log, group random
        $insert_status = false;
        $round_count = 0;
        while( $insert_status == false && $round_count < 20){ //วนไรเรื่อยจนกว่าจะใส่ข้อมูลได้
            $round_count++;
            $quizzesforsubj = DB::table('exam_news')
                ->where('chapter_id', $chapter_id)
                ->where('level_id', $level_id)
                ->inRandomOrder()
                ->first();
            $group_r = DB::table('groupRandoms')
                ->where('group_id', $group_id)
                ->where('listexamid', 'like', '%,' .$quizzesforsubj->id . ',%')
                ->count();
            if($group_r == 0){  //check ไม่ซ้ำ
                $group_r = DB::table('groupRandoms')
                    ->where('group_id', $group_id)
                    ->first();
                $group_rand = $group_r->listexamid;
                $concat_quiz = $group_rand.$quizzesforsubj->id.',';
                DB::table('groupRandoms')
                    ->where('group_id', $group_id)
                    ->update(['listexamid' => $concat_quiz]);
                DB::table('logChildrenQuizzes')->insert([
                    'group_id' => $group_id,
                    'exam_id' => $quizzesforsubj->id,
                    'time' => Carbon::now()
                ]);
                $insert_status = true;
            }
        }
        if($round_count == 20){
            echo "20";
            $textReplyMessage = "ข้อสอบไม่เพียงพอ";
            $replyData = new TextMessageBuilder($textReplyMessage);
            DB::table('chat_sequences')->insert([
                'send' => "PM",
                'type_reply' => 1,
                'to' => $userId,
                'detail' => "ข้อสอบไม่เพียงพอ",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            return $replyData;
        }

        //show the new quiz
        $current_quiz = DB::table('exam_news')
            ->where('id', $quizzesforsubj->id)
            ->first();
  
        $this->replymessage_exam($replyToken,($count_quiz+1),$current_quiz->id,$text_reply,$userId);
        $pathtoexam = SERV_NAME.$current_quiz->local_pic;
        $arr_replyData[] = new ImageMessageBuilder($pathtoexam,$pathtoexam);
        return $arr_replyData;
    }
    //use this function after the student pick their own lesson
    public function start_exam($replyToken,$userId, $chapter_id) {
        $count_quiz = 0;
        $version = 0;
        $arr_replyData = array();
        $current_chapter = DB::table('chapters')
            ->where('id', $chapter_id)
            ->first();
        $old_group_count = DB::table('groups')
            ->where('line_code', $userId)
            ->where('chapter_id', $chapter_id)
            ->where('status',false)
            ->orderBy('id','DESC')
            ->count();
        // if student has finished the old group or fist time create group
        if ($old_group_count == 0) {
            $quizzesforsubj = DB::table('exam_news') //generate the first quiz
                ->where('chapter_id', $chapter_id)
                ->where('level_id', 2)
                ->inRandomOrder()
                ->first();
            if($quizzesforsubj === null){
                $textReplyMessage = "ยังไม่มีข้อสอบวิชานี้";
                return $textReplyMessage;
            }
            $group_id = DB::table('groups')->insertGetId([ //create new group
                'line_code' => $userId,
                'chapter_id' => $chapter_id,
                'status' => false
            ]);
            $tests = DB::table('groupRandoms')->insert([
                'group_id' => $group_id,
                'listexamid' => ','.$quizzesforsubj->id.',',
                'listlevelid' => "2,"
            ]);
            DB::table('logChildrenQuizzes')->insert([
                'group_id' => $group_id,
                'exam_id' => $quizzesforsubj->id,
                'time' => Carbon::now()
            ]);
            $textReplyMessage = "ยินดีต้อนรับน้องๆเข้าสู่บทเรียน\nเรื่อง ".$current_chapter->name."\nเรามาเริ่มกันที่ข้อแรกกันเลยจ้า";
            $arr_replyData[] = new TextMessageBuilder($textReplyMessage);
        }
        //if student has non-finish old group
        else { //in the future, don't forget to check the expire date
            $old_group = DB::table('groups')
                ->where('line_code', $userId)
                ->where('chapter_id', $chapter_id)
                ->orderBy('id','DESC')
                ->first();
            $group_id = $old_group->id;
            $count_quiz = DB::table('logChildrenQuizzes')
            ->where('group_id', $group_id)
            ->count();

            $version = 1;
         
            $textReplyMessage = "เรามาเริ่มบทเรียน\nเรื่อง ".$current_chapter->name."\n กันต่อ ในข้อที่ ".$count_quiz." กันเลยจ้า";
            $arr_replyData[] = new TextMessageBuilder($textReplyMessage);
        }
        //for now, there's a non-ans log for every case
        $current_log = DB::table('logChildrenQuizzes')
            ->where('group_id', $group_id)
            ->orderBy('id','DESC')
            ->first();

        $current_quiz = DB::table('exam_news')
            ->where('id', $current_log->exam_id)
            ->first();
        
        $this->replymessage_start_exam($replyToken,$current_chapter->name,$version,$count_quiz,$current_log->exam_id,$userId);
        $pathtoexam = SERV_NAME.$current_quiz->local_pic;
        $arr_replyData[] = new ImageMessageBuilder($pathtoexam,$pathtoexam);
        return $arr_replyData;
    }
    public function start_homework($replyToken,$userId,$send_groups_id,$examgroup_id) {
        $old_group_count = DB::table('exam_test_groups')
            ->where('line_code', $userId)
            ->where('examgroup_id', $examgroup_id)
            ->where('send_groups_id', $send_groups_id)
            ->where('status',false)
            ->count();
        
        if($old_group_count == 0){
            $count_quiz = 1;
            $group_id = DB::table('exam_test_groups')->insertGetId([
                'line_code' => $userId,
                'examgroup_id' => $examgroup_id,
                'send_groups_id' => $send_groups_id,
                'status' => false
            ]);
            $quiz = DB::table('info_examgroups')
                ->where('examgroup_id', $examgroup_id)
                ->orderBy('id','ASC')
                ->first();
            DB::table('homework_logs')->insert([
                'line_code' => $userId,
                'group_hw_id' => $examgroup_id,
                'send_groups_id' => $send_groups_id,
                'exam_id' => $quiz->exam_id,
                'created_at' => Carbon::now()
            ]);
            $version = 0;
        }else{
            $count_quiz = DB::table('homework_logs')
                ->where('line_code', $userId)
                ->where('send_groups_id', $send_groups_id)
                ->where('group_hw_id', $examgroup_id)
                ->count();
            $version = 1;
        }
        $this->replymessage_start_homework($replyToken,$version,$send_groups_id,$examgroup_id,$count_quiz,$userId);
        $arr_replyData[] = new TextMessageBuilder("$old_group_count");
        return $arr_replyData;
    }
    public function close_group($group_id) {
        $current_group = DB::table('groups')
            ->where('id', $group_id)
            ->first();
        $current_std = DB::table('students')
            ->where('line_code', $current_group->line_code)
            ->first();
        $all_lvl = DB::table('levels')
            ->get();
        $point_update = 0;
        foreach ($all_lvl as $lvl) {
            $point_update += ($this->results($group_id, $lvl->id)) * $lvl->id;
        }
        DB::table('students')
            ->where('line_code', $current_group->line_code)
            ->update(['point' => $current_std->point + $point_update]);
        DB::table('groups')
            ->where('id', $group_id)
            ->update(['status' => 1, 'score' => $point_update]);
        return $this->declare_point($current_group->line_code);
    }
    public function results($group_id, $level_id) {
        $current_group = DB::table('groups')
            ->where('id', $group_id)
            ->first();
        $stdanses = DB::table('logChildrenQuizzes')
            ->where('group_id', $group_id)
            ->get();
        // DB::table('groupRandoms')
        //     ->where('group_id', '=',$group_id)
        //     ->delete();
        $total_exam = 0;
        $total_true = 0;
        foreach($stdanses as $stdans) {
            $examforweight = DB::table('exam_news')
                ->where('id', $stdans->exam_id)
                ->first();
            if ($examforweight->level_id == $level_id) {
                $total_exam += 1;
                $total_true += ($stdans->is_correct ? 1 : 0);
            }
        }
        if ($total_exam != 0) {
            DB::table('results')->insert([
                'line_code' => $current_group->line_code,
                'group_id' => $group_id,
                'level_id' => $level_id,
                'total_level' => $total_exam,
                'total_level_true' => $total_true
            ]);
        }
        return $total_true;
    }
    public function declare_point($userId) {
        $group_true = DB::table('groups')
            ->where('line_code', $userId)
            ->where('status',1)
            ->orderBy('id','DESC')
            ->first();
        if($group_true === null){
            $textReplyMessage = "น้องๆยังไม่ได้ทำข้อสอบครบอย่างน้อย 1 ชุด";
        }
        else{
            $concat_result = "มาดูผลคะแนนจากข้อสอบชุดล่าสุดกันเถอะ :)";
            $group_result = DB::table('results')
                ->where('group_id',$group_true->id)
                ->get();
            foreach ($group_result as $g_result) {
                $text_result = "\nระดับ ";
                for ($i=0; $i < $g_result->level_id; $i++) { 
                    $text_result = $text_result."✩";
                }
                $text_result = $text_result.": ถูกต้อง ".$g_result->total_level_true."/".$g_result->total_level." ข้อ";
                $concat_result = $concat_result.$text_result;
            }
            $textReplyMessage = $concat_result;
        }
        DB::table('user_sequences')
            ->where('line_code', $userId)
            ->update(['type' => "other"]);
        DB::table('chat_sequences')->insert([
            'send' => "PM",
            'type_reply' => 1,
            'to' => $userId,
            'detail' => $textReplyMessage,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        $replyData = new TextMessageBuilder($textReplyMessage);
        return $replyData;
    }
    public function replymessage_chap($replyToken,$fn_json,$sub_id,$userId){  
        DB::table('chat_sequences')->insert([
            'send' => "PM",
            'type_reply' => 5,
            'to' => $userId,
            'detail' => "[เลือกหัวข้อ sub:".$sub_id."]",
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        $url = 'https://api.line.me/v2/bot/message/reply';
        $data = [
            'replyToken' => $replyToken,
            'messages' => [$this->$fn_json($sub_id)],
        ];

        DB::table('user_sequences')
                ->where('line_code', $userId)
                ->update(['type' => "other"]);

        $access_token = LINE_MESSAGE_ACCESS_TOKEN;
        $post = json_encode($data);
        $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
    }
    public function replymessage_princ($replyToken,$fn_json,$princ_id,$userId){   
        echo "replymessage_princ";
        DB::table('chat_sequences')->insert([
            'send' => "PM",
            'type_reply' => 1,
            'to' => $userId,
            'detail' => "ผิดแล้วพี่หมีจะสอนให้",
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        DB::table('chat_sequences')->insert([
            'send' => "PM",
            'type_reply' => 5,
            'to' => $userId,
            'detail' => "เฉลยหลักการที่".$princ_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        DB::table('chat_sequences')->insert([
            'send' => "PM",
            'type_reply' => 1,
            'to' => $userId,
            'detail' => "น้องๆ ลองตอบใหม่สิจ๊ะ :)",
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        $messages1 = [
            'type' => 'text',
            'text' =>  'ผิดแล้วพี่หมีจะสอนให้'
        ]; 
        $messages2 = [
            'type' => 'text',
            'text' =>  'น้องๆ ลองตอบใหม่สิจ๊ะ :)'
        ]; 
        $url = 'https://api.line.me/v2/bot/message/reply';
        $data = [
            'replyToken' => $replyToken,
            'messages' => [$messages1,$this->$fn_json($princ_id),$messages2],
        ];

        DB::table('user_sequences')
                ->where('line_code', $userId)
                ->update(['type' => "exam"]);

        $access_token = LINE_MESSAGE_ACCESS_TOKEN;
        $post = json_encode($data);
        $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
    }
    public function replymessage7($replyToken,$fn_json,$userId){   
        $url = 'https://api.line.me/v2/bot/message/reply';
        $data = [
            'replyToken' => $replyToken,
            'messages' => [$this->$fn_json()],
        ];

        DB::table('user_sequences')
            ->where('line_code', $userId)
            ->update(['type' => "other"]);

        $access_token = LINE_MESSAGE_ACCESS_TOKEN;
        $post = json_encode($data);
        $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
    }
    public function replymessage6($replyToken,$fn_json,$userId){   
        $messages1 = [
            'type' => 'text',
            'text' =>  'เอาล่ะ! ถ้าพร้อมแล้ว เรามาเลือกวิชาแรกที่จะทำข้อสอบกันเถอะ'
          ]; 
        $url = 'https://api.line.me/v2/bot/message/reply';
        $data = [
            'replyToken' => $replyToken,
            'messages' => [$messages1,$this->$fn_json()],
        ];

        DB::table('user_sequences')
                ->where('line_code', $userId)
                ->update(['type' => "other"]);

        $access_token = LINE_MESSAGE_ACCESS_TOKEN;
        $post = json_encode($data);
        $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
    }
    public function replymessage_hw($replyToken,$count_quiz,$exam_id,$userId){   
        DB::table('chat_sequences')->insert([
            'send' => "PM",
            'type_reply' => 5,
            'to' => $userId,
            'detail' => "[HW] ข้อที่".$count_quiz.",เลขที่ข้อสอบ".$exam_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        $url = 'https://api.line.me/v2/bot/message/reply';
        $exam_check_pic = DB::table('exam_news')
            ->where('id', $exam_id)
            ->first();

        if($exam_check_pic->local_pic === null){
            $data = [
                'replyToken' => $replyToken,
                'messages' => [$this->flex_choice_nonpic($count_quiz,$exam_id)],
            ];
        }
        else{
            $data = [
                'replyToken' => $replyToken,
                'messages' => [$this->flex_choice_pic($count_quiz,$exam_id)],
            ];
        }   
        DB::table('user_sequences')
                ->where('line_code', $userId)
                ->update(['type' => "homework"]);

        $access_token = LINE_MESSAGE_ACCESS_TOKEN;
        $post = json_encode($data);
        $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
    }
    public function replymessage_exam($replyToken,$count_quiz,$exam_id,$text_reply,$userId){   
        DB::table('chat_sequences')->insert([
            'send' => "PM",
            'type_reply' => 1,
            'to' => $userId,
            'detail' => $text_reply,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]); 
        DB::table('chat_sequences')->insert([
            'send' => "PM",
            'type_reply' => 5,
            'to' => $userId,
            'detail' => "[EXAM] ข้อที่".$count_quiz.",เลขที่ข้อสอบ".$exam_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);        
        $messages1 = [
            'type' => 'text',
            'text' =>  $text_reply
          ]; 
        $url = 'https://api.line.me/v2/bot/message/reply';
        $exam_check_pic = DB::table('exam_news')
            ->where('id', $exam_id)
            ->first();
        if($exam_check_pic->local_pic === null){
            $data = [
                'replyToken' => $replyToken,
                'messages' => [$messages1,$this->flex_choice_nonpic($count_quiz,$exam_id)],
            ];
        }
        else{
            $data = [
                'replyToken' => $replyToken,
                'messages' => [$messages1,$this->flex_choice_pic($count_quiz,$exam_id)],
            ];
        }   
        DB::table('user_sequences')
            ->where('line_code', $userId)
            ->update(['type' => "exam"]);
        // dd($data);//flex next quiz
        // dd($data['messages'][0]['text']);//ถูกต้อง ยังผิดอยู่เลย 


        $access_token = LINE_MESSAGE_ACCESS_TOKEN;
        $post = json_encode($data);
        $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
    }
    public function replymessage_start_homework($replyToken,$version,$send_groups_id,$group_hw,$count_quiz,$userId){ 


        $exam_id = DB::table('homework_logs')
                ->where('group_hw_id', $group_hw)
                ->where('send_groups_id',$send_groups_id)
                ->orderBy('id','DESC')
                ->first(); 
        if($version == 0){
            $messages1 = [
                'type' => 'text',
                'text' => 'เรามาเริ่มทำการบ้านข้อแรกกันเถอะ',
            ]; 
        }
        else if($version == 1){
            $messages1 = [
                'type' => 'text',
                'text' => 'เรามาทำการบ้านกันต่อ ในข้อที่ '.$count_quiz.'กันเลยจ้า',
            ]; 
        }


        DB::table('chat_sequences')->insert([
            'send' => "PM",
            'type_reply' => 1,
            'to' => $userId,
            'detail' => $messages1['text'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        DB::table('chat_sequences')->insert([
            'send' => "PM",
            'type_reply' => 5,
            'to' => $userId,
            'detail' => "[HW] ข้อที่".$count_quiz.",เลขที่ข้อสอบ".$exam_id->exam_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        $url = 'https://api.line.me/v2/bot/message/reply';
        $exam_check_pic = DB::table('exam_news')
            ->where('id',$exam_id->exam_id)
            ->first();
        if($exam_check_pic->local_pic === null){
            $data = [
                'replyToken' => $replyToken,
                'messages' => [$messages1,$this->flex_choice_nonpic($count_quiz,$exam_id->exam_id)],
            ];
        }
        else{
            $data = [
                'replyToken' => $replyToken,
                'messages' => [$messages1,$this->flex_choice_pic($count_quiz,$exam_id->exam_id)],
            ];
        }

        DB::table('user_sequences')
            ->where('line_code', $userId)
            ->update(['type' => "homework"]);

        $access_token = LINE_MESSAGE_ACCESS_TOKEN;
        $post = json_encode($data);
        $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
    }
    public function replymessage_start_exam($replyToken,$chapter,$version,$count_quiz,$exam_id,$userId){ 
        if($version == 0){
            $count_quiz++; 
            $messages1 = [
                'type' => 'text',
                'text' => 'ยินดีต้อนรับน้องๆเข้าสู่บทเรียนเรื่อง '.$chapter.'เรามาเริ่มกันที่ข้อแรกกันเลยจ้า',
            ]; 
        }
        else if($version == 1){
            $messages1 = [
                'type' => 'text',
                'text' => 'เรามาเริ่มบทเรียนเรื่อง '.$chapter.'กันต่อ ในข้อที่ '.$count_quiz.'กันเลยจ้า',
            ]; 
        }

        DB::table('chat_sequences')->insert([
            'send' => "PM",
            'type_reply' => 1,
            'to' => $userId,
            'detail' => $messages1['text'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        DB::table('chat_sequences')->insert([
            'send' => "PM",
            'type_reply' => 5,
            'to' => $userId,
            'detail' => "[EXAM] ข้อที่".$count_quiz.",เลขที่ข้อสอบ".$exam_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        $url = 'https://api.line.me/v2/bot/message/reply';
        $exam_check_pic = DB::table('exam_news')
            ->where('id', $exam_id)
            ->first();
        if($exam_check_pic->local_pic === null){
            $data = [
                'replyToken' => $replyToken,
                'messages' => [$messages1,$this->flex_choice_nonpic($count_quiz,$exam_id)],
            ];
        }
        else{
            $data = [
                'replyToken' => $replyToken,
                'messages' => [$messages1,$this->flex_choice_pic($count_quiz,$exam_id)],
            ];
        }
        DB::table('user_sequences')
            ->where('line_code', $userId)
            ->update(['type' => "exam"]);
        $access_token = LINE_MESSAGE_ACCESS_TOKEN;
        $post = json_encode($data);
        $headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);
    }
    public function flex_message_sub(){
        $query_sub = DB::table('subjects')
            ->get();
        $md_array = array();
        $key=-1;
        foreach($query_sub as $value){
            $md_array[($value->id)-1] = 
                array (
                    ++$key => 
                    array (
                      'type' => 'box',
                      'layout' => 'baseline',
                      'contents' => 
                      array (
                        0 => 
                        array (
                          'type' => 'text',
                          'text' => $value->name,
                          'action' => 
                          array (
                            'type' => 'message',
                            'text' => $value->name,
                          ),
                          'size' => 'lg',
                        ),
                      ),
                    ),
                    ++$key => 
                    array (
                      'type' => 'separator',
                      'color' => '#59BDD3',
                      'margin' => 'md',
                    ),
                );
        }
        $suject_s =array();
        foreach($md_array as $md){
            foreach($md as $key){
                array_push($suject_s,$key);
            }
        }
        $textMessageBuilder = [ 
            "type" => "flex",
            "altText" => "this is a flex message",
            "contents" => 
                            array (
                                'type' => 'bubble',
                                'styles' => 
                                array (
                                  'header' => 
                                  array (
                                    'backgroundColor' => '#59BDD3',
                                  ),
                                ),
                                'header' => 
                                array (
                                  'type' => 'box',
                                  'layout' => 'baseline',
                                  'spacing' => 'none',
                                  'contents' => 
                                  array (
                                    0 => 
                                    array (
                                      'type' => 'icon',
                                      'url' => SERV_NAME.'/img/books.png',
                                      'size' => 'xl',
                                    ),
                                    1 => 
                                    array (
                                      'type' => 'text',
                                      'text' => 'เลือกวิชา',
                                      'weight' => 'bold',
                                      'size' => 'xl',
                                      'margin' => 'md',
                                      'color' => '#ffffff',
                                    ),
                                  ),
                                ),
                                'body' => 
                                array (
                                    'type' => 'box',
                                    'layout' => 'vertical',
                                    'contents' => 
                                    $suject_s
                                )   
                            )
              ];
        return $textMessageBuilder;  
    }
    public function flex_message_chap($sub_id){
        $query_sub = DB::table('chapters')
            ->where('subject_id',$sub_id)
            ->get();
        $md_array = array();
        $key=-1;
        foreach($query_sub as $value){
            $md_array[($value->id)-1] = 
                array (
                    ++$key => 
                    array (
                      'type' => 'box',
                      'layout' => 'baseline',
                      'contents' => 
                      array (
                        0 => 
                        array (
                          'type' => 'text',
                          'text' => $value->name,
                          'wrap' => true,
                          'action' => 
                          array (
                            'type' => 'message',
                            'text' => $value->name,
                          ),
                          'size' => 'lg',
                        ),
                      ),
                    ),
                    ++$key => 
                    array (
                      'type' => 'separator',
                      'color' => '#59BDD3',
                      'margin' => 'md',
                    ),
                );
        }
        $suject_s =array();
        foreach($md_array as $md){
            foreach($md as $key){
                array_push($suject_s,$key);
            } 
        }
        $textMessageBuilder = [ 
            "type" => "flex",
            "altText" => "this is a flex message",
            "contents" => 
                array (
                    'type' => 'bubble',
                    'styles' => 
                    array (
                        'header' => 
                        array (
                        'backgroundColor' => '#59BDD3',
                        ),
                    ),
                    'header' => 
                    array (
                        'type' => 'box',
                        'layout' => 'baseline',
                        'spacing' => 'none',
                        'contents' => 
                        array (
                            0 => 
                            array (
                                'type' => 'icon',
                                'url' =>  SERV_NAME.'/img/books.png',
                                'size' => 'xl',
                            ),
                            1 => 
                            array (
                                'type' => 'text',
                                'text' => 'เลือกหัวข้อ',
                                'weight' => 'bold',
                                'size' => 'xl',
                                'margin' => 'md',
                                'color' => '#ffffff',
                            ),
                        ),
                    ),
                    'body' => 
                    array (
                        'type' => 'box',
                        'layout' => 'vertical',
                        'contents' => 
                        $suject_s
    
                    )   
                )
            ];        
        return $textMessageBuilder;      
    }
    // public function text_only(){
    //     $textMessageBuilder = [ 
    //         "type" => "flex",
    //         "altText" => "this is a flex message",
    //         "contents" => 
            
              
    //         ];   
    //     return $textMessageBuilder; 
    // }
    public function flex_principle($princ_id){

        $princ_check = DB::table('principle_news')
            ->where('id', $princ_id)
            ->first();
        //dd($princ_check);
        if($princ_check->local_pic === null){
            $textMessageBuilder = [ 
            "type" => "flex",
            "altText" => "this is a flex message",
            "contents" => 
                array (
                  'type' => 'bubble',
                  'styles' => 
                  array (
                    'header' => 
                    array (
                      'backgroundColor' => '#5FBCD1',
                    ),
                  ),
                  'header' => 
                  array (
                    'type' => 'box',
                    'layout' => 'horizontal',
                    'contents' => 
                    array (
                      0 => 
                      array (
                        'type' => 'text',
                        'text' => 'หลักการ',
                        'weight' => 'bold',
                        'color' => '#ffffff',
                        'align' => 'center',
                        'size' => 'lg',
                      ),
                    ),
                  ),
                  'body' => 
                  array (
                    'type' => 'box',
                    'layout' => 'horizontal',
                    'spacing' => 'md',
                    'contents' => 
                    array (
                      0 => 
                      array (
                        'type' => 'box',
                        'layout' => 'vertical',
                        'flex' => 2,
                        'contents' => 
                        array (
                          0 => 
                          array (
                            'type' => 'text',
                            'text' => $princ_check->detail,
                            'gravity' => 'top',
                            'size' => 'sm',
                            'wrap' => true,
                            'align' => 'center',
                          ),
                        ),
                      ),
                    ),
                  ),
                )  
            ];
        }
        else if($princ_check->detail === null){
            $textMessageBuilder = [ 
            "type" => "flex",
            "altText" => "this is a flex message",
            "contents" => 
                array (
                  'type' => 'bubble',
                  'styles' => 
                  array (
                    'header' => 
                    array (
                      'backgroundColor' => '#5FBCD1',
                    ),
                  ),
                  'header' => 
                  array (
                    'type' => 'box',
                    'layout' => 'horizontal',
                    'contents' => 
                    array (
                      0 => 
                      array (
                        'type' => 'text',
                        'text' => 'หลักการ',
                        'weight' => 'bold',
                        'color' => '#ffffff',
                        'align' => 'center',
                        'size' => 'lg',
                      ),
                    ),
                  ),
                  'hero' => 
                  array (
                    'type' => 'image',
                    'url' => SERV_NAME.$princ_check->local_pic,
                    'size' => 'full',
                    'aspectRatio' => '20:13',
                    'aspectMode' => 'cover',
                  ),
                )  
            ];
        }
        else{
            $textMessageBuilder = [ 
            "type" => "flex",
            "altText" => "this is a flex message",
            "contents" => 
                array (
                  'type' => 'bubble',
                  'styles' => 
                  array (
                    'header' => 
                    array (
                      'backgroundColor' => '#5FBCD1',
                    ),
                  ),
                  'header' => 
                  array (
                    'type' => 'box',
                    'layout' => 'horizontal',
                    'contents' => 
                    array (
                      0 => 
                      array (
                        'type' => 'text',
                        'text' => 'หลักการ',
                        'weight' => 'bold',
                        'color' => '#ffffff',
                        'align' => 'center',
                        'size' => 'lg',
                      ),
                    ),
                  ),
                  'hero' => 
                  array (
                    'type' => 'image',
                    'url' => SERV_NAME.$princ_check->local_pic,
                    'size' => 'full',
                    'aspectRatio' => '20:13',
                    'aspectMode' => 'cover',
                  ),
                  'body' => 
                  array (
                    'type' => 'box',
                    'layout' => 'horizontal',
                    'spacing' => 'md',
                    'contents' => 
                    array (
                      0 => 
                      array (
                        'type' => 'box',
                        'layout' => 'vertical',
                        'flex' => 2,
                        'contents' => 
                        array (
                          0 => 
                          array (
                            'type' => 'text',
                            'text' => $princ_check->detail,
                            'gravity' => 'top',
                            'size' => 'sm',
                            'wrap' => true,
                            'align' => 'center',
                          ),
                        ),
                      ),
                    ),
                  ),
                )  
            ];   
        }
        
        return $textMessageBuilder; 
    }
    public function flex_choice_pic($count_quiz,$exam_id){
        $exam =  DB::table('exam_news')
            ->where('id',$exam_id)
            ->first();
            $textMessageBuilder = [ 
                "type" => "flex",
                "altText" => "this is a flex message",
                "contents" => 
                array (
                    'type' => 'bubble',
                    'hero' => 
                    array (
                        'type' => 'image',
                        'url' => SERV_NAME.$exam->local_pic,
                        'size' => 'full',
                        'aspectRatio' => '20:13',
                        'aspectMode' => 'cover',
                        'action' => 
                            array (
                                'type' => 'uri',
                                'uri' => SERV_NAME.$exam->local_pic,
                            ),
                    ),
                    'body' => 
                    array (
                    'type' => 'box',
                    'layout' => 'vertical',
                    'contents' => 
                    array (
                        0 => 
                        array (
                            'type' => 'text',
                            'text' => 'ข้อที่ '.$count_quiz,
                            'weight' => 'bold',
                            'size' => 'lg',
                            'margin' => 'md',
                        ),
                        1 => 
                        array (
                            'type' => 'text',
                            'text' => $exam->question,
                            'wrap' => true,
                            'size' => 'md',
                            'margin' => 'md',
                            'color' => '#5C5C5C',
                        ),
                        2 => 
                        array (
                            'type' => 'separator',
                            'color' => '#999999',
                            'margin' => 'xl',
                        ),
                        3 => 
                        array (
                            'type' => 'box',
                            'layout' => 'horizontal',
                            'margin' => 'xl',
                            'contents' => 
                            array (
                                0 => 
                                array (
                                    'type' => 'text',
                                    'wrap' => true,
                                    'color' => '#446698',
                                    'text' => '1) '.$exam->choice_a,
                                    'action' => 
                                    array (
                                        'type' => 'message',
                                        'text' => '1',
                                    ),
                                ),
                            ),
                        ),
                        4 => 
                        array (
                            'type' => 'box',
                            'layout' => 'horizontal',
                            'margin' => 'xl',
                            'contents' => 
                            array (
                                0 => 
                                array (
                                    'type' => 'text',
                                    'wrap' => true,
                                    'color' => '#446698',
                                    'text' => '2) '.$exam->choice_b,
                                    'action' => 
                                    array (
                                        'type' => 'message',
                                        'text' => '2',
                                    ),
                                ),
                            ),
                        ),
                        5 => 
                        array (
                            'type' => 'box',
                            'layout' => 'horizontal',
                            'margin' => 'xl',
                            'contents' => 
                            array (
                                0 => 
                                array (
                                    'type' => 'text',
                                    'wrap' => true,
                                    'color' => '#446698',
                                    'text' => '3) '.$exam->choice_c,
                                    'action' => 
                                    array (
                                        'type' => 'message',
                                        'text' => '3',
                                    ),
                                ),
                            ),
                        ),
                        6 => 
                        array (
                            'type' => 'box',
                            'layout' => 'horizontal',
                            'margin' => 'xl',
                            'contents' => 
                            array (
                                0 => 
                                array (
                                    'type' => 'text',
                                    'wrap' => true,
                                    'color' => '#446698',
                                    'text' => '4) '.$exam->choice_d,
                                    'action' => 
                                    array (
                                        'type' => 'message',
                                        'text' => '4',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    ),
                )
            ];  
        return $textMessageBuilder; 
    }
    public function flex_choice_nonpic($count_quiz,$exam_id){
        $exam =  DB::table('exam_news')
            ->where('id',$exam_id)
            ->first();

            $textMessageBuilder = [ 
                "type" => "flex",
                "altText" => "this is a flex message",
                "contents" => 
                array (
                    'type' => 'bubble',
                    'body' => 
                    array (
                    'type' => 'box',
                    'layout' => 'vertical',
                    'contents' => 
                    array (
                        0 => 
                        array (
                        'type' => 'text',
                        'text' => 'ข้อที่ '.$count_quiz,
                        'weight' => 'bold',
                        'size' => 'lg',
                        'margin' => 'md',
                        ),
                        1 => 
                        array (
                        'type' => 'text',
                        //'text' => $exam->local_pic,
                        'text' => $exam->question,
                        'wrap' => true,
                        'size' => 'md',
                        'margin' => 'md',
                        'color' => '#5C5C5C',
                        ),
                        2 => 
                        array (
                        'type' => 'separator',
                        'color' => '#999999',
                        'margin' => 'xl',
                        ),
                        3 => 
                        array (
                            'type' => 'box',
                            'layout' => 'horizontal',
                            'margin' => 'xl',
                            'contents' => 
                            array (
                                0 => 
                                array (
                                    'type' => 'text',
                                    'wrap' => true,
                                    'color' => '#446698',
                                    'text' => '1) '.$exam->choice_a,
                                    'action' => 
                                    array (
                                        'type' => 'message',
                                        'text' => '1',
                                    ),
                                ),
                            ),
                        ),
                        4 => 
                        array (
                            'type' => 'box',
                            'layout' => 'horizontal',
                            'margin' => 'xl',
                            'contents' => 
                            array (
                                0 => 
                                array (
                                    'type' => 'text',
                                    'wrap' => true,
                                    'color' => '#446698',
                                    'text' => '2) '.$exam->choice_b,
                                    'action' => 
                                    array (
                                        'type' => 'message',
                                        'text' => '2',
                                    ),
                                ),
                            ),
                        ),
                        5 => 
                        array (
                            'type' => 'box',
                            'layout' => 'horizontal',
                            'margin' => 'xl',
                            'contents' => 
                            array (
                                0 => 
                                array (
                                    'type' => 'text',
                                    'wrap' => true,
                                    'color' => '#446698',
                                    'text' => '3) '.$exam->choice_c,
                                    'action' => 
                                    array (
                                        'type' => 'message',
                                        'text' => '3',
                                    ),
                                ),
                            ),
                        ),
                        6 => 
                        array (
                            'type' => 'box',
                            'layout' => 'horizontal',
                            'margin' => 'xl',
                            'contents' => 
                            array (
                                0 => 
                                array (
                                    'type' => 'text',
                                    'wrap' => true,
                                    'color' => '#446698',
                                    'text' => '4) '.$exam->choice_d,
                                    'action' => 
                                    array (
                                        'type' => 'message',
                                        'text' => '4',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    ),
                )
            ];  
        
        return $textMessageBuilder; 
    }
    public function flex_result_push(){
        DB::table('chat_sequences')->insert([
            'send' => "PM",
            'type_reply' => 5,
            'to' => $userId,
            'detail' => "[สรุปคะแนนการบ้าน]",
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        $mytime = Carbon::now();

        $result_detail = DB::table('send_groups')
            ->join('info_classrooms','info_classrooms.classroom_id','=','send_groups.room_id')
            ->join('examgroups','examgroups.id','=','send_groups.examgroup_id')
            ->select('send_groups.id as id','info_classrooms.classroom_id as room_id','info_classrooms.line_code as line_code','examgroups.name as title_hw','send_groups.key_date as key_date','send_groups.created_at as created_date','send_groups.exp_date as exp_date','examgroups.id as id_group','examgroups.parent_id as parrent_id',

                    \DB::raw("(SELECT name FROM managers
                          WHERE examgroups.parent_id = managers.id
                        ) as parent_name"),
                    \DB::raw("(SELECT count(id) FROM info_examgroups
                          WHERE info_examgroups.examgroup_id = examgroups.id
                        ) as max_point"),
                    \DB::raw("(SELECT total FROM homework_result_news
                          WHERE homework_result_news.examgroup_id = examgroups.id AND homework_result_news.send_groups_id = send_groups.id AND homework_result_news.line_code = info_classrooms.line_code
                        ) as total_point")
                )
            ->where('send_groups.key_date','<=',$mytime )
            ->where('send_groups.key_status','=',0)
            ->get();
        $line_code_arr = $result_detail->unique('line_code')->pluck('line_code')->toArray(); //ได้เด็กไม่ซ้ำแล้วจ้า
        
        foreach ($line_code_arr as $line_code_arr) {
            $result_detail_me = DB::table('send_groups')
            ->join('info_classrooms','info_classrooms.classroom_id','=','send_groups.room_id')
            ->join('examgroups','examgroups.id','=','send_groups.examgroup_id')
            ->select('send_groups.id as id','info_classrooms.classroom_id as room_id','info_classrooms.line_code as line_code','examgroups.name as title_hw','send_groups.key_date as key_date','send_groups.created_at as created_date','send_groups.exp_date as exp_date','examgroups.id as id_group','examgroups.parent_id as parrent_id',
                'send_groups.key_status as key_status',

                    \DB::raw("(SELECT name FROM managers
                          WHERE examgroups.parent_id = managers.id
                        ) as parent_name"),
                    \DB::raw("(SELECT count(id) FROM info_examgroups
                          WHERE info_examgroups.examgroup_id = examgroups.id
                        ) as max_point"),
                    \DB::raw("(SELECT total FROM homework_result_news
                          WHERE homework_result_news.examgroup_id = examgroups.id AND homework_result_news.send_groups_id = send_groups.id AND homework_result_news.line_code = info_classrooms.line_code
                        ) as total_point")
                )
            ->where('send_groups.key_date','<=',$mytime )
            ->where('info_classrooms.line_code','=',$line_code_arr)
            ->where('send_groups.key_status','=',0)
            ->get();



             $data = array(
                'to' => $line_code_arr,
                'messages' => 
                array (
                    array (
                        'type' => 'flex',
                        'altText' => 'dd',
                        'contents' => 
                        array (
                            'type' => 'carousel',
                            'contents' => 
                                array  (


                                       )
                                )   
                            )                  
                        )                   
                    );

            foreach ($result_detail_me as $result_detail) {
                if($result_detail->total_point === null){
                    $result_detail->total_point = 0;
                }
                $result_detail->exp_date =  date("d/m/Y", strtotime($result_detail->exp_date));
                $result_detail->created_date =  date("d/m/Y", strtotime($result_detail->created_date));

                $data['messages'][0]['contents']['contents'][] = array(
                    'type' => "bubble",
                    'styles' => array(
                            'header' => array(
                                'backgroundColor' => "#5FBCD1"
                            ),
                            'footer' => array(
                                'separator' => false
                            ),
                        ),
                    'header' => array(
                            'type' => "box",
                            'layout' => "horizontal",
                            'contents' => array (
                                array (
                                    'type' => 'text',
                                    'text' => 'สรุปคะแนน',
                                    'weight' => 'bold',
                                    'color' => '#ffffff',
                                    'size' => 'xl',
                                    'align' => 'center',
                                ),
                            ),

                        ),
                    'body' => array (
                        'type' => 'box',
                        'layout' => 'vertical',
                        'contents' => 
                            array (
                                array (
                                    'type' => 'text',
                                    'text' => "ชุด : ".$result_detail->title_hw,
                                    'weight' => 'bold',
                                    'align' => 'center',
                                    'size' => 'sm',
                                ),
                                array (
                                    'type' => 'text',
                                    'text' => "สร้างโดย : ".$result_detail->parent_name,
                                    'align' => 'center',
                                    'size' => 'xs',
                                    'wrap' => true,
                                ),
                                array (
                                    'type' => 'box',
                                    'layout' => 'horizontal',
                                    'margin' => 'xl',
                                    'contents' => 
                                        array (
                                            array (
                                                'type' => 'box',
                                                'layout' => 'vertical',
                                                'contents' => 
                                                array ( 
                                                    array (
                                                        'type' => 'text',
                                                        'text' => 'วันที่สั่ง',
                                                        'align' => 'center',
                                                        'size' => 'xxs',
                                                        'color' => '#aaaaaa',
                                                    ),
                                                    array (
                                                        'type' => 'text',
                                                        'text' => " ".$result_detail->created_date." ",
                                                        // date("d/m/Y", strtotime($str));
                                                        'align' => 'center',
                                                        'size' => 'xxs',
                                                        'color' => '#aaaaaa',
                                                    ),
                                                ),
                                            ),
                                        array (
                                            'type' => 'box',
                                            'layout' => 'horizontal',
                                            'margin' => 'xl',
                                            'contents' => 
                                                array (
                                                    array (
                                                        'type' => 'box',
                                                        'layout' => 'vertical',
                                                        'contents' => 
                                                            array (
                                                                array (
                                                                    'type' => 'text',
                                                                    'text' => 'วันที่กำหนดส่ง',
                                                                    'align' => 'center',
                                                                    'size' => 'xxs',
                                                                    'color' => '#aaaaaa',
                                                                ), 
                                                                array (
                                                                    'type' => 'text',
                                                                    'text' => " ".$result_detail->exp_date." ",
                                                                    'align' => 'center',
                                                                    'size' => 'xxs',
                                                                    'color' => '#aaaaaa',
                                                                    'wrap' => true,
                                                                ),
                                                            ),
                                                        ),
                                                    ),
                                                ),
                                            ),
                                        ),
                                        array (
                                            'type' => 'separator',
                                            'margin' => 'xl',
                                        ),
                                        array (
                                            'type' => 'box',
                                            'layout' => 'horizontal',
                                            'margin' => 'xl',
                                            'contents' => 
                                            array (
                                                array (
                                                    'type' => 'box',
                                                    'layout' => 'horizontal',
                                                    'contents' => 
                                                        array (
                                                            array (
                                                                'type' => 'text',
                                                                'margin' => 'xl',
                                                                'text' => 'คะแนน',
                                                                'size' => 'sm',
                                                                'flex' => 3,
                                                                'align' => 'center',
                                                                'gravity' => 'bottom',
                                                            ),
                                                            array (
                                                                'type' => 'text',
                                                                'text' => " ".$result_detail->total_point." ",
                                                                'weight' => 'bold',
                                                                'size' => 'xxl',
                                                                'color' => '#5FBCD1',
                                                                'align' => 'end',
                                                                'flex' => 0,
                                                            ), 
                                                            array (
                                                                'type' => 'text',
                                                                'text' => '/',
                                                                'size' => 'sm',
                                                                'color' => '#555555',
                                                                'gravity' => 'bottom',
                                                                'flex' => 0,
                                                            ),
                                                            array (
                                                                'type' => 'text',
                                                                'text' => " ".$result_detail->max_point." ",
                                                                'size' => 'sm',
                                                                'color' => '#555555',
                                                                'gravity' => 'bottom',
                                                                'flex' => 2,
                                                            ),
                                                        ),
                                                    ),
                                                ),
                                            ),
                                        ),
                                    ),
                    'footer' => 
                        array (
                            'type' => 'box',
                            'layout' => 'horizontal',
                            'margin' => 'xxl',
                            'contents' => 
                            array (
                                array (
                                    'type' => 'button',
                                    'flex' => 2,
                                    'style' => 'primary',
                                    'color' => '#5FBCD1',
                                    'action' => 
                                        array (
                                            'type' => 'uri',
                                            'label' => 'เฉลยละเอียด',
                                            'uri' => 'https://pimee.softbot.ai/detail_homework/'.$result_detail->line_code.'/'.$result_detail->id,
                                        ),
                                ),
                            ),
                        ),
                );
            }
            $data =json_encode($data);

            $send_result = $this->sendReplyMessage_FLEX('/push',$data);

        }

        DB::table('send_groups')
            ->where('key_date','<=',$mytime )
            ->update(['key_status' => 1]);

    }
    public function sendReplyMessage_FLEX($method, $post_body){
            $url = 'https://api.line.me/v2/bot/message';
            $API_URL        = $url;  // URL API_Messgae LINE
            $ACCESS_TOKEN   = LINE_MESSAGE_ACCESS_TOKEN;    // TOKEN of Account LINE DEV
            $CHANNELSECRET  = LINE_MESSAGE_CHANNEL_SECRET;   // CHANNEL of Account LINE DEV
            $POST_HEADER    = array('Content-Type: application/json', 'Authorization: Bearer ' . $ACCESS_TOKEN);

            $ch = curl_init($API_URL.$method);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $POST_HEADER);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            $result = curl_exec($ch);
            curl_close($ch);

            return $result;
        }
    public function notification() {
        $httpClient = new CurlHTTPClient(LINE_MESSAGE_ACCESS_TOKEN);
        $bot = new LINEBot($httpClient, array('channelSecret' => LINE_MESSAGE_CHANNEL_SECRET));
    
        $user_select = DB::table('groups')
            ->select('id','line_code')
            ->where('status', false)
            ->get();
       
        foreach ($user_select as $line_u) {
            $join_log_group = DB::table('groups')
                ->join('logChildrenQuizzes', 'logChildrenQuizzes.group_id', '=', 'groups.id')
                ->join('chapters', 'chapters.id', '=', 'groups.chapter_id')
                ->select('logChildrenQuizzes.id as log_id','chapters.name as chap_name', 'groups.id as group_id', 'groups.line_code','logChildrenQuizzes.time')
                ->where('groups.line_code', $line_u->line_code)
                ->where('groups.id', $line_u->id)
                ->orderBy('groups.id','ASC')
                ->orderBy('logChildrenQuizzes.time', 'DESC')
                ->get();
            $unfin_log = array_unique($join_log_group->pluck('chap_name')->all());
            $chap_text7 = "";
            $chap_text3 = "";
            $del_group = false;
            foreach ($unfin_log as $rest_chap) {
                $del_subj = $join_log_group->where('chap_name', $rest_chap)->first();
                if ((new Carbon($del_subj->time))->diffInDays(Carbon::now()) >= 6) {
                    DB::table('groupRandoms')
                        ->where('group_id', $del_subj->group_id)
                        ->delete();
                    DB::table('logChildrenQuizzes')
                        ->where('group_id', $del_subj->group_id)
                        ->delete();
                    DB::table('groups')
                        ->where('id', $del_subj->group_id)
                        ->delete();
                    $del_group = true;
                    $chap_text7 = $chap_text7." ".$rest_chap.",";
                    echo "MORE6".$rest_chap;
                }
                else if ((new Carbon($del_subj->time))->diffInDays(Carbon::now()) >= 2) {
                    $chap_text3 = $chap_text3." ".$rest_chap.",";
                    echo "MORE2".$rest_chap;
                }
            }

            DB::table('user_sequences')
                ->where('line_code', $line_u ->line_code)
                ->update(['type' => "other"]);

            if ($del_group == true) {
                $chap_text7 = rtrim($chap_text7, ',');
                $textReplyMessage = "ข้อสอบเรื่อง".$chap_text7." ที่ทำค้างไว้ถูกลบแล้วนะครับบบบ";
                // $replyData = new TextMessageBuilder($textReplyMessage);
                // $response = $bot->pushMessage($line_u ->line_code,$replyData);
            }
            else if (strlen($chap_text3) > 0) {
                $chap_text3 = rtrim($chap_text3, ',');
                $textReplyMessage = "กลับมาทำโจทย์เรื่อง".$chap_text3." กับพี่หมีกันเถอะ !!!!!!";
                
            }
            DB::table('chat_sequences')->insert([
                'send' => "PM",
                'type_reply' => 1,
                'to' => $userId,
                'detail' => $textReplyMessage,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            $replyData = new TextMessageBuilder($textReplyMessage);
            $response = $bot->pushMessage($line_u->line_code ,$replyData);
        }
    }
    public function notification_homework() {
        $httpClient = new CurlHTTPClient(LINE_MESSAGE_ACCESS_TOKEN);
        $bot = new LINEBot($httpClient, array('channelSecret' => LINE_MESSAGE_CHANNEL_SECRET));

        $mytime = Carbon::now();
        $room_id_homework = DB::table('send_groups') //ห้องที่มีการบ้าน แต่ยังไม่แจ้งเตือน
            ->join('info_classrooms','info_classrooms.classroom_id','=','send_groups.room_id')
            ->join('examgroups','examgroups.id','=','send_groups.examgroup_id')
            ->select('send_groups.id as id','info_classrooms.classroom_id as room_id','info_classrooms.line_code as line_code','examgroups.name as title_hw','send_groups.noti_date as noti_date','send_groups.key_date as key_date')
            ->where('send_groups.noti_status', false)
            ->get();

        foreach ($room_id_homework as $room_id_hw) {
            if($room_id_hw->noti_date <= $mytime){//เวลาตรงตามแจ้งเตือน
                DB::table('send_groups')
                    ->where('id', $room_id_hw->id)
                    ->update(['noti_status' => 1]);

                DB::table('user_sequences')
                    ->where('line_code', $room_id_hw->line_code)
                    ->update(['type' => "other"]);

                $actionBuilder = array(
                                new UriTemplateActionBuilder(
                                    'ดูการบ้านทั้งหมด', // ข้อความแสดงในปุ่ม
                                    'line://app/1602719598-1A6ZJ3Pb'
                                ),
                            );
                            $imageUrl = null;
                            $replyData = new TemplateMessageBuilder('Button Template',
                                new ButtonTemplateBuilder(
                                        'การบ้าน', // กำหนดหัวเรื่อง
                                        'วันนี้น้องๆมีการบ้านใหม่เรื่อง'.$room_id_hw->title_hw.'อย่าลืมเข้ามาทำนะครับ', // กำหนดรายละเอียด
                                        $imageUrl, // กำหนด url รุปภาพ
                                        $actionBuilder  // กำหนด action object
                                )
                            ); 
                DB::table('chat_sequences')->insert([
                    'send' => "PM",
                    'type_reply' => 14,
                    'to' => $room_id_hw->line_code,
                    'detail' => "PUSH [noti_hw :".$room_id_hw->title_hw."]",
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
                $response = $bot->pushMessage($room_id_hw->line_code,$replyData);
            }
            
        }
    }
    public function add_null_to_exp_log(){
        $mytime = Carbon::now();
        // ->addMinutes(2)
        $end_hw = DB::table('send_groups')
            ->join('info_classrooms','info_classrooms.classroom_id','=','send_groups.room_id')
            ->join('examgroups','examgroups.id','=','send_groups.examgroup_id')
            ->select('send_groups.id as id','info_classrooms.classroom_id as room_id','info_classrooms.line_code as line_code','examgroups.name as title_hw','send_groups.key_date as key_date','send_groups.created_at as created_date','send_groups.exp_date as exp_date','examgroups.id as id_group','examgroups.parent_id as parrent_id',

                    \DB::raw("(SELECT name FROM managers
                          WHERE examgroups.parent_id = managers.id
                        ) as parent_name"),
                    \DB::raw("(SELECT count(id) FROM info_examgroups
                          WHERE info_examgroups.examgroup_id = examgroups.id
                        ) as max_point")
                    ,
                    \DB::raw("(SELECT total FROM homework_result_news
                          WHERE homework_result_news.examgroup_id = examgroups.id AND homework_result_news.send_groups_id = send_groups.id AND homework_result_news.line_code = info_classrooms.line_code
                        ) as total_point")
                )
            ->where('send_groups.exp_date','<=',$mytime )
            ->where('send_groups.exp_status','=',0)
            ->get();
        foreach ($end_hw as $end_hw) {
            DB::table('send_groups')
                ->where('id',$end_hw->id)
                ->update(['exp_status' => "1"]);

            $count_quiz_true = DB::table('homework_logs')
                ->where('group_hw_id', $end_hw->id_group)
                ->where('send_groups_id',$end_hw->id)
                ->where('line_code',$end_hw->line_code)
                ->where('is_correct', 1)
                ->count();

            $count_exam_test_groups = DB::table('exam_test_groups')
                ->where('examgroup_id', $end_hw->id_group)
                ->where('send_groups_id',$end_hw->id)
                ->where('line_code',$end_hw->line_code)
                ->count();

            if($count_exam_test_groups == 1){
                DB::table('exam_test_groups')
                    ->where('examgroup_id', $end_hw->id_group)
                    ->where('send_groups_id',$end_hw->id)
                    ->where('line_code',$end_hw->line_code)
                    ->update(['status' => 1]);
            }
            else if($count_exam_test_groups == 0){
                DB::table('exam_test_groups')->insert([
                    'line_code' => $end_hw->line_code,
                    'send_groups_id' => $end_hw->id,
                    'examgroup_id' => $end_hw->id_group,
                    'status' => 1,
                    'created_at' => Carbon::now()
                ]);
            }

            DB::table('homework_result_news')->insert([
                'line_code' => $end_hw->line_code,
                'send_groups_id' => $end_hw->id,
                'examgroup_id' => $end_hw->id_group,
                'total' => $count_quiz_true,
                'created_at' => Carbon::now()
            ]);

            $count_do_quiz = DB::table('homework_logs')
                ->where('line_code',$end_hw->line_code)
                ->where('group_hw_id',$end_hw->id_group)
                ->where('send_groups_id',$end_hw->id)
                ->count();
           
            $count_max_quiz = DB::table('info_examgroups')
                ->where('examgroup_id',$end_hw->id_group)
                ->count();

            for($i=($count_do_quiz+1);$i<=$count_max_quiz;$i++){
                $next = DB::table('info_examgroups')
                    ->where('examgroup_id',$end_hw->id_group)
                    ->offset($i-1)
                    ->first();
                DB::table('homework_logs')->insert([
                    'line_code' => $end_hw->line_code,
                    'send_groups_id' => $end_hw->id,
                    'group_hw_id' => $end_hw->id_group,
                    'exam_id' => $next->exam_id,
                    'created_at' => Carbon::now()
                ]);
            }
        
        }

    }
    public function notification_homework_result() {
        $mytime = Carbon::now();
    
        $result_detail = DB::table('send_groups')
            ->join('info_classrooms','info_classrooms.classroom_id','=','send_groups.room_id')
            ->join('examgroups','examgroups.id','=','send_groups.examgroup_id')
            ->select('send_groups.id as id','info_classrooms.classroom_id as room_id','info_classrooms.line_code as line_code','examgroups.name as title_hw','send_groups.key_date as key_date','send_groups.created_at as created_date','send_groups.exp_date as exp_date','examgroups.id as id_group','examgroups.parent_id as parrent_id',

                    \DB::raw("(SELECT name FROM managers
                          WHERE examgroups.parent_id = managers.id
                        ) as parent_name"),
                    \DB::raw("(SELECT count(id) FROM info_examgroups
                          WHERE info_examgroups.examgroup_id = examgroups.id
                        ) as max_point"),
                    \DB::raw("(SELECT total FROM homework_result_news
                          WHERE homework_result_news.examgroup_id = examgroups.id AND homework_result_news.send_groups_id = send_groups.id AND homework_result_news.line_code = info_classrooms.line_code
                        ) as total_point")
                )
            ->where('send_groups.key_date','<=',$mytime )
            ->get();
        
        $line_code_arr = $result_detail->unique('line_code')->pluck('line_code')->toArray(); //ได้เด็กไม่ซ้ำแล้วจ้า
        $array = $result_detail->toArray();
    }
    public function detect_intent_texts($projectId, $text1, $sessionId, $languageCode){
        // new session
        $test = array('credentials' => 'client-secret.json');
        $sessionsClient = new SessionsClient($test);
        $session = $sessionsClient->sessionName($projectId, $sessionId ?: uniqid());
        // printf('Session path: %s' . PHP_EOL, $session);
     
        // create text input
        $textInput = new TextInput();
        $textInput->setText($text1);
        $textInput->setLanguageCode($languageCode);
     
        // create query input
        $queryInput = new QueryInput();
        $queryInput->setText($textInput);
     
        // get response and relevant info
        $response = $sessionsClient->detectIntent($session, $queryInput);
        $queryResult = $response->getQueryResult();
        $queryText = $queryResult->getQueryText();
        $intent = $queryResult->getIntent();
        $displayName = $intent->getDisplayName();
        $confidence = $queryResult->getIntentDetectionConfidence();
        $fulfilmentText = $queryResult->getFulfillmentText();
     
        $sessionsClient->close();

        return $fulfilmentText;
    }
}
