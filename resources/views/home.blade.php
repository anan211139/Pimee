@extends('layout.app')
    @section('content')
    <div class="main-container">
        <div id="navbar">
            <div class="navLeft">
                <div><img class="logo" src="picture/bear_N.png"></div>
                <div class="logoBtn">พี่หมีติวเตอร์</div>
            </div>
            <div class="navRight">
                <a class="navLogin" href="#login">LOG IN</a>
                <a onclick="document.getElementById('id01').style.display='block'">Register</a>
            </div>
        </div>
        <a href="#detail" id ="arrowBtn"class="plus"><img id ="plus" src="picture/arrow.png"></a>
        <div id="login" style='background-image: url("picture/topbgforweb.png"); background-size: 100% auto; background-repeat: no-repeat;
        background-position: bottom right;
        height: 100%; '>
            <div class="layout">
                <div class="pMhee">
                    <p class="fonts">ยินดีต้อนรับ เข้าสู่</p>
                    <p class="name"><strong>พี่หมีติวเตอร์</strong></p>
                </div>
                {!! Form::open(['url' => 'loginsubmit']) !!}
                <!-- <form action="/login"> -->
                    <p class="fonts2">Username</p>
                    <input type="text" name="uname" required>
                    <p class="fonts2">Password</p>
                    <input type="password" name="pass" required>
                    <button class="loginBtn">LOG IN</button>
                <!-- </form> -->
                {!! Form::close() !!}
                <a class="forgot" href="">Forgot Password?</a><br>
                <span class="span">Don't have an account?</span>
                <a class="forgot" onclick="document.getElementById('id01').style.display='block'">Sign up</a>
                @include('inc.message')
                @include('inc.loginmessage')
            </div>
        </div>
        <div id="detail" class="detail" 
        style='
        background-image: url("picture/duck.png"),url("picture/ball.png");
        background-repeat: no-repeat,no-repeat;
        background-position: 5% 90%,90%,100%; 
        background-size: 110px 140px,155px 160px; '>
            <div class="iconContainer">
                <div class="iconStyle">
                    <img class="icon" src="picture/1024px-LINE_logo.svg.png">
                    <p>สามารถเข้าถึงได้ด้วย LINE</p>
                </div>
                <div class="iconStyle">
                    <img class="icon" src="picture/noti.png">
                    <p>จะมีการส่งข้อความแจ้งเตือนให้น้องๆทำข้อสอบ</p>
                </div>
                <div class="iconStyle">
                    <img class="icon" src="picture/chart.png">
                    <p>สามารถติดตามผลได้จากกราฟ</p>
                </div>
            </div>
            <div class="box">
                <div class="topic">
                    <p><b>ABOUT</b></p>
                </div>
                <p> พี่หมีติวเตอร์  โปรเเกรมจำลองบทสนทนา หรือเรียกสั้นๆว่าเเชทบอท ซึ่งอยู่บน Line
                    ที่จะมาเป็นติวเตอร์ส่วนตัว ให้กับน้องๆ  โดยพี่หมีติวเตอร์สามารถส่งข้อความเเจ้งเตือน
                    ให้น้องๆมาทบทวนบทเรียน  ซึ่งน้องๆสามารถเลือกบทเรียนที่จะทบทวนได้  โดยจะมีการเก็บคะแนน
                    เพื่อนำมาแลกของรางวัล เพื่อเป็นเเรงจูงใจให้เด็กมีเป้าหมายในการทบทวน อีกทั้งมีการวัดระดับ
                    ความเข้าใจในบทเรียนนั้นๆ พร้อมทั้งนำเสนอผลคะเเนนออกมาเป็นกราฟเพื่อให้ ผู้ปกครองเเละ
                    คุณครูสามารถเห็นการพัฒนาของเด็ก</p>
            </div>
        </div>
        <div class="detail2">
            <img style="width: 100%;height: auto;" src="picture/lowbg.png">
        </div>
        {{-- <div class="detail3">
            <div class="layoutVideo">
                <div class="intrinsic-container intrinsic-container-16x9">
                    <iframe width="auto" height="auto" src="https://www.youtube.com/embed/Yad6t_EgwVw" allowfullscreen>
                    </iframe>
                </div>
            </div>
        </div> --}}
        <div class="detail3">
            <div class="content">
                {{-- <h1>เริ่มต้นใช้งาน</h1> --}}
                <div class="layoutContentTutor">
                    <div>
                        <img class="imgContentTutor" src="picture/com.png">
                    </div>
                    <div class="layoutContentLeft">
                        <span class="button spanfont">เพิ่มห้องเรียน</span>
                        <h2>ในการใช้งานคุณครู 1 ท่าน สามารถติดตามผลห้องเรียนตนเองได้มากกว่า 1 ห้องเรียน</h2>
                        <p>โดยคุณครูต้องทำการ <b>เพิ่มห้องเรียน</b> ให้เรียบร้อย</p>
                        {{-- <p><b>หลังจากเพิ่มเรีย</b></p> --}}
                    </div>
                    <div class="layoutContentRight layoutContentBackground">
                        <span class="button spanfont">เลือกห้องเรียน</span>                        
                        <h2>คุณครูสามารถเลือกห้องเรียนที่มีอยู่เพื่อติดตามผล</h2>
                        <p>หลังจากเพิ่มห้องเรียนเรียบร้อยแล้ว <b>จะมีรายการห้องเรียนขึ้นให้เลือก</b></p> 
                        {{-- <p></p>                      --}}
                    </div>
                    <div class="layoutContentBackground">
                        <img class="imgContentTutor" src="picture/com-2.png">
                    </div>
                    <div>
                        <img class="imgContentTutor" src="picture/com-2.png">
                    </div>
                    <div class="layoutContentLeft">
                        <span class="button spanfont">ติดตามผล</span>                        
                        <h2>คุณครูสามารถ<br>ติดตามผลของห้องเรียนในแต่ละห้องได้</h2>
                        <p>โดยจะแสดง <b>คะแนนโดยรวมของห้องนั้นๆ</b> และลำดับของห้องเรียน</p>                         
                    </div>
                    <div class="layoutContentRight layoutContentBackground">
                        <span class="button spanfont">ติดตามผลรายบุคคล</span>                        
                        <h2>คุณครูจะสามารถดูคะแนนของนักเรียนรายคนได้</h2>
                        <p>โดยกดเข้าไปที่ <b>รายชื่อของนักเรียน</b> บนตารางรายชื่อ</p>
                    </div>
                    <div class="layoutContentBackground">
                        <img class="imgContentTutor" src="picture/com-2.png">
                    </div>
                        {{-- <div>
                            <img class="imgContentTutor2" src="picture/phone5.png">
                        </div> --}}
                        {{-- <div class="layoutContentLeft">
                            <h2>หรือ Scan QR Code</h2>
                            <p>Scan QR Code บริเวณ <b>หมายเลข 5</b> เพื่อเข้าสู่หน้าเพิ่มนักเรียน</p>                         
                        </div> --}}
                </div>
            </div>
            <div class="layoutVideo">
                <div class="intrinsic-container intrinsic-container-16x9">
                    <iframe width="auto" height="auto" src="https://www.youtube.com/embed/Yad6t_EgwVw" allowfullscreen>
                    </iframe>
                </div>
            </div>
        </div>
        @include('inc.footer');
        @if($errors->any())
        <script type='text/javascript'>alert('{{$errors->first()}}');</script>
        @endif
    </div>

        @include('inc.pop-up-regis')
@endsection
