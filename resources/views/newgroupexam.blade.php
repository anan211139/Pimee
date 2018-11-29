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
        <label class="add-header">เพิ่มชุดแบบฝึกหัด</label>
        {!! Form::open(['url' => 'newgroup']) !!}
            <div>
                <input placeholder="ชื่อแบบฝึกหัด" class="input-name " name="name">
            </div>
	    <button class="add-btn">เพิ่ม</button>
        <a class="cancel-btn" href="/selectclass">ยกเลิก</a>
        {!! Form::close() !!}
    </body>
</html>
