<?php 
    $listclass = session('listclass', 'default');
?>
<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>พี่หมีติวเตอร์</title>
        <link rel="stylesheet" href="css/class.css" />
        <link href="https://fonts.googleapis.com/css?family=Athiti|Kanit|Mitr|Roboto" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Prompt" rel="stylesheet">
        <link rel="shortcut icon" href="picture/bear_N.png">
    </head>
    <body>
        <div class="topnav" id="myTopnav">
            <div class="logo-nav">
                <a href = '/'>
                    <div>
                        <img class="logo" src="picture/bear_N.png">
                    </div>
                    <p>พี่หมีติวเตอร์</p>
                </a>
            </div>
            <div class="icon">
                <a class="username">{{session('name','default')}}</a>
                <a href = "/logout">
                    <img class="logo" src="picture/exit-to-app-button.png">
                </a>
            </div>
        </div>
        <label class="header">กรุณาเลือกห้องเรียน</label>
        <div class="layoutSelectPage">
         @for($i=0;$i<count($listclass);$i++)
            <div class="class-name">
                <a href="/selectclass/select/{{$listclass[$i]['id']}}" class="class-a">
                    {{$listclass[$i]['name']}}
                </a>
                <button class="delete-btn" onclick = "window.location.href = '/removeclass/{{$listclass[$i]['id']}}';">
                        <img src="picture/garbage.png">
                </button>
            </div>
        @endfor
            <a href="/newclass" class="plus">
                <img src="picture/plus.png">
            </a>
        </div>
    </body>
</html>
