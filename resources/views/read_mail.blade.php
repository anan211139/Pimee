<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>พี่หมีติวเตอร์ ADMIN</title>
        <link rel="stylesheet" href="../../css/read_mail.css" />
        <link rel="stylesheet" href="../../css/footer.css" />
        <link href="https://fonts.googleapis.com/css?family=Athiti|Kanit|Mitr|Roboto" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Prompt" rel="stylesheet">
        <link rel="shortcut icon" href="picture/bear_N.png">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    </head>
    <body>
      @php
      $readMail_info = json_decode($readMail_info, true);
      @endphp
      <div class="main-container">
        <div class="topnav" id="myTopnav">
            <div class="logo-nav">
                <a>
                    <div>
                        <img class="logo" src="../../picture/bear_N.png" onclick = "window.location.href = '/mail';">
                    </div>
                    <p class="namepi" onclick = "window.location.href = '/mail';">พี่หมีติวเตอร์</p>
                </a>
            </div>
            <div class="icon">
                <a class="menu-a">
                    <div>
                        <img class="logo" src="../../picture/file.png">
                    </div>
                    <p id="add" onclick = "window.location.href = '/addExam_admin';">เพิ่มแบบฝึกหัด</p>
                </a>
                <a class="username" href ="/dashboard">ggg</a>
                <a href ="/logout">
                    <img class="logo" src="../../picture/exit-to-app-button.png">
                </a>
            </div>
        </div>
        <div class="margin-content">
            <div class="read-con">
                <div class="tools-con">
                    <div class="tools">
                        <img class="logo" src="../../picture/left-arrow.png" onclick = "window.location.href = '/mail';" >
                    </div>
                    <div>
                        <input type="hidden" id="feedback_id" name="" value="{{$id}}">
                        <label>{{$header}}</label>
                        <span>{{$span}}</span>
                    </div>
                    <div class="tools">
                        <img class="logo" id="delete" src="../../picture/garbage_white.png">
                    </div>
                </div>
                <div class="read-content">
                    <div>
                        <h2>{{$readMail_info[0]['head']}}</h2>
                        <label>จาก :</label>
                        <span>{{$readMail_info[0]['name']}}</span>
                    </div>
                    <div class="mail-content">
                        <p>{{$readMail_info[0]['details']}}</p>
                    </div>
                </div>
            </div>
        </div>
      </div>
    </body>
    <script>
      window.onload = pageLoad;
      function pageLoad(){
        var data = @json($readMail_info);
        console.log(data);
      }

      $(document).on('click', '#delete', function(){
        var feedback_id = $('#feedback_id').val();
          if (confirm("คุณต้องการลบรายงานปัญหานี้ใช่ไหม?")) {
            $.ajax({
              url:"{{route('delete.eachfeedback')}}",
              method: "GET",
              data:{feedback_id:feedback_id},
              success:function(result){
                alert(result);
                window.location.replace("/mail");
              }
            });
          }
      });




    </script>
</html>
