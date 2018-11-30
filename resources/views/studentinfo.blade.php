<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>พี่หมีติวเตอร์</title>
        <link rel="stylesheet" href="../css/studentinfo.css" />
        <link href="https://fonts.googleapis.com/css?family=Athiti|Kanit|Mitr|Roboto" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Prompt" rel="stylesheet">
        <link rel="shortcut icon" href="picture/bear_N.png">
        <script src="https://d.line-scdn.net/liff/1.0/sdk.js"></script>
    </head>
    <body>
        <h1>บันทึกประวัติ</h1>
        <div class="box">
                <input type="text" name="line_code" id="useridprofilefield" style ="display:none;"></p>
                <label>ชื่อ</label>
                <div class="con-input">
                    <input id ="name" type="text" name="name" required>
                </div>
                <!-- <label>นามสกุล</label>
                <div class="con-input">
                    <input type="text" name="surname" required>
                </div>
                <label>โรงเรียน</label>
                <div class="con-input">
                    <input type="text" name="school" required>
                </div> -->
                <!-- <label>ห้อง</label>
                <div class="con-input">
                    <input type="text" name="class" required>
                </div> -->
                <div class="layout-btn">
                    <button class="save-btn">บันทึก</button>
                </div>
        </div>
        <script>

            $('.save-btn').on('click', function(){
                var line_code = document.getElementById("useridprofilefield").value;
                var name = document.getElementById("name").value;
                if(name != ""){
                    var _token =$('input[name="_token"]').val(); 
                    $.ajax({
                        url:"{{route('sent_studentdata')}}",
                        method:"POST",
                        data:{name:name,line_code:line_code,_token:_token},
                        success:function(){
                            liff.sendMessages(
                            [
                                {
                                    type: 'text',
                                    text: "[ลงทะเบียนเรียบร้อยแล้ว]"
                                }
                                // , {
                                //     type: 'sticker',
                                //     packageId: '2',
                                //     stickerId: '144'
                                // }
                            ])
                            .then(function () {
                                liff.closeWindow();
                                
                            });
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
                    document.getElementById('useridprofilefield').textContent = profile.userId;
                })
            }   

        </script>

    </body>
</html>