<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>พี่หมีติวเตอร์ ADMIN</title>
        <link rel="stylesheet" href="css/add_exam.css" />
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
                <a>
                    <div>
                        <img class="logo" src="picture/bear_N.png">
                    </div>
                    <p class="namepi">พี่หมีติวเตอร์</p>
                </a>
            </div>
            <div class="icon">
                <a class="menu-a">
                    <div>
                        <img class="logo" src="picture/file.png">
                    </div>
                    <p id="add">เพิ่มแบบฝึกหัด</p>
                </a>
                <a class="username" href ="/dashboard">ggg</a>
                <a href ="/logout">
                    <img class="logo" src="picture/exit-to-app-button.png">
                </a>
            </div>
        </div>
        <div class="margin-content">
                <h2>เพิ่มแบบฝึกหัด</h2>
                <form action="/addExamSubmit_teacher" enctype="multipart/form-data" method="post"  autocomplete="off" >
                <div class="form-con">
                    <div class="grid-con">
                        <div>
                            <p>โจทย์*</p>
                            <textarea name="exam" required></textarea>
                        </div>
                        <div>
                            <p>กรุณาเลือกวิชาและบท*</p>
                            <div class="flex-con">
                                <select class="style-select" name="subject" id="subject"  required>
                                    <option value="">เลือกวิชา</option>
                                    @foreach($subject as $subject_name)
                                      <option value="{{$subject_name->id}}">{{$subject_name->name}}</option>
                                    @endforeach
                                </select>
                                <select class="style-select" name="chapter" id="chapter"  required>
                                  <option value="">เลือกบท</option>
                                </select>
                            </div>
                            <p>รูปภาพประกอบโจทย์ (ถ้ามี)</p>
                            <input type="file" name="image" value="" class="upload" accept="image/*">
                            <input type="hidden" name="_token" value="{{csrf_token()}}">
                            <p>ตัวเลือก*</p>
                            <ol class="choice-ol">
                                <li>
                                    <input name="choice1"  required>
                                </li>
                                <li>
                                    <input name="choice2"  required>
                                </li>
                                <li>
                                    <input name="choice3"  required>
                                </li>
                                <li>
                                    <input name="choice4"  required>
                                </li>
                            </ol>
                            <p>เฉลย*</p>
                            <div>
                                <span class="radio-con" name="answer">
                                    <input type="radio" value="1" name="answer" id="choice1" required>
                                    <label for="choice1">1.</label>
                                    <input type="radio" value="2" name="answer" id="choice2">
                                    <label for="choice2">2.</label>
                                    <input type="radio" value="3" name="answer" id="choice3">
                                    <label for="choice3">3.</label>
                                    <input type="radio" value="4" name="answer" id="choice4">
                                    <label for="choice4">4.</label>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div style="width:100%; text-align:right;margin: 10px 0;">
                        <button class="add-btn" type="submit">เพิ่มแบบฝึกหัด</button>
                    </div>
                </div>
                {{csrf_field()}}
            </form>
        </div>
        {{csrf_field()}}
        @include('inc.footer');
        @if($errors->any())
        <script type='text/javascript'>alert('{{$errors->first()}}');</script>
        @endif
      </div>
    </body>
    <script>
      $('#subject').change(function(){
        var value = $(this).val();
        console.log(value);
        var _token = $('input[name="_token"]').val();
        $.ajax({
          url:"{{route('dropdown.addExam_subject')}}",
          method:"POST",
          data:{value:value,_token:_token},
          success:function(result){
            console.log(result);
              $('#chapter').html(result);
          }
        })
      }); //เรียกข้อมูลเมื่อมีการเลือกวิชา
    </script>
</html>
