<?php
  $stu = session('childdata','default');
  $pointsub1 = session('sub1','default');
  $pointsub2 = session('sub2','default');
  $meansub1 = session('meansub1','default');
  $meansub2 = session('meansub2','default');
  $classcode = session('classcode','default');
  $overall = ($meansub1[0]['mean']+$meansub2[0]['mean'])/2;
 ?>

<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>พี่หมีติวเตอร์</title>
        <link rel="stylesheet" href="css/dashboard.css" />
        <link rel="stylesheet" href="css/footer.css" />
        <link href="https://fonts.googleapis.com/css?family=Athiti|Kanit|Mitr|Roboto" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Prompt" rel="stylesheet">
        <link rel="shortcut icon" href="picture/bear_N.png">
    </head>
    <body>
        <div class="main-container">
            <div class="topnav" id="myTopnav">
                <div class="logo-nav">
                    <a href = "/">
                        <div>
                            <img class="logo" src="picture/bear_N.png">
                        </div>
                        <p class="namepi">พี่หมีติวเตอร์</p>
                    </a>
                </div>
                <div class="menu-nav">
                    <a class="menu-a" href = "/selectclass">
                        <div>
                            <img class="logo" src="picture/shuffle.png">
                        </div>
                        <p>เลือกห้อง</p>   
                    </a>
                    <a class="menu-a" href = "/newclass">
                        <div>
                            <img class="logo" src="picture/plus-symbol.png">
                        </div>
                        <p>เพิ่มห้อง</p>     
                    </a>
                    <a class="menu-a" onclick="document.getElementById('pup-code').style.display='block'">
                        <div>
                            <img class="logo" src="picture/link.png">
                        </div>
                        <p>เชื่อมต่อกับนักเรียน</p>
                    </a>
                    <a class="menu-a" href = "/aboutexam">
                        <div>
                            <img class="logo" src="picture/file (1).png">
                        </div>
                        <p>ดูข้อสอบ</p>
                    </a>
                </div>
                <div class="icon">
                    <a class="username" href ="/dashboard">{{session('name','default')}}</a>
                    <a href ="/logout">
                        <img class="logo" src="picture/exit-to-app-button.png">
                    </a>
                    <a class="icon2" onclick="topnav()">
                        <img class="logo" src="picture/hamberger.png">
                    </a>
                </div>
            </div>
            <h1 class="class-name">ห้อง {{$classcode[0]['name']}}</h1>
            <div class="section1">
                <div class="item colorGreen">
                    <label class="label">จำนวนนักเรียนในห้อง (คน)</label>
                    <div class="value">{{count($stu)}}</div>
                </div>
                <div class="item colorYellow">
                    <label class="label">ค่าเฉลี่ยของนักเรียนทั้งหมด</label>
                    <div class="layoutScore">
                        <div class="value">{{number_format($overall, 2, '.', '')}}</div>
                        <div class="smallLabel">/55</div>
                    </div>
                </div>
                <div class="item colorBlue">
                    <label class="label">อันดับของห้องเรียน</label>    
                    <div class="layoutScore">
                        <div class="value">1</div>
                        <div class="smallLabel">/10</div>
                    </div>
                </div>
            </div>
            <div class="section2">
                <div class="subject-name">
                    <p class="subjectIcon" style="background-color: #ff525b;">วิชาคณิตศาสตร์</p>
                </div>
                <div class="section2_1">
                    <div class="subSection1">
                        <h2 class="align-left">ข้อสอบทั้งหมด</h2>
                        <div class="barCon">
                            <div class="pro">
                                <div id="bar1" class="bar" style="background-color: #ff525b;"></div>
                            </div>
                            <p class="smallLabel">นักเรียนทำข้อสอบไปแล้ว {{count($pointsub1)}} จาก {{count($stu)}} คน</p>
                        </div>
                        <div>
                        {{-- <h3 class="align-left">ค่าเฉลี่ยรายวิชา</h3> --}}
                            <div class="div-score div-score-color1">
                                <div class="label-container" style="background-color: #ff525b;">
                                    <label>ค่าเฉลี่ยรายวิชา (คะแนน)</label>
                                </div>
                                <div class="layoutScore">
                                    <div class="value">{{number_format($meansub1[0]['mean'], 2, '.', '')}}</div>
                                    <div class="smallLabel">/55</div>
                                </div>
                            </div>
                            {{-- <div class="div-score div-score-color1">
                                <div class="label-container" style="background-color: #ff525b;">
                                    <label>ทำแล้ว (คน)</label>
                                </div>
                                <div class="layoutScore">
                                    <div class="value">{{count($pointsub1)}}</div>
                                    <div class="smallLabel">/{{count($stu)}}</div>
                                </div>
                            </div> --}}
                        </div>
                    </div> 
                </div>
                <div class="subSectionR">
                    <h3>การกระจายตัวของคะแนนนักเรียนทั้งหมดในห้อง</h3>
                    <div class="legend">
                        {{-- <div class="legend">
                            <div class="box-legend"></div>
                            <p>นักเรียน</p>
                        </div> --}}
                        <div class="legend">
                            <div class="box-legend2" style = "background-color: #ff525b;"></div>
                            <p>นักเรียนในระบบ</p>
                        </div>
                    </div>
                    <canvas id="barchart1"></canvas>
                </div>
            </div>
            <div class="section2">
                <div class="subject-name">
                    <p class="subjectIcon" style="background-color: #ffcc5c;">วิชาภาษาอังกฤษ</p>
                </div>
                <div class="section2_1">
                    <div class="subSection1">
                        <h2 class="align-left">ข้อสอบทั้งหมด</h2>
                            <div class="barCon">
                                <div class="pro">
                                    <div id="bar2" class="bar" style="background-color: #ffcc5c;"></div>
                                </div>
                                <p class="smallLabel">นักเรียนทำข้อสอบไปแล้ว {{count($pointsub2)}} จาก {{count($stu)}} คน</p>
                            </div>
                            <div>
                                <div class="div-score div-score-color1">
                                    <div class="label-container" style="background-color: #ffcc5c;">
                                        <label>ค่าเฉลี่ยรายวิชา (คะแนน)</label>
                                    </div>
                                    <div class="layoutScore">
                                        <div class="value">{{number_format($meansub2[0]['mean'], 2, '.', '')}}</div>
                                        <div class="smallLabel">/55</div>
                                    </div>
                                </div>
                                {{-- <div class="div-score div-score-color1">
                                    <div class="label-container" style="background-color: #ffcc5c;">
                                    <label>ทำแล้ว (คน)</label>
                                </div>
                                <div class="layoutScore">
                                    <div class="value">{{count($pointsub2)}}</div>
                                    <div class="smallLabel">/{{count($stu)}}</div>
                                </div> --}}
                            {{-- </div> --}}
                        </div>
                    </div> 
                </div>
                <div class="subSectionR">
                    <h3>การกระจายตัวของคะแนนนักเรียนทั้งหมดในห้อง</h3>
                    <div class="legend">
                        {{-- <div class="legend">
                            <div class="box-legend"></div>
                            <p>นักเรียน</p>
                        </div> --}}
                        <div class="legend">
                            <div class="box-legend2" style = "background-color : #ffcc5c;"></div>
                            <p>นักเรียนในระบบ</p>
                        </div>
                    </div>
                    <canvas id="barchart2"></canvas>  
                </div>
            </div>
            <div class="section3">
                <div class="overflow">
                    <div class="headT">
                        <h3>รายชื่อนักเรียนทั้งหมด</h3>
                        <form class="searchCon">
                            <input type="text" name="search" placeholder="Search">
                            <button type="submit">ค้นหา</button>
                        </form>
                    </div>
                    <table id="student">
                        <tr>
                            <th scope="col" rowspan="2"></th>
                            <th scope="col" rowspan="2">ชื่อ</th>
                            <th colspan="2" scope="colgroup">ค่าเฉลี่ยรายวิชา</th>
                        </tr>
                        <tr>
                            <th scope="col">คณิตศาสตร์</th>
                            <th scope="col">ภาษาอังกฤษ</th>
                        </tr>


                        @for($i = 0;$i < count($stu); $i++)
                        <tr onclick = "window.location.href = '/detailN/{{$stu[$i]['line_code']}}';">
                          <td><img class = "stuimg" src="
                                        @if($stu[$i]['local_pic'])
                                            {{$stu[$i]['local_pic']}}
                                        @else
                                            picture/bear_N.png
                                        @endif"
                                        onerror='this.src="picture/bear_N.png"'>
                         </td>
                          
                          <td>{{$stu[$i]['name']}}</td>
                          <td>
                            @php
                            $index = 0;
                            $haveData = False;
                            @endphp
                            @for($j =0;$j < count($pointsub1);$j++)
                              @if($pointsub1[$j]['line_code'] == $stu[$i]['line_code'])
                                @php
                                  $haveData = True;
                                  $index = $j;
                                @endphp
                              @endif
                            @endfor
                            @if($haveData)
                              {{number_format($pointsub1[$index]['mean'])}}
                            @else
                              -
                            @endif
                          </td>
                          <td>
                            @php
                            $index2 = 0;
                            $haveData2 = False;
                            @endphp
                            @for($j =0;$j < count($pointsub2);$j++)
                              @if($pointsub2[$j]['line_code'] == $stu[$i]['line_code'])
                                @php
                                $haveData2 = True;
                                $index2 = $j;
                                @endphp
                              @endif
                            @endfor
                            @if($haveData2)
                              {{number_format($pointsub2[$index2]['mean'])}}
                            @else
                              -
                            @endif
                          </td>
                        </tr>
                        @endfor
                    </table>
                </div>
            </div>
            <div class="test" style = "display:none;">
                {!! Form::open(['url' => 'newclassroom']) !!}
                    <input type="text" name="name" required>
                    <button class="loginBtn">Submit</button>
                {!! Form::close() !!}
            </div>
            @include('inc.footer');
            @if($errors->any())
                <script type='text/javascript'>alert('{{$errors->first()}}');</script>
            @endif

             <!-- pop-up -->
    
             <div id="pup-code" class="modal">
                <div class="modal-content animate" action="/action_page.php">
                    <div class="container">
                        <!-- <p class="headRegis"><b>เชื่อมต่อกับนักเรียน</b></p> -->
                        <p>
                            <div>
                                <img class="logo-pup" src="picture/link.png">
                            </div>
                            <p class="headRegis">เชื่อมต่อกับนักเรียน</p>
                        </p>
                        <p class="con-code"><u>{{$classcode['0']['classroom_code']}}</u></p>
                        <label>* นำรหัสที่ได้รับไปกรอกใน line ของนักเรียน</label>
                        <div class="popBtnLayout">
                            <a class="cancelBtn" type="button" onclick="document.getElementById('pup-code').style.display='none'">Close</a>
                        </div>  
                    </div>
                </div>
            </div>
             <!-- /pop-up -->
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
        <script>
            var elemMath = document.getElementById("bar1");
            var elemEng = document.getElementById("bar2");
            var maxValue = {{count($stu)}};
            move({{count($pointsub1)}},elemMath,maxValue);
            move({{count($pointsub2)}},elemEng,maxValue);
            function move(value,elem,max) {
                var width = 0;
                var id = setInterval(frame, 15);
                var data = (value/max)*100;

                function frame() {
                    if(data == 0 || max == 0){
                        elem.style.width = 0 + '%';
                    } else {
                        if (width >= data) {
                        clearInterval(id);
                        } else {
                        width++;
                        elem.style.width = width + '%';
                        }
                        }
                    }
                }
            function topnav() {
                var x = document.getElementById("myTopnav");
                if (x.className === "topnav") {
                    x.className += " responsive";
                } else {
                    x.className = "topnav";
                }
            }


            var pointsub1 = @json(session('sub1_json','default'));
            var pointsub2 = @json(session('sub2_json','default'));
            var bar = document.getElementById('barchart1').getContext('2d');
            var bar2 = document.getElementById('barchart2').getContext('2d');
            var countscorelength1 = [0,0,0,0,0,0,0,0,0,0,0];
            var countscorelength2 = [0,0,0,0,0,0,0,0,0,0,0];
            var colorarray = ["#FF525B","#FF525B","#FF525B","#FF525B","#FF525B","#FF525B","#FF525B","#FF525B","#FF525B","#FF525B","#FF525B"];
            var lengthscore = [[0,5],[6,10],[11,15],[16,20],[21,25],[26,30],[31,35],[36,40],[41,45],[46,50],[51,55]]
            var lable = ["0-5","6-10","11-15","16-20","21-25","26-30","31-35","36-40","41-45","46-50","51-55"]
            if(window.innerWidth<500){
                bar.canvas.height = 300;
                bar2.canvas.height = 300;
            }
            for (var i = 0; i < lengthscore.length; i++) {
                for (var j = 0; j < pointsub1.length; j++) {
                if (Math.round(pointsub1[j].mean) >= lengthscore[i][0] && Math.round(pointsub1[j].mean) <= lengthscore[i][1]) {
                    countscorelength1[i]++;
                }
                }for (var j = 0; j < pointsub2.length; j++) {
                if (Math.round(pointsub2[j].mean) >= lengthscore[i][0] && Math.round(pointsub2[j].mean) <= lengthscore[i][1]) {
                    countscorelength2[i]++;
                }
                }
            }
            console.log(countscorelength1,countscorelength1,pointsub1);

            var Chart1 = new Chart(bar, {
  type: 'bar',
  data: {
    datasets: [{
          // label: sub1,
          data: countscorelength1,
          backgroundColor: colorarray,
          borderWidth: 0,
          type: 'bar'
        }],
    labels:lable
  },
  options: {
    title:{
      display: false
    },
    scales: {
      yAxes: [{
        ticks: {
          beginAtZero:true,
          stepSize: 1
        },
        scaleLabel: {
          display: true,
          labelString: 'จำนวนนักเรียน'
        }
      }],
        xAxes: [{
        scaleLabel: {
        display: true,
        labelString: 'คะแนน'
        }
        }]
    },
    legend: {
            display: false
    },
    tooltips: {
        callbacks: {
            label: function(tooltipItem, data) {
              return "จำนวน"+ " "+ tooltipItem.yLabel + " " + "คน";
            },
            title : function(tooltipItem, data){
              return tooltipItem[0].xLabel + " "  + "คะแนน";
            }
          }
}
  }
});
var Chart1 = new Chart(bar2, {
  type: 'bar',
  data: {
    datasets: [{
          // label: sub2,
          data: countscorelength2,
          backgroundColor: colorarray,
          borderWidth: 0,
          type: 'bar'
        }],
    labels:lable
  },
  options: {
    title:{
      display: false
    },
    scales: {
      yAxes: [{
        ticks: {
          beginAtZero:true,
          stepSize: 1
        },
        scaleLabel: {
          display: true,
          labelString: 'จำนวนนักเรียน'
        }
      }],
      xAxes: [{
        scaleLabel: {
          display: true,
          labelString: 'คะแนน'
        }
        }]
    },
    legend: {
            display: false
    },
    tooltips: {
        callbacks: {
            label: function(tooltipItem, data) {
                return "จำนวน"+ " "+ tooltipItem.yLabel + " " + "คน";
            },
            title : function(tooltipItem, data){
              return tooltipItem[0].xLabel + " "  + "คะแนน";
            }
          }
}
  }
});
        </script>
    </body>
