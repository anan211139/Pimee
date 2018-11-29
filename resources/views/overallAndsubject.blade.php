<?php
  $choosechild = session('choosechild','default');
  $subject_list = session('subject_list', 'default');
  $chapther_list = session('chapter_list', 'default');
  $chapterCh = session('chapterCh','default');
  $choosechilddata =session('choosechilddata','default');

  function setimage($url){
    if(!file($url)){
      echo " picture/bear_Nffff.png";
    }else{
      echo $url;
    }
  }

 ?>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>พี่หมีติวเตอร์</title>
        <link rel="stylesheet" href="css/detail.css" />
        <link href="https://fonts.googleapis.com/css?family=Athiti|Kanit|Mitr|Roboto" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Prompt" rel="stylesheet">
        <link rel="shortcut icon" href="picture/bear_N.png">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    </head>
    <body>
        <div class="main-container">
            <div id="sideMenu">
                <div class="homeBtn-flex">
                  <a class="homeBtn" onclick = "window.location.href = '/dashboard';">
                    <img class="imgHome" src="picture/home.png">
                  </a>
                </div>
                <img class="profileImg" src="
                @if($choosechilddata[0]['local_pic'])
                  {{$choosechilddata[0]['local_pic']}}
                @else
                  picture/bear_Nffff.png
                @endif"
                onerror='this.src="picture/bear_Nffff.png"'>
                <p>{{$choosechilddata[0]['name']}}</p>


                <div class="layoutMenu">
                    <div class="menuBtn">
                        <a href="/selectoverall/{{$choosechild}}">ดูคะแนนรวม</a>
                    </div>
                  @for($i=0;$i<count($subject_list);$i++)
                  <div class="menuBtn">
                        <ul class="main-navigation" style = ' list-style: none;
                                                              padding: 0;
                                                              margin: 0;
                                                              left: 100%;
                                                              top: 0 ;
                                                              width: 100%;'>
                            <li class="list" style = 'position: relative;
                                                      border-radius: calc(1px + 0.2vw); 
                                                      width: 100%;'>
                                <div class="con-subBtn" style = ' display: flex;
                                                                  flex-flow: row;
                                                                  justify-content: space-between;'>
                                    <a class="btn-subject" href="/selectsubject/{{$subject_list["$i"]["id"]}}">
                                      {{$subject_list["$i"]["name"]}}
                                    </a>
                                    <button class="btn-dropdown"><img class="dropdownicon" src="picture/down-arrow5bbcd2.png" style = 'width: 10px;height: 10px;'></button>
                                </div>
                                <ul class="main-navigation" style = 'display: none;'>
                                  @for($j=0;$j<count($chapther_list);$j++)
                                    @if($subject_list["$i"]["id"] == $chapther_list["$j"]["subject_id"])
                                      <li class="list">
                                        <a class="a" href="/selectchapter/{{$subject_list["$i"]["id"]}}/{{$chapther_list["$j"]["id"]}}">
                                          {{$chapther_list["$j"]["name"]}}
                                        </a>
                                      </li>
                                    @endif
                                  @endfor
                                </ul>
                            </li>
                        </ul>
                    </div>
                    @endfor
                </div>


            </div>
            <div id="mySidenav" class="sidenav">
                <a onclick="closeNav()" class="close-btn">Close</a>
                <div class="homeBtn-flex">
                  <a class="homeBtn" onclick = "window.location.href = '/dashboard';">
                    <img class="imgHome" src="picture/home.png">
                  </a>
                </div>
                <img class="profileImg" src="
                @if($choosechilddata[0]['local_pic'])
                  {{$choosechilddata[0]['local_pic']}}
                @else
                  picture/bear_Nffff.png
                @endif"
                onerror='this.src="picture/bear_Nffff.png"'>
                <p>{{$choosechilddata[0]['name']}}</p>
                <div class="layoutMenu">
                  <div class="menuBtn">
                    <a href="/selectoverall/{{$choosechild}}">ดูคะแนนรวม</a>
                  </div>
                  @for($i=0;$i<count($subject_list);$i++)
                    <div class="menuBtn">
                      <ul class="main-navigation" style = ' list-style: none;
                                                            padding: 0;
                                                            margin: 0;
                                                            left: 100%;
                                                            top: 0 ;
                                                            width: 100%;'>
                        <li class="list" style = 'position: relative;
                                                  border-radius: calc(1px + 0.2vw); 
                                                  width: 100%;'>
                          <div class="con-subBtn" style = ' display: flex;
                                                            flex-flow: row;
                                                            justify-content: space-between;'>
                            <a class="btn-subject" href="/selectsubject/{{$subject_list["$i"]["id"]}}">
                              {{$subject_list["$i"]["name"]}}
                            </a>
                            <button class="btn-dropdown"><img class="dropdownicon" src="picture/down-arrow5bbcd2.png" style = 'width: 10px;height: 10px;'></button>
                                </div>
                                <ul class="main-navigation" style = 'display: none;'>
                                  @for($j=0;$j<count($chapther_list);$j++)
                                    @if($subject_list["$i"]["id"] == $chapther_list["$j"]["subject_id"])
                                      <li class="list">
                                        <a class="a" href="/selectchapter/{{$subject_list["$i"]["id"]}}/{{$chapther_list["$j"]["id"]}}">
                                          {{$chapther_list["$j"]["name"]}}
                                        </a>
                                      </li>
                                    @endif
                                  @endfor
                                </ul>
                            </li>
                        </ul>
                    </div>
                    @endfor
                </div>
            </div>
            <div class="content">
                <div id="navbar">
                        <div class="navLeft">
                            <div id="hamberger" onclick="openNav()"><img class="logo" src="picture/hamberger.png"></div>
                        </div>
                    <div class="navRight">
                      <a class="name" href="/dashboard">{{session('name','default')}}</a>
                        {{-- <a href="/logout" class="navLogOut">ออกจากระบบG</a> --}}
                      <a href ="/logout">
                        <img class="logo" src="picture/exit-to-app-button.png">
                      </a>
                    </div>
                    
                </div>
                @if(session()->has('student_score_allsubject'))
                  @include('inc.pageoverall')
                @endif
                @if(session()->has('student_score'))
                  @include('inc.pagesubject')
                @endif
                @if(session()->has('student_score_chapter'))
                  @include('inc.pagechapter')
                @endif
                
                <script>
                  $(document).ready(function(){
                    $('.list button.btn-dropdown').on("click", function(e){
                        $(this).parent().next('ul').toggle();
                        e.stopPropagation();
                    });
                  });
                </script>
    </body>
</html>
