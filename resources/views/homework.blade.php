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
                        </table>
                    </div>

                </div>
               
             
            </div>
        </div> 
        <div id="test">ddd</div>
        <div style="display: none" id="useridfield"></div>
        <div style="display: none" id="useridfield1"></div>
        {{csrf_field()}}
    </body>
    <script>
            var globalProfile ;
            window.onload = function (e) {
                liff.init(function (data) {
                    getProfile();
                });
            };
            function getProfile(){
                liff.getProfile().then(function (profile) {
                    document.getElementById('useridfield').innerHTML = profile.userId;
                    globalProfile = profile;
                }).then(function(send_value){
                    var value = globalProfile.userId;
                    var _token = $('input[name="_token"]').val();
                    $.ajax({
                        url:"{{route('homework_value')}}",
                        method:"POST",
                        data:{value:value,_token:_token},
                        success:function(result){
                            console.log(result);
                            $('.first').append(result);
                        }
                    })
                    $.ajax({
                        url:"{{route('homework_value2')}}",
                        method:"POST",
                        data:{value:value,_token:_token},
                        success:function(result){
                            console.log(result);
                            $('.last').append(result);
                        }
                    })

                }).then(function(send_message){
                    document.getElementById('test').addEventListener('click', function () {
                        https://developers.line.me/en/reference/liff/#liffsendmessages()
                        liff.sendMessages([{
                            type: 'text',
                            text: "Send text message"
                        }, {
                            type: 'sticker',
                            packageId: '2',
                            stickerId: '144'
                        }]).then(function () {
                            window.alert("Sent");
                        }).catch(function (error) {
                            window.alert("Error sending message: " + error);
                        });
                        liff.closeWindow()
                    });

                })
            }


            
        </script>
    
</html>