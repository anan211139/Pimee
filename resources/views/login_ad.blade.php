<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>พี่หมีติวเตอร์ ADMIN</title>
        <link rel="stylesheet" href="css/login_ad.css" />
        <!-- <link rel="stylesheet" href="css/footer.css" /> -->
        <link href="https://fonts.googleapis.com/css?family=Athiti|Kanit|Mitr|Roboto" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Prompt" rel="stylesheet">
        <link rel="shortcut icon" href="picture/bear_N.png">
    </head>
    <body style = " margin: 0;
                    background-color: #f7f7f7;
                    font-family: 'Kanit', sans-serif;
                    background-image: url('picture/bg-admin-01-01.png');
                    background-size: 100% auto;
                    background-attachment: fixed;"> 
        <div class="main-container">                   
            <div class="box">
                <div class="login-con">
                    <div class="picture-box">
                        <div>
                            <img class="pic-admin" src="picture/admin.png">
                        </div>
                    </div>
                    {!! Form::open(['url' => 'loginadminsubmit']) !!}
                        <div class="login-box">
                            <p class="head-admin">Admin Pimeetutor</p>
                            <div class="input-con">
                                <p>Username</p>
                                <input name = 'username'>
                            </div>
                            <div class="input-con">
                                <p>Password</p>
                                <input type="password" name = 'password'>
                            </div>
                            <button class="loginBtn">Log in</button>
                            @if(session('login'))
                                <div class="alert-message" style = "color : red; padding-bottom:10px;">
                                    {{session('login')}}
                                </div>
                            @endif
                        </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </body>
</html>