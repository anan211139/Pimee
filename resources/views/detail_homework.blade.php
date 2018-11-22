<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>พี่หมีติวเตอร์</title>
        <link rel="stylesheet" href="../../css/studentinfo.css" />
        <link rel="stylesheet" href="../../css/detail_homework.css" />
        <link href="https://fonts.googleapis.com/css?family=Athiti|Kanit|Mitr|Roboto" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Prompt" rel="stylesheet">
    </head>
    <body class="homework_detail">

        <h1>เฉลยละเอียด</h1>
        <div class="flex title">
          @foreach($exam_topic as $exam_topic)
            <p>ชุด : {{ $exam_topic->subject }}</p>
            <p>โดย : คุณครู {{ $exam_topic->name_parent }}</p>
          @endforeach
        </div>
        <div class="box">
          @foreach($result as $result)
           <div class="key_detail">
              <div class="principle">
                 <a>ข้อ {{$result['count_quiz']}}</a>
               </div>
               <?php
                if($result['local_pic_exam']!==null){
               ?>
                  <img class="show_img" src="https://pimee.softbot.ai/{{$result['local_pic_exam']}}" />
               <?php
                }
               ?>
               
               <p>{{$result['exam']}}</p>
               <table>
                  <tr>
                    <td class="index">
                      <?php 
                        if($result['true_ans']==1){
                      ?>
                          <img class="check" src="../../img/true1.png"/>
                      <?php
                        }
                        else if($result['ans_selec']==1){
                      ?>
                          <img class="check" src="../../img/false1.png"/>
                      <?php 
                        }
                      ?>
                      
                      <p>A.</p>
                    </td>
                    <td class="detail">{{$result['choice_a']}}</td>
                    <td class="index">
                      <?php 
                        if($result['true_ans']==2){
                      ?>
                          <img class="check" src="../../img/true1.png"/>
                      <?php
                        }
                        else if($result['ans_selec']==2){
                      ?>
                          <img class="check" src="../../img/false1.png"/>
                      <?php 
                        }
                      ?>
                      <p>B.</p>
                    </td>
                    <td class="detail">{{$result['choice_b']}}</td>
                  </tr>
                  <tr>
                    <td class="index">
                      <?php 
                        if($result['true_ans']==3){
                      ?>
                          <img class="check" src="../../img/true1.png"/>
                      <?php
                        }
                        else if($result['ans_selec']==3){
                      ?>
                          <img class="check" src="../../img/false1.png"/>
                      <?php 
                        }
                      ?>
                      <p>C.</p>
                    </td>
                    <td class="detail">{{$result['choice_c']}}</td>
                    <td class="index">
                      <?php 
                        if($result['true_ans']==4){
                      ?>
                          <img class="check" src="../../img/true1.png"/>
                      <?php
                        }
                        else if($result['ans_selec']==4){
                      ?>
                          <img class="check" src="../../img/false1.png"/>
                      <?php 
                        }
                      ?>
                      <p>D.</p>
                    </td>
                    <td class="detail">{{$result['choice_d']}}</td>
                  </tr>
               </table>
               <h3>หลักการ</h3>
               
               <?php
                if($result['local_pic_princ']!==null){
               ?>
                  <img class="show_img" src="https://pimee.softbot.ai/{{$result['local_pic_princ']}}" />
               <?php
                }
               ?>
               <p class="detail">{{$result['princ_detail']}}</p>
               <hr/>
           </div>
          @endforeach
        </div> 
    </body>
</html>