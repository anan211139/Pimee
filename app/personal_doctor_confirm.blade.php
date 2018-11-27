<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>botest</title>
</head>
<link href="https://fonts.googleapis.com/css?family=Kanit" rel="stylesheet">
<!-- <link rel="stylesheet" href="css/stylecss_pploy.css" /> -->
<link rel="stylesheet" href="{{URL::asset('css/stylecss_pploy.css')}}">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
 <!--  <script src="https://res.wx.qq.com/mmbizwap/zh_CN/htmledition/js/vconsole/3.0.0/vconsole.min.js"></script> -->
  <!-- <script>
    var vConsole = new VConsole();
  </script> -->
<style>
#sendmessagebutton {
    background-color: #4CAF50; /* Green */
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;
}


#sendmessagebutton {border-radius: 4px;}

</style>
<body>           
    <div class="content card">
        <div id="us_id"></div>
      <center> <h4> รหัสคุณหมอ: {{$record->doctor_id}} </h4> 
       <h4>  ชื่อ: {{$record->name}}</h4> 
       <h4>  นามสกุล: {{$record->lastname}}</h4> 
        <button class ="send-btn">ยืนยัน</button></center>
                <div class="title m-b-md">
                   <div id="chartdiv"></div>
                </div>    
                
        </div>
         



    <button id="openwindowbutton" style="display:none" style="display:none" >open window</button>
    <button id="openwindowexternalbutton" style="display:none">open window external</button>





    <div id="profileinfo" style="display:none">
        <h2>Profile</h2>
        <div id="profilepicturediv">
        </div>
        <table border="1">
            <tr>
                <th>userId</th>
                <td id="useridprofilefield"></td>
            </tr>
            <tr>
                <th>displayName</th>
                <td id="displaynamefield"></td>
            </tr>
            <tr>
                <th>statusMessage</th>
                <td id="statusmessagefield"></td>
            </tr>
        </table>
    </div> 
    <div id="liffdata" style="display:none">
        <h2>LIFF Data</h2>
        <table border="1">
            <tr>

                <th>language</th>
                <td id="languagefield"></td>
            </tr>
            <tr>
                <th>context.viewType</th>
                <td id="viewtypefield"></td>
            </tr>
            <tr>
                <th>context.userId</th>
                <td id="useridfield"></td>
            </tr>
            <tr>
                <th>context.utouId</th>
                <td id="utouidfield"></td>
            </tr>
            <tr>
                <th>context.roomId</th>
                <td id="roomidfield"></td>
            </tr>
            <tr>
                <th>context.groupId</th>
                <td id="groupidfield"></td>
            </tr>
        </table>
        
    </div>
    <!-- LIFF SDK  -->
    <script src="https://d.line-scdn.net/liff/1.0/sdk.js"></script>
    <script >

               $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });



      var user_id_line;
      var roomId;
      var  doctor_id = {{$record->doctor_id}};
      window.onload = function (e) {
       var x = 'test';
    // init 
    // https://developers.line.me/ja/reference/liff/#initialize-liff-app
    liff.init(function (data) {
        getProfile();
        initializeApp(data);
       
    });
    
  $('.send-btn').on('click', function(){
                   //alert(user_id_line);
// document.getElementById('us_id').innerHTML = user_id_line;
                    var _token = $('input[name="_token"]').val(); 
                    $.ajax({
                        url:"{{route('send_code')}}",
                        method:"POST",
                         data:{doctor_id:doctor_id, roomId:roomId, user_id_line:user_id_line,_token:_token},
                         


                    }).then(function () {
                        //window.alert("ยืนยันคุณหมอประจำตัวคุณแม่");
                        liff.closeWindow()

                    })
              
            });




   
    // document.getElementById('sendmessagebutton').addEventListener('click', function () {
    //     // https://developers.line.me/ja/reference/liff/#liffsendmessages()
    //     liff.sendMessages([{
    //         type: 'text',
    //         text: 'ยืนยันคุณหมอ:'+ doctor_id
    //     }])//.
    //     .then(function () {
    //         //window.alert("ยืนยันคุณหมอประจำตัวคุณแม่");
    //         liff.closeWindow()

    //     })
    //     .catch(function (error) {
    //         window.alert("Error sending message: " + error);
    //     });
    // });
       
            
};

function getProfile(){
    // https://developers.line.me/ja/reference/liff/#liffgetprofile()
    return liff.getProfile().then(function (profile) {
        document.getElementById('useridprofilefield').textContent = profile.userId;
        document.getElementById('displaynamefield').textContent = profile.displayName;

        user_id_line =  profile.userId;


        var profilePictureDiv = document.getElementById('profilepicturediv');
        if (profilePictureDiv.firstElementChild) {
            profilePictureDiv.removeChild(profilePictureDiv.firstElementChild);
        }
        var img = document.createElement('img');
        img.src = profile.pictureUrl;
        img.alt = "Profile Picture";
        img.width = 200;
        profilePictureDiv.appendChild(img);

        document.getElementById('statusmessagefield').textContent = profile.statusMessage;
    }).catch(function (error) {
        window.alert("Error getting profile: " + error);
    });
}

function initializeApp(data) {

 
    document.getElementById('languagefield').textContent = data.language;
    document.getElementById('viewtypefield').textContent = data.context.viewType;
    document.getElementById('useridfield').textContent = data.context.userId;
    document.getElementById('utouidfield').textContent = data.context.utouId;
    document.getElementById('roomidfield').textContent = data.context.roomId;
    document.getElementById('groupidfield').textContent = data.context.groupId;
}
</script>
</body>
</html>