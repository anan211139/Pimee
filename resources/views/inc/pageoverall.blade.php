<?php
  $sumoverall = json_decode(session('sumoverall','default'), true);
  $sumsub1 = json_decode(session('sumsub1','default'), true);
  $sumsub2 = json_decode(session('sumsub2','default'), true);
 ?>
<div class="center">
    <h1>ทุกวิชา</h1>
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
            <div class="layout-score2">
                <div class="div-score div-score-color1">
                    <label>Math (ข้อ)</label>
                    <p class="score">@if($sumsub1[0]['max']){{$sumsub1[0]['true']}}/{{$sumsub1[0]['max']}}@else 0/0 @endif</p>
                </div>
                <div class="div-score div-score-color2">
                    <label>English (ข้อ)</label>
                    <p class="score">@if($sumsub2[0]['max']){{$sumsub2[0]['true']}}/{{$sumsub2[0]['max']}}@else 0/0 @endif</p>
                </div>
            </div>
        </div>
          <h3>การกระจายตัวของคะแนนนักเรียนทั้งหมดในระบบ</h3>
        <div class="sub-section">
          <h3>วิชาคณิตศาสตร์</h3>
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
          <h3>วิชาภาษาอังกฤษ</h3>
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
  Chart.defaults.global.defaultFontSize = 15;
  Chart.defaults.global.defaultFontFamily = "'Kanit', sans-serif";
  var sub = @json(session('subject_list','default'));
  var sub1 = [];
  var sub2 = [];
  sub1.push(sub[0]['name']);
  sub2.push(sub[1]['name']);


  var student = @json(session('student_score_allsubject','default'));
  var score_count = @json(session('student_score_count','default'));
  var overall = @json(session('overall_score','default'));
  var st_count = @json(session('student_count','default'));
  var overalllist1 = @json(session('listoverallscore_sub1','default'));
  var overalllist2 = @json(session('listoverallscore_sub2','default'));


  var subject_name = [];
  var student_score_allsubject = [];
  var student_score_count = [];
  var overall_score = [];
  var student_count = [];
  var chapter_name = [];
  var listscore = [];
  var countscorelength1 = [0,0,0,0,0,0,0,0,0,0,0];
  var countscorelength2 = [0,0,0,0,0,0,0,0,0,0,0];
  var colorarray1 = ["#007d91","#007d91","#007d91","#007d91","#007d91","#007d91","#007d91","#007d91","#007d91","#007d91","#007d91"];
  var colorarray2 = ["#007d91","#007d91","#007d91","#007d91","#007d91","#007d91","#007d91","#007d91","#007d91","#007d91","#007d91"];
  var lengthscore = [[0,5],[6,10],[11,15],[16,20],[21,25],[26,30],[31,35],[36,40],[41,45],[46,50],[51,55]]
  var lable = ["0-5","6-10","11-15","16-20","21-25","26-30","31-35","36-40","41-45","46-50","51-55"]

  for (var i = 0; i < student.length; i++) {
    subject_name.push(student[i].name);
    student_score_allsubject.push(Number(student[i].score));
    student_score_count.push(score_count[i].count);
    overall_score.push(Number(overall[i].overall));
    student_count.push(st_count[i].student_count);
    chapter_name.push(student[i].name);
    student_score_allsubject[i] = Math.round(student_score_allsubject[i] / student_score_count[i]); //คะแนนบาร์ชาตนักเรียน
    overall_score[i] = ((overall_score[i]+student_score_allsubject[i]) / student_count[i]).toFixed(2); //คะแนนบาร์ชาตรวม
  }
  console.log(student_score_allsubject);
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
    if (student_score_allsubject[0] >= lengthscore[i][0] && student_score_allsubject[0] <= lengthscore[i][1]) {
      colorarray1[i] = "#5cbcd2";
    }if (student_score_allsubject[1] >= lengthscore[i][0] && student_score_allsubject[1] <= lengthscore[i][1]) {
      colorarray2[i] = "#5cbcd2";
    }
  }
  
  
  console.log(countscorelength1);

  var bar = document.getElementById('barchart').getContext('2d');
  var bar2 = document.getElementById('barchart2').getContext('2d');

  // console.log(wid);
  if(window.innerWidth<500){
    bar.canvas.height = 300;
    bar2.canvas.height = 300;
  }
  console.log(countscorelength1);
  console.log(countscorelength2);
  console.log(colorarray1);
  console.log(colorarray2);



var Chart1 = new Chart(bar, {
  type: 'bar',
  data: {
    datasets: [{
          // label: sub1,
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
</script>
