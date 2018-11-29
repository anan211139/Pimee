<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>พี่หมีติวเตอร์</title>
        <link rel="stylesheet" href="css/status.css" />
        <link href="https://fonts.googleapis.com/css?family=Athiti|Kanit|Mitr|Roboto" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Prompt" rel="stylesheet">
        <link rel="shortcut icon" href="picture/bear_N.png">
    </head>
    <body>
        <h1>
            @if(session('reporttype'))
                {{session('reporttype')}}
            @endif
        </h1>
        <p>
            @if(session('reportdetail'))
                {{session('reportdetail')}}
            @endif
        </p>
        @if(session()->has('connectstatus'))
            <?php 
                session()->forget('connectstatus');
            ?>
            <script>
                window.onload = callback;
                function callback(){
                    liff.sendMessages(
                        [
                            {
                                type: 'text',
                                text: "[ลงทะเบียนเรียบร้อยแล้ว]"
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
            </script>
        @endif
    </body>
</html>