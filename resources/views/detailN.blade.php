<?php 
    $chapters = json_decode(session('chapters','default'),TRUE);
    $childdata = json_decode(session('chooeschilddata','default'),TRUE);
    $mean = json_decode(session('meanscore','default'),TRUE);
    $homeworklist = json_decode(session('homeworklist','default'),TRUE);
    // $name = json_decode(session('username','default'), true);
    // dd($name);
?>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>พี่หมีติวเตอร์ ADMIN</title>
        <link rel="stylesheet" href="css/detailN.css" />
        <!-- <link rel="stylesheet" href="css/footer.css" /> -->
        <link href="https://fonts.googleapis.com/css?family=Athiti|Kanit|Mitr|Roboto" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Prompt" rel="stylesheet">
        <link rel="shortcut icon" href="picture/bear_N.png">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script>
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
        </script>
    </head>
    <body>
        <div id="main-container" class="main-container">
            <div class="topnav" id="myTopnav">
                <div class="logo-nav">
                    <a href= "/">
                        <div>
                            <img class="logo" src="picture/bear_N.png">
                        </div>
                        <p class="namepi">พี่หมีติวเตอร์</p>
                    </a>
                </div>
                
                <div class="menu-nav">
                    <a class="menu-a" id="defaultOpen" onclick="opentab(event, 'storage')">
                        <div>
                            <img class="logo" src="picture/study.png">
                        </div>
                        <p>การทำแบบฝึกหัด</p>   
                    </a>
                    <a class="menu-a"  onclick="opentab(event, 'manage')">
                        <div>
                            <img class="logo" src="picture/homework.png">
                        </div>
                        <p>การทำการบ้าน</p>     
                    </a>
                </div>
                <div class="icon">
                    <a class="username" href ="/dashboard">{{session('username','default')}}</a>
                    <a href ="/logout">
                        <img class="logo" src="picture/exit-to-app-button.png">
                    </a>
                    <a id="hamberger" class="icon2" onclick="topnav()">
                        <img id="imgham" class="logo" src="picture/hamberger.png">
                    </a>
                </div>
            </div>
            <div id="storage" class="tabcontent">
                <div class="exercise">
                    <div class="profile-ex">
                        <div>
                            <img class="profileImg-exercise" src="{{$childdata[0]['local_pic']}}">
                        </div>
                        <label>{{$childdata[0]['name']}}</label>
                    </div>
                    <div class="content-ex">
                        <h2>คะแนนเฉลียในแต่ละบท</h2>
                        <div class="overflow-homework">
                          @foreach($mean as $obj)
                            <div class="chapter-con" onclick = "window.location.href = '/detailchapter/{{$obj['id']}}';">
                                <label>{{$obj['name']}} :</label>
                                <div class="barCon">
                                    <div class="pro">
                                        <div id="bar{{$loop->iteration}}" class="bar"></div>
                                    </div>
                                    <div>
                                        {{number_format($obj['mean'], 0, '.', '')}}/55  
                                    </div>
                                </div>
                            </div>
                            <script>
                                var valueMath = {{$obj['mean']}};
                                var elemMath = document.getElementById("bar{{$loop->iteration}}");
                                var maxValue = 55;
                                move(valueMath,elemMath,maxValue);
                            </script>
                          @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div id="manage"  class="tabcontent">
                <div class="head-homework">
                    <div class="menu-homework">
                        <div>
                            <div class="profile-con">
                                <div>
                                    <img class="profileImg-homework" src="{{$childdata[0]['local_pic']}}">
                                </div>
                                <label>{{$childdata[0]['name']}}</label>
                            </div>
                        </div>
                        <div>
                            <select id = "chapter" class="style-select">
                                <option value="0">เลือกชุดแบบฝึก</option>
                              @foreach($homeworklist as $obj)
                                <option value="{{$obj['examgroup_id']}}">{{$obj['name']}}</option>
                              @endforeach
                            </select>
                        </div>
                        <script>
                            $('#chapter').change(function(){
                                var chapter = $(this).val();
                                var _token =$('input[name="_token"]').val(); 
                                $.ajax({
                                    url:"{{route('queryhomeworkresult')}}",
                                    method:"POST",
                                    data:{chapter:chapter,_token:_token},
                                    success:function(result){
                                        $('#exam-space').html(result);
                                        
                                    },
                                })
                            });
                        </script>
                    </div>
                    <div class="score-homework">
                        <div class="layout-score-subject">
                            <div>
                                <img class="cup" src="picture/trophy.png">
                            </div>
                            <div>
                                <p class="main-score-subject">40/55</p>
                                <label>คะแนนรวม <b id = "showname"></b></label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <table role="table">
                    <thead role="rowgroup">
                            <col width="40">
                            <col width="250">
                            <col width="100">
                            <col width="40">
                        <tr role="row">
                            <th role="columnheader">ข้อ</th>
                            <th role="columnheader">โจทย์</th>
                            <th role="columnheader">บท</th>
                            <th role="columnheader">การตอบ</th>
                        </tr>
                    </thead>
                    <tbody role="rowgroup" id = "exam-space">
                    </tbody>
                </table>
            </div>
        </div>
        
        {{csrf_field()}}
        <script type="text/javascript" src="js/detailN.js"></script>
    </body>
</html>