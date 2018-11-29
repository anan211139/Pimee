<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>พี่หมีติวเตอร์</title>
        <link rel="stylesheet" href="css/class.css" />
        <link href="https://fonts.googleapis.com/css?family=Athiti|Kanit|Mitr|Roboto" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Prompt" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <link rel="shortcut icon" href="picture/bear_N.png">
        <script src="https://d.line-scdn.net/liff/1.0/sdk.js"></script>
    </head>
    <body>
        <div class="topnav" id="myTopnav">
            <div class="logo-nav">
                <a href = "/">
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
        <label class="add-header">กรอกโค้ด</label>

            <div>
                <input id ="code" placeholder="Code" class="input-name " name="code">
                <input id = "useridprofilefield" type="text" class="input-name " name="line_code" style = "display:none;">
            </div>
            <div class ="btm-space">
                <button id = "btn1" class="add-btn" style="display:none;">Add</button>
                <button id = "btn2" class="add-btn2">Add</button>
            </div>
            
            <div id="return-space"></div>
            {{csrf_field()}}

        <script>
            
            $('#code').on('change keyup paste', function(){
                var code = $(this).val();
                var _token =$('input[name="_token"]').val(); 
                if(code != ""){
                    $.ajax({
                        url:"{{route('checkrommcode')}}",
                        method:"POST",
                        data:{code:code,_token:_token},
                        success:function(result){
                            console.log(result);
                            if(result == 0){
                                document.getElementById("btn1").style.display = "none";
                                document.getElementById("btn2").style.display = "inline-block";
                            }else if(result == 1){
                                document.getElementById("btn1").style.display = "inline-block";
                                document.getElementById("btn2").style.display = "none";
                            }
                        }
                    })
                }
            });
                

            $('.add-btn').on('click', function(){
                var line_code = document.getElementById("useridprofilefield").value;
                var roomcode = document.getElementById("code").value;
                console.log(line_code);
                if(roomcode != ""){
                    var _token =$('input[name="_token"]').val(); 
                    $.ajax({
                        url:"{{route('sent_connectroom')}}",
                        method:"POST",
                        data:{roomcode:roomcode,line_code:line_code,_token:_token},
                        success:function(result){
                            if(result == 202){
                                console.log(result);
                                document.getElementById('return-space').innerHTML = "Code not found";
                                liff.sendMessages(
                                [
                                    {
                                        type: 'text',
                                        text: "[Code not found]"
                                    }, {
                                        type: 'sticker',
                                        packageId: '2',
                                        stickerId: '144'
                                    }
                                ])
                                .then(function () {
                                    liff.closeWindow();
                                    
                                });
                            }else{
                                liff.sendMessages(
                                [
                                    {
                                        type: 'text',
                                        text: "[เชื่อมต่อเรียบร้อยแล้ว]"
                                    }, {
                                        type: 'sticker',
                                        packageId: '2',
                                        stickerId: '144'
                                    }
                                ])
                                .then(function () {
                                    liff.closeWindow();
                                    
                                });
                            }
                        },
                    })
                }else{
                    alert('กรุณากรอกข้อมูล');
                }
            });
            
            window.onload = function (e) {
                liff.init(function (data) {
                    getProfile();
                });
            };
            function getProfile(){
                liff.getProfile().then(function (profile) {
                    document.getElementById('useridprofilefield').value = profile.userId;
                })
            }   

        </script>
    </body>
</html>
