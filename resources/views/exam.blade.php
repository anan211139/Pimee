<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>พี่หมีติวเตอร์ ADMIN</title>
        <link rel="stylesheet" href="css/exam.css" />
        <link rel="stylesheet" href="css/footer.css" />
        <link href="https://fonts.googleapis.com/css?family=Athiti|Kanit|Mitr|Roboto" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Prompt" rel="stylesheet">
        <link rel="shortcut icon" href="picture/bear_N.png">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    </head>
    <body>
        <div class="main-container">
            <div class="topnav" id="myTopnav">
                <div class="logo-nav">
                    <a href ="/">
                        <div>
                            <img class="logo" src="picture/bear_N.png">
                        </div>
                        <p class="namepi">พี่หมีติวเตอร์</p>
                    </a>
                </div>
                <div class="menu-nav">
                    <a class="menu-a" id="defaultOpen" onclick="opentab(event, 'storage')">
                        <div>
                            <img class="logo" src="picture/44308397_110582356533684_876217605201854464_n.png">
                        </div>
                        <p>คลังแบบฝึกหัด</p>   
                    </a>
                    <a class="menu-a" onclick="opentab(event, 'manage')">
                        <div>
                            <img class="logo" src="picture/44373119_2244581095823176_3268087396310188032_n.png">
                        </div>
                        <p>จัดการแบบฝึกหัด</p>     
                    </a>
                </div>
                <div class="icon">
                    <a class="username" href ="/">{{session('name','default')}}</a>
                    <a href ="/logout">
                        <img class="logo" src="picture/exit-to-app-button.png">
                    </a>
                    <a id="hamberger" class="icon2" onclick="topnav()">
                        <img id="imgham" class="logo" src="picture/hamberger.png">
                    </a>
                </div>
            </div>
            <div id="storage" class="tabcontent">
                <div class="filter-con">
                    <select id ="onwer" class="style-select">
                        <option value="0">แบบฝึกหัดทั้งหมด</option>
                        <option value="1">แบบฝึกหัดของท่าน</option>
                        <option value="2">แบบฝึกหัดในระบบ</option>
                    </select>
                    <!-- <div id = "sub" class="custom-select"> -->
                    @php
                    $subject = json_decode($jsonsubject, true);
                    $chapter = json_decode($jsonchapter, true);
                    $exam = json_decode($queryresult, true);
                    $correct =json_decode($jsoncorrect, true);
                    $wrong = json_decode($jsonwrong,true);
                    $group = json_decode($jsongroup,true);
                    $listgroup = json_decode($listgroup,true);
                    $readgroup = json_decode($readgroup,true);
                    @endphp
                        <select name = "subid" id ="subid" class="style-select">
                          <option value="0">ทุกวิชา</option>
                          @foreach($subject as $num)
                            <option value="{{$num['id']}}"> {{$num['name']}}</option>
                          @endforeach
                        </select>
                    <!-- </div> -->
                    <select id="chapter" class="style-select">
                        <option value="0">ทุกบท</option>
                        @foreach($chapter as $num)
                            <option value="{{$num['id']}}"> {{$num['name']}}</option>
                        @endforeach
                    </select>       
                    <select id = "level" class="style-select">
                        <option value="0">ทุกระดับ</option>
                        <option value="3">ยาก</option>
                        <option value="2">กลาง</option>
                        <option value="1">ง่าย</option>
                    </select>
                </div>
                <table role="table">
                    <thead role="rowgroup">
                            <col width="40">
                            <col width="250">
                            <col width="40">
                            <col width="40">
                        <tr role="row">
                            <th role="columnheader">ลำดับ</th>
                            <th role="columnheader">แบบฝึกหัด</th>
                            <th role="columnheader">ตอบถูก (คน)</th>
                            <th role="columnheader">ตอบผิด (คน)</th>
                        </tr>
                    </thead>
                    <tbody id = "examlist" role="rowgroup">
                      @foreach($exam as $obj)
                        @php 
                            $temp = -1;
                            $temp2 = -1;
                        @endphp
                        @foreach($correct as $num)
                            @if($obj['id'] == $num['exam_id'])
                                @php 
                                    $temp = $loop->iteration - 1;
                                @endphp
                            @endif
                        @endforeach
                        @foreach($wrong as $num)
                            @if($obj['id'] == $num['exam_id'])
                                @php 
                                    $temp2 = $loop->iteration - 1;
                                @endphp
                            @endif
                        @endforeach
                        <tr role="row" onclick="document.getElementById('exam{{$obj['id']}}').style.display='block'">
                            <td role="cell">{{$loop->iteration}}</td>
                            <td role="cell">{{substr($obj['question'], 0, 50)}}</td>
                            <td role="cell">
                              @if($temp == -1)
                                0
                              @else
                                {{$correct[$temp]['num']}}
                              @endif
                            </td>
                            <td role="cell">
                              @if($temp2 == -1)
                                0
                              @else
                                {{$wrong[$temp2]['num']}}
                              @endif
                            </td>
                        </tr>
                      @endforeach
                    </tbody>
                </table>
                <div class="pagecontrol-con">
                    <div class="cursor">
                        <img class="logo rotate180" src="picture/next.png">
                    </div>
                    <div>
                        1 / 10
                    </div>
                    <div class="cursor">
                        <img class="logo" src="picture/next.png">
                    </div>
                </div>
                @include('inc.footer')
            </div>
            <div id="manage" class="tabcontent">
                <div id="sideMenu" class="sideMenu">
                  @foreach($readgroup as $obj)
                    <button class="menuBtn examgroup{{$obj['id']}}" value = "{{$obj['id']}}">{{$obj['name']}}</button>
                  @endforeach
                    <button class="menuBtn add" onclick = "window.location.href = '/newgroupexam';">
                        <img class="logo" src="picture/plus.png">
                        <p>สร้างชุดแบบฝึกหัด</p>
                    </button>
                </div>
                <div id="sideMenu-res" class="sideMenu">
                    @foreach($readgroup as $obj)
                        <button class="menuBtn examgroup{{$obj['id']}}" value = "{{$obj['id']}}">{{$obj['name']}}</button>
                    @endforeach
                    <button class="menuBtn add" onclick = "window.location.href = '/newgroupexam';">
                        <img class="logo" src="picture/plus.png">
                        <p>สร้างชุดแบบฝึกหัด</p>
                    </button>
                </div>
                <div class="manage-con">
                    <h2>แบบฝึกหัดใน "การบ้านชุดที่ 1"</h2>
                    <div class="manage-grid">

                    </div>
                    <div class="send-con">
                        <button class="send-btn"  onclick="document.getElementById('send').style.display = 'block';">ส่งไปยังห้องเรียน</button>
                    </div>
                    <a id="icon-menu" class="icon-menu">
                        <img id="icon-res" src="picture/grid.png">
                    </a>
                </div>
            </div>
          @foreach($exam as $obj)
            <div id="exam{{$obj['id']}}" class="modal">
                <div class="modal-content animate" action="/action_page.php">
                    <div class="pup-container">
                        <div class="pup-content">
                            <div class="overflow2">
                                <div class="pup-padding">
                                    <p><b>คำถาม</b></p>
                                    <p>
                                        {{$obj['question']}}
                                    </p>
                                    <p><b>ตัวเลือก</b></p>
                                    <ol>
                                        <li>{{$obj['choice_a']}}</li>
                                        <li>{{$obj['choice_b']}}</li>
                                        <li>{{$obj['choice_c']}}</li>
                                        <li>{{$obj['choice_d']}}</li>
                                    </ol>
                                    <p><b>เฉลย ข้อ : </b><span>{{$obj['answer']}}</span></p> 
                                </div>
                                <div class="btn-report">
                                    <button id="btn-report{{$obj['id']}}" class="report-style add">
                                        <img class="logo" src="picture/information.png">
                                        <p>รางานปัญหา</p>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="checkbox-con">
                          {!! Form::open(['url' => 'addtogroup']) !!}
                            <p class="p-padding">เพิ่มในชุดแบบฝึกหัด</p>
                            <div class="overflow">
                              @foreach($group as $item)
                                <div class="checkbox-layout">
                                    <input type="checkbox" name = "group_id[]" id="check-{{$item['id']}}" value="{{$item['id']}}">
                                    <label for="check-{{$item['id']}}">{{$item['name']}}</label>
                                </div>
                              @endforeach
                            </div>
                            <input style="display:none;" name = "exam_id" type="text" value = "{{$obj['id']}}">
                            <button class="send" style = 'display:none'>submit</button>
                            {!! Form::close() !!}
                            
                            <div class="btn-checkbox">
                                <button class="checkbox-layout add btmsendgroup">
                                    <img class="logo" src="picture/plus.png">                                
                                    <p>เพิ่มในชุดแบบฝึกหัด</p>
                                </button>
                                
                                <button class="checkbox-layout add">
                                    <!-- <img class="logo" src="picture/plus.png"> -->
                                    <p>สร้างชุดแบบฝึกหัด</p>
                                </button>
                            </div>
                          
                        </div>
                        <a class="close" onclick="document.getElementById('exam{{$obj['id']}}').style.display='none'">
                            <img class="logo" src="picture/close.png">                        
                        </a>
                    </div>
                </div>
            </div>
            <div id="report{{$obj['id']}}" class="modal2">
                <div class="modal-content" action="/action_page.php">
                    <div class="pup-container">
                        <div class="pup-content">
                            <div class="overflow2">
                                <div class="pup-padding">
                                    <p><b>คำถาม</b></p>
                                    <p>
                                        {{$obj['question']}}
                                    </p>
                                    <p><b>ตัวเลือก</b></p>
                                    <ol>
                                        <li>{{$obj['choice_a']}}</li>
                                        <li>{{$obj['choice_b']}}</li>
                                        <li>{{$obj['choice_c']}}</li>
                                        <li>{{$obj['choice_d']}}</li>
                                    </ol>
                                    <p><b>เฉลย ข้อ : </b><span>{{$obj['answer']}}</span></p> 
                                </div>
                            </div>
                        </div>
                        <div class="checkbox-con">
                            <p class="p-padding">รางานปัญหาแบบฝึกหัด</p>
                            <div id = "inputreport" style="text-align:center;">
                                <textarea class ="report" wrap="physical"></textarea>
                            </div>
                            <input id = "examid" type="text" value = "{{$obj['id']}}" style = "display:none;">
                            <button class="checkbox-layout add btmfeedback">
                                <img class="logo" src="picture/sent-mail.png">
                                <p>ส่งข้อความ</p>
                            </button>
                        </div>
                        <a id="report-close{{$obj['id']}}" class="close">
                            <img class="logo" src="picture/close.png">
                        </a>
                    </div>
                </div>
            </div>
          @endforeach
        </div>

        <div id="send" class="modal2">
              
                <div class="modal-content-send animate" action="/action_page.php">
                  {!! Form::open(['url' => 'sendexamtoroom']) !!}
                    <div class="grid-send">
                        <div>
                            <h2>เลือกห้องเรียน</h2>
                            <div class="overflow-send">
                            @foreach($listgroup as $obj)
                                <div class="checkbox-layout">
                                    <input type="checkbox" name = "room_id[]" id="class-1" value="{{$obj['id']}}">
                                    <label for="class-1">{{$obj['name']}}</label>
                                </div>
                            @endforeach
                            </div>
                            <input style ="display:none;" id ="formgroupexam" type="text" name = "groupexam" required>
                            <!-- <div class="btn-send">
                                <button class="checkbox-layout add btn-center">
                                    <img class="logo" src="picture/sent-mail.png">
                                    <p>ส่งแบบฝึกหัด</p>
                                </button>
                            </div> -->
                            <a id="report-close" class="close" onclick="document.getElementById('send').style.display='none';">
                                <img class="logo" src="picture/close.png">
                            </a>
                        </div>
                        <div class="date-con">
                            <div class="time">
                                <p>วันหมดอายุ*</p>
                                <input name = "exp" class="input-date" type="datetime-local" required>
                            </div>
                            <div class="time">
                                <p>วันปล่อยเฉลย*</p>
                                <input name = "noti" class="input-date" type="datetime-local" required>
                            </div>
                            <div class="time">
                                <p>วันแจงเตือน*</p>
                                <input name ="key" class="input-date" type="datetime-local" required>
                            </div>
                        </div>
                    </div>
                    <div class="btn-send2">
                        <button class="checkbox-layout add btn-center">
                            <img class="logo" src="picture/sent-mail.png">
                            <p>ส่งแบบฝึกหัด</p>
                        </button>
                    </div>
                  {!! Form::close() !!}
                </div>
              
            </div>
        
            
          <script>
            var send = document.getElementById("send");
            
            function topnav() {;
                var x = document.getElementById("myTopnav");
                if (x.className === "topnav") {
                    x.className += " responsive";
                } else {
                    x.className = "topnav";
                }
            }
            
            function opentab(evt, idTab) {
                var i, tabcontent, tablinks;
                tabcontent = document.getElementsByClassName("tabcontent");
                for (i = 0; i < tabcontent.length; i++) {
                    tabcontent[i].style.display = "none";
                }
                tablinks = document.getElementsByClassName("menu-a");
                for (i = 0; i < tablinks.length; i++) {
                    tablinks[i].className = tablinks[i].className.replace(" active", "");
                }
                document.getElementById(idTab).style.display = "block";
                evt.currentTarget.className += " active";
            }
                        
            document.getElementById("defaultOpen").click();

            var iconMenu = document.getElementById("icon-menu");
            var countIcon = 1;
            iconMenu.addEventListener("click", openNav);
            function openNav() {
                if(countIcon%2 != 0){
                    document.getElementById("sideMenu-res").style.width = "calc(230px + 2vw)";
                    document.getElementById("icon-res").src="picture/close.png";
                    countIcon++;
                }else{
                    document.getElementById("sideMenu-res").style.width = "0";
                    document.getElementById("icon-res").src="picture/grid.png";
                    countIcon++;
                }
            }   


            @foreach($exam as $obj)
                var modal{{$obj['id']}} = document.getElementById('exam{{$obj['id']}}');
                var modaltwo{{$obj['id']}} = document.getElementById('report{{$obj['id']}}');
                var reportClose{{$obj['id']}} = document.getElementById('report-close{{$obj['id']}}');
                var report{{$obj['id']}} = document.getElementById('btn-report{{$obj['id']}}');

                report{{$obj['id']}}.addEventListener("click",openReport{{$obj['id']}});
                function openReport{{$obj['id']}}(){
                    modaltwo{{$obj['id']}}.style.display='block';
                    modal{{$obj['id']}}.style.display='none';  
                }
                reportClose{{$obj['id']}}.addEventListener("click",reportClosePupup{{$obj['id']}});
                function reportClosePupup{{$obj['id']}}(){
                    modaltwo{{$obj['id']}}.style.display = "none";
                }
            @endforeach

            window.onclick = function(event) {
              @foreach($exam as $obj)
                if (event.target == modal{{$obj['id']}}) {
                    modal{{$obj['id']}}.style.display = "none";
                }
                if (event.target == modaltwo{{$obj['id']}}) {
                    modaltwo{{$obj['id']}}.style.display = "none";
                    }
              @endforeach
                if (event.target == send) {
                        send.style.display = "none";
                        console.log("gg");
                    }
                } 
                var hambergerBtn = document.getElementById('hamberger');
                var imgham = document.getElementById('imgham');
                var hamcount = 0;

                hambergerBtn.addEventListener("click",closeBtn);
                function closeBtn(){
                    if(hamcount%2 != 0){
                        imgham.src = "picture/hamberger.png";
                        hamcount++;
                    }
                    else{
                        imgham.src = "picture/close.png";
                        hamcount++;
                    }
                }  
            </script>
            {{csrf_field()}}
            @include('inc.ajaxexam')
    </body>
</html>