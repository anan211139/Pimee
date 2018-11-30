<?php 
    $extenlevel = json_decode(session('extenlevel','default'), true);
    $chapter = json_decode(session('jsonchapters','default'), true);
    $chaptername = json_decode(session('chaptername','default'), true);
    $childdata = json_decode(session('jsonchooeschilddata','default'), true);
    $name = json_decode(session('username','default'), true);
 
    
    $totalmax = 0;
    $totaltrue = 0;

    for ($i=0; $i < count($extenlevel) ; $i++) {
      $totalmax += $extenlevel[$i]['total_level'];
    }
    for ($i=0; $i < count($extenlevel) ; $i++) {
      $totaltrue += $extenlevel[$i]['total_level_true'];
    }
?>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>พี่หมีติวเตอร์</title>
        <link rel="stylesheet" href="css/detailChapter.css" />
        <link rel="stylesheet" href="css/footer.css" />
        <link href="https://fonts.googleapis.com/css?family=Athiti|Kanit|Mitr|Roboto" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Prompt" rel="stylesheet">
        <link rel="shortcut icon" href="picture/bear_N.png">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    </head>
    <body>
        <div class="main-container">
            <div class="content">
                <div class="topnav" id="myTopnav">
                    <div class="logo-nav">
                        <a href = "/">
                            <div>
                                <img class="logo" src="picture/bear_N.png">
                            </div>
                            <p class="namepi">พี่หมีติวเตอร์</p>
                        </a>
                    </div>
                    <div class="icon">
                        <a class="username" href ="/dashboard">{{$name}}</a>
                        <a href ="/logout">
                            <img class="logo" src="picture/exit-to-app-button.png">
                        </a>
                        <!-- <a id="hamberger" class="icon2" onclick="topnav()">
                            <img id="imgham" class="logo" src="picture/hamberger.png">
                        </a> -->
                    </div>
                </div>
                <div class="center">
                    <h1 class="head-color">บทสมการ</h1>
                    <div class="grid-container">
                        <div class="menu-homework">
                            <div class="flex-center">
                                <div class="profile-con"  onclick = "window.location.href = '/detailN';">
                                    <div>
                                        <img class="profileImg-homework" src="{{$childdata[0]['local_pic']}}">
                                    </div>
                                    <label>{{$childdata[0]['name']}}</label>
                                </div>
                            </div>
                            <div class="newcenter">
                              <div>
                                {!! Form::open(['url' => 'change_chapter']) !!}
                                  <select id = "change_chapter" name = "chapter" class="style-select" onchange="myFunction()">
                                      <option value="0">
                                          {{$chaptername[0]['name']}}
                                      </option>
                                    @foreach($chapter as $obj)
                                      <option value="{{$obj['id']}}">{{$obj['name']}}</option>
                                    @endforeach
                                  </select>
                                  <button id="submit_chapter" style = "display:none;" type = "submit">submit</button>
                                {!! Form::close() !!}
                              </div>
                            </div>
                            <script>
                            function myFunction() {
                                $("#submit_chapter").click();
                            }
                            </script>
                            <div class="section1">
                                <p>จำนวนแบบฝึกหัดที่ทำได้ครั้งล่าสุด</p>
                                <div>
                                    <img class="cup" src="picture/trophy.png">
                                </div>
                                <p class="main-score">{{$totaltrue}}/{{$totalmax}}</p>
                                <div>
                                <div class="div-score div-score-color1">
                    <label>ระดับยาก (ข้อ)</label>
                    <p class="score">
                      @php
                        $index = 0;
                        $haveData = False;
                      @endphp
                      @for($i=0;$i<count($extenlevel);$i++)
                        @if($extenlevel[$i]['level_id'] == 3)
                          @php
                            $haveData = True;
                            $index = $i;
                          @endphp
                        @endif
                      @endfor
                      @if($haveData)
                        {{$extenlevel[$index]['total_level_true']}}/{{$extenlevel[$index]['total_level']}}
                      @else
                        0/0
                      @endif
                    </p>
                </div>
                <div class="div-score div-score-color2">
                    <label>ระดับกลาง (ข้อ)</label>
                    <p class="score">
                      @php
                        $index2 = 0;
                        $haveData2 = False;
                      @endphp
                      @for($i=0;$i<count($extenlevel);$i++)
                        @if($extenlevel[$i]['level_id'] == 2)
                          @php
                            $haveData2 = True;
                            $index2 = $i;
                          @endphp
                        @endif
                      @endfor
                      @if($haveData2)
                        {{$extenlevel[$index2]['total_level_true']}}/{{$extenlevel[$index2]['total_level']}}
                      @else
                        0/0
                      @endif
                    </p>
                </div>
                <div class="div-score div-score-color3">
                    <label>ระดับง่าย (ข้อ)</label>
                    <p class="score">
                      @php
                        $index3 = 0;
                        $haveData3 = False;
                      @endphp
                      @for($i=0;$i<count($extenlevel);$i++)
                        @if($extenlevel[$i]['level_id'] == 1)
                          @php
                            $haveData3 = True;
                            $index3 = $i;
                          @endphp
                        @endif
                      @endfor
                      @if($haveData3)
                        {{$extenlevel[$index3]['total_level_true']}}/{{$extenlevel[$index3]['total_level']}}
                      @else
                        0/0
                      @endif
                    </p>
                </div>
                                </div>
                            </div>
                        </div>
                        <div class="section2">
                            <div class="sub-section">
                                <canvas id="linechart"></canvas>
                            </div>
                            <div class="sub-section">
                                <canvas id="barchart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="footer">
                    <div class="footerCenter">
                        <div class="tooltip">
                            <img class="imgContact" src="picture/facebook-logo-button.png">
                            <span class="tooltiptext">พี่หมีติวเตอร์</span>
                        </div>
                        <div class="tooltip">
                            <img class="imgContact" src="picture/linefooter.png">
                            <span class="tooltiptext">@พี่หมีติวเตอร์</span>
                        </div>
                        <div class="tooltip">
                            <img class="imgContact" src="picture/web.png">
                            <span class="tooltiptext">www.พี่หมีติวเตอร์.com</span>
                        </div>
                    </div>
                    <div class="footerLeft">
                            <img class="nectecLogo" src="picture/Nectec_logo.png">
                    </div>
                </div> -->
                @include('inc.footer');
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
        <script>
                Chart.defaults.global.defaultFontSize = 15;
                Chart.defaults.global.defaultFontFamily = 'Kanit', 'sans-serif';

                var student = @json(session('student_score_chapter','default'));
                var overall = @json(session('overall_score','default'));
                var st_count = @json(session('student_count','default'));

                var student_score_chapter = [];
                var chapter_group = [];
                var overall_score = [];
                var student_count = [];
                var scorebar = [];
                var countscorelength = [0,0,0,0,0,0,0,0,0,0,0];


                student_count.push(st_count[0].count);
                overall_score.push((overall[0].score/student_count).toFixed(2));
                for (var i = 1; i <= student.length; i++) {
                    student_score_chapter.push(Number(student[i-1].score));
                    chapter_group.push('ชุดที่ '+ i);
                }if (student.length) {
                    scorebar.push(Number(student[student.length-1].score));
                }
                
                var bar = document.getElementById('barchart').getContext('2d');
                  var line = document.getElementById('linechart').getContext('2d');
                var lineChart = new Chart (line,{
                  type:'line',
                  data:{
                    labels:[1,2,3,4,5,6,7,8,9,10],
                    datasets:[
                      {
                        label : "นักเรียน",
                        borderColor: ["#5cbcd2"],
                        fill :false,
                        data: student_score_chapter
                      }
                    ]
                  },
                  options:{
                    title:{
                      display: true,
                      text: 'คะแนนทีนักเรียนทำได้ในแต่ละครั้ง'
                    }
                  }
                }
                );
                
                var barChart = new Chart (bar,{
                  type:'bar',
                  data:{
                    datasets:[
                      {
                        label: "นักเรียน",
                        backgroundColor: "#5cbcd2",
                        data:  scorebar
                      },
                      {
                        label: "นักเรียนทั้งหมดในระบบ",
                        backgroundColor: "#007d91",
                        data: overall_score
                      }
                    ]
                  },
                  options:{
                    title:{
                      display: true,
                      text: 'คะแนนที่นักเรียนได้ในครั้งล่าสุดเปรียบเทียบกับคะแนนเฉลี่ยของนักเรียนทั้งหมดในระบบ'
                    },
                    scales: {
                      yAxes: [{
                      ticks: {
                      beginAtZero:true
                        }
                      }]
                    }
                  }
                }
                );
            </script>
    </body>
</html>