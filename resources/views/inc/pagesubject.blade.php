<?php
  $subject_status = session('subject_status','default');
  $max = json_decode(session('maxchapter','default'), true);
  $score = json_decode(session('scorechapter','default'), true);
  $sumoverall = json_decode(session('sumoverall','default'), true);
  $substatus = json_decode(session('substatus','default'), true);
?>
<div class="center">
    <h1>{{$substatus[0]['name']}}</h1>
    <div class="section2">
        <div class="sub-section">
            <p>จำนวนแบบฝึกหัดที่ทำได้</p>
            <div class="layout-score-subject">
                <div>
                    <img class="cup" src="picture/trophy.png">
                </div>
                <div>
                    <p class="main-score-subject">@if($sumoverall[0]['max']){{$sumoverall[0]['true']}}/{{$sumoverall[0]['max']}}@else 0/0 @endif</p>
                    <label>จำนวนข้อทั้งหมดที่ทำได้</label>
                </div>
            </div>
            @if($subject_status == 1)
            <div class="layout-score2">
                <div class="div-score div-score-color1">
                    <label>{{$chapther_list[0]['name']}} (ข้อ)</label>
                    <p class="score">
                      @php
                      $index = 0;
                      $haveData = False;
                      @endphp
                      @for($i=0;$i<2;$i++)
                        @if(isset($max[$i]['max']))
                          @if($max[$i]['id'] == 1)
                            @php
                              $haveData = True;
                              $index = $i;
                            @endphp
                          @endif
                        @endif
                      @endfor
                      @if($haveData)
                        {{$score[$index]['score']}}/{{$max[$index]['max']}}
                      @else
                        0/0
                      @endif
                    </p>
                </div>
                <div class="div-score div-score-color2">
                    <label>{{$chapther_list[1]['name']}} (ข้อ)</label>
                    <p class="score">
                      @php
                      $index2 = 0;
                      $haveData2 = False;
                      @endphp
                      @for($i=0;$i<2;$i++)
                        @if(isset($max[$i]['max']))
                          @if($max[$i]['id'] == 2)
                            @php
                              $haveData2 = True;
                              $index2 = $i;
                            @endphp
                          @endif
                        @endif
                      @endfor
                      @if($haveData2)
                        {{$score[$index2]['score']}}/{{$max[$index2]['max']}}
                      @else
                        0/0
                      @endif
                    </p>
                </div>
            </div>
            
            @elseif($subject_status == 2)
            <div class="layout-score2">
                <div class="div-score div-score-color1">
                    <label>{{$chapther_list[2]['name']}} (ข้อ)</label>
                    <p class="score">
                    @php
                    $index = 0;
                    $haveData = False;
                    @endphp
                    @for($i=0;$i<2;$i++)
                      @if(isset($max[$i]['max']))
                        @if($max[$i]['id'] == 3)
                          @php
                            $haveData = True;
                            $index = $i;
                          @endphp
                        @endif
                      @endif
                    @endfor
                    @if($haveData)
                      {{$score[$index]['score']}}/{{$max[$index]['max']}}
                    @else
                      0/0
                    @endif</p>
                </div>
                <div class="div-score div-score-color2">
                    <label>{{$chapther_list[3]['name']}} (ข้อ)</label>
                    <p class="score">
                      @php
                      $index2 = 0;
                      $haveData2 = False;
                      @endphp
                      @for($i=0;$i<2;$i++)
                        @if(isset($max[$i]['max']))
                          @if($max[$i]['id'] == 4)
                            @php
                              $haveData2 = True;
                              $index2 = $i;
                            @endphp
                          @endif
                        @endif
                      @endfor
                      @if($haveData2)
                        {{$score[$index2]['score']}}/{{$max[$index2]['max']}}
                      @else
                        0/0
                      @endif
                    </p>
                </div>
            </div>
            @endif 
        </div>
          <h3>การกระจายตัวของคะแนนนักเรียนทั้งหมดในระบบ</h3>
        <div class="sub-section">
          <h3>สมการ</h3>
          <div class="legend">
            <div class="legend">
              <div class="box-legend"></div>
              <p>นักเรียน</p>
            </div>
            <div class="legend">
              <div class="box-legend2"></div>
              <p>นักเรียนในระบบ</p>
            </div>
          </div>
            <canvas id="barchart"></canvas>
        </div>
        <div class="sub-section">
          <h3>ห.ร.ม</h3>
          <div class="legend">
            <div class="legend">
              <div class="box-legend"></div>
              <p>นักเรียน</p>
            </div>
            <div class="legend">
              <div class="box-legend2"></div>
              <p>นักเรียนในระบบ</p>
            </div>
          </div>
            <canvas id="barchart2"></canvas>
        </div>
    </div>
</div>
</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
<script>
  function openNav() {
  document.getElementById("mySidenav").style.width = "calc(200px + 2vw)";
  }

  function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
  }

  var student_data = @json(session('student_score','default'));
  var above = @json(session('above','default'));
  var below = @json(session('below','default'));
  var overalllist1 = @json(session('listoverallscore_sub1','default'));
  var overalllist2 = @json(session('listoverallscore_sub2','default'));

  var countgroups = @json(session('countgroups','default'));


  var student_score = [];
  var score_above = [];
  var score_below = [];
  var chapter_name = [];
  var countscorelength1 = [0,0,0,0,0,0,0,0,0,0,0];
  var countscorelength2 = [0,0,0,0,0,0,0,0,0,0,0];
  var colorarray1 = ["#007d91","#007d91","#007d91","#007d91","#007d91","#007d91","#007d91","#007d91","#007d91","#007d91","#007d91"];
  var colorarray2 = ["#007d91","#007d91","#007d91","#007d91","#007d91","#007d91","#007d91","#007d91","#007d91","#007d91","#007d91"];
  var lengthscore = [[0,5],[6,10],[11,15],[16,20],[21,25],[26,30],[31,35],[36,40],[41,45],[46,50],[51,55]]
  var lable = ["0-5","6-10","11-15","16-20","21-25","26-30","31-35","36-40","41-45","46-50","51-55"]
  for (var i = 0; i < student_data.length; i++) {
    student_score.push(Math.round(student_data[i].score/countgroups[i].count));
    score_above.push(Number(above[i].above));
    score_below.push(Number(below[i].below));
    chapter_name.push(student_data[i].name);
    score_above[i] /= score_below[i];
  }
  console.log(student_score);
  for (var i = 0; i < lengthscore.length; i++) {
    for (var j = 0; j < overalllist1.length; j++) {
      if (Math.round(overalllist1[j].score) >= lengthscore[i][0] && Math.round(overalllist1[j].score) <= lengthscore[i][1]) {
        countscorelength1[i]++;
      }
    }for (var j = 0; j < overalllist2.length; j++) {
      if (Math.round(overalllist2[j].score) >= lengthscore[i][0] && Math.round(overalllist2[j].score) <= lengthscore[i][1]) {
        countscorelength2[i]++;
      }
    }
  }for (var i = 0; i < lengthscore.length; i++) {
    if (student_score[0] >= lengthscore[i][0] && student_score[0] <= lengthscore[i][1]) {
      colorarray1[i] = "#5cbcd2";
    }if (student_score[1] >= lengthscore[i][0] && student_score[1] <= lengthscore[i][1]) {
      colorarray2[i] = "#5cbcd2";
    }
  }
  console.log(overalllist2);
  
  Chart.defaults.global.defaultFontSize = 20;
  Chart.defaults.global.defaultFontFamily = "'Kanit', sans-serif";

  var bar = document.getElementById('barchart').getContext('2d');
  var bar2 = document.getElementById('barchart2').getContext('2d');
  if(window.innerWidth<500){
    bar.canvas.height = 300;
    bar2.canvas.height = 300;
  }
  console.log(countscorelength1);
  console.log(countscorelength2);
  console.log(colorarray1);
  console.log(colorarray2);
  var chapters = @json(session('chapter_list','default'));
  var chap1 = [];
  var chap2 = [];
  chap1.push(chapters[0]['name']);
  chap2.push(chapters[1]['name']);
  console.log(chapters);
  var Chart1 = new Chart(bar, {
    type: 'bar',
    data: {
      datasets: [{
            // label: chap1,
            data: countscorelength1,
            backgroundColor: colorarray1,
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
                var label = data.datasets[tooltipItem.datasetIndex].label || '';
                if (label) {
                    label += ': ';
                }
                label += tooltipItem.yLabel;
                return "จำนวน"+ " "+ label + " " + "คน";
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
            // label: chap2,
            data: countscorelength2,
            backgroundColor: colorarray2,
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












//   var barChart = new Chart (bar,{
//     type:'bar',
//     data:{
//       labels:chapter_name,
//       datasets:[
//         {
//           label: "นักเรียน",
//           backgroundColor: "#5cbcd2",
//           borderWidth: 0,
//           data:student_score
//         },
//         {
//           label: "นักเรียนทั้งหมดในระบบ",
//           backgroundColor: "#007d91",
//           borderWidth: 0,
//           data:score_above
//         }
//       ]
//     },
//     options:{
//       title:{
//         display: true,
//         text: 'คะแนนที่ได้ในแต่ละบท'
//       },
//       scales: {
//         yAxes: [{
//         ticks: {
//         beginAtZero:true
//           }
//         }]
//       },
//       tooltips: {
//            callbacks: {
//                label: function(tooltipItem, data) {
//                    var label = data.datasets[tooltipItem.datasetIndex].label || '';
//                    if (label) {
//                        label += ': ';
//                    }
//                    label += Math.round(tooltipItem.yLabel * 100) / 100;
//                    return label + " " + "คะแนน";
//                }
//            }
//          }
//     }
//   }
// );
</script>
