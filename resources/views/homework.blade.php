<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>พี่หมีติวเตอร์</title>
        <link rel="stylesheet" href="../css/studentinfo.css" />
        <link rel="stylesheet" href="../css/homework.css" />
        <link href="https://fonts.googleapis.com/css?family=Athiti|Kanit|Mitr|Roboto" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Prompt" rel="stylesheet">
        <link rel="shortcut icon" href="picture/bear_N.png">
        <script src="https://d.line-scdn.net/liff/1.0/sdk.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script>
            window.onload = function (e) {

                liff.init(function (data) {
                    initializeApp(data);
                });
            };
            function initializeApp(data) {
                document.getElementById('useridfield').textContent = data.context.userId; 
            }


            $(document).ready(function()
            {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var _token = $('input[name="_token"]').val(); 
                $.ajax({
                    type: "POST",
                    url: "{{route('homework')}}",
                    contentType: "application/json; charset=utf-8",
                    data: {
                        // key : data.context.userId 
                        id : data.context.userId,
                        _token:_token
                    },
                    success: function (result) {
                        return result;
                    }
                });
            };
        </script>
    </head>

    <body>
        <h1>รายการการบ้าน</h1>
        <div class="box">
            <input type="text" name="line_code" id="useridprofilefield" style ="display:none;"></p>
            <div class="tabs">
                <div class="tab-2">
                    <label for="tab2-1" class="ready">ยังทำไม่เสร็จ</label>
                    <input id="tab2-1" name="tabs-two" type="radio" checked="checked">
                    <div>
                        <table class="first">
                            <tr class="head">
                                <!-- <th>วิชา</th> -->
                                <th><div class="head_table">เรื่อง</div></th> 
                                <th>คุณครู</th>
                                <th>กำหนดส่ง</th>
                                <th></th>
                                
                            </tr>
                            @foreach($homework as $homework_notyet)
                                <?php
                                    $homework_notyet->exp_date =  date("d/m/Y", strtotime($homework_notyet->exp_date));
                                    if($homework_notyet->finish_status === null){
                                ?> 
                                    <tr>
                                        <!-- <td>คณิตศาสตร์</td> -->
                                        <td>{{$homework_notyet->title_hw}}</td> 
                                        <td class="date">{{$homework_notyet->parent_name}}</td>
                                        <td class="date">{{$homework_notyet->exp_date}}</td>
                                        <td>
                                            <div class="do_hw">
                                                <a href="aa">ทำ</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php

                                    }
                                ?>
                                
                            @endforeach

                        </table>

                    </div>
                </div>

                <div class="tab-2">
                    <label for="tab2-2" class="done">ทำเสร็จแล้ว</label>
                    <input id="tab2-2" name="tabs-two" type="radio">
                    <div>
                        <table class="last">
                            <tr class="head">
                                <!-- <th>วิชา</th> -->
                                <th><div class="head_table">เรื่อง</div></th> 
                                <th>คุณครู</th>
                                <th>คะแนน</th>
                            </tr>
                            @foreach($homework as $homework_finish)
                               <?php
                                    if($homework_finish->finish_status == 1){
                                ?>
                                    <tr>
                                        <!-- <td>คณิตศาสตร์</td> -->
                                        <td>{{$homework_finish->title_hw}}</td> 
                                        <td>{{$homework_finish->parent_name}}</td>
                                        <td>{{$homework_finish->total}}</td>
                                    </tr>
                                <?php

                                    }
                                ?>
                                
                            @endforeach
                        </table>
                    </div>

                </div>
             
            </div>
        </div> 
        <div id="useridfield"></div>
    </body>
</html>