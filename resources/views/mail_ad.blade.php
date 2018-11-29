<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>พี่หมีติวเตอร์ ADMIN</title>
        <link rel="stylesheet" href="css/mail_ad.css" />
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
                        <img class="logo" src="picture/bear_N.png" onclick = "window.location.href = '/mail';" >
                    </div>
                    <p class="namepi" onclick = "window.location.href = '/mail';">พี่หมีติวเตอร์</p>
                </a>
            </div>
            <div class="icon">
                <a class="menu-a">
                    <div>
                        <img class="logo" src="picture/file.png">
                    </div>
                    <p id="add" onclick = "window.location.href = '/addExam_admin';">เพิ่มแบบฝึกหัด</p>
                </a>
                <a class="username" href ="/dashboard">ggg</a>
                <a href ="/logout">
                    <img class="logo" src="picture/exit-to-app-button.png">
                </a>
            </div>
        </div>
        <div class="margin-content">
            <h2>รายงานปัญหา</h2>
            <table role="table">
                <thead role="rowgroup">
                    <col width="30">
                    <col width="60">
                    <col width="70">
                    <col width="150">
                    <col width="50">
                    <tr role="row">
                        <th role="columnheader">
                            <input id="checkAll" class="option" type="checkbox">
                        </th>
                        <th role="columnheader" colspan="4">
                            <div class="head-table">
                                <div class="option-con">
                                    <a class="option" onclick="document.location.reload();">
                                        <img class="logo2" src="../picture/refresh.png">
                                    </a>
                                    <a class="option" id="delete">
                                        <img class="logo2" src="picture/garbage_white.png">
                                    </a>
                                </div>
                                <div>
                                  <select id="sortBy" class="style-select">
                                      <option value="0">ทุกเรื่อง</option>
                                      <option value="1">เกี่ยวกับแบบฝึกหัด</option>
                                      <option value="2">เกี่ยวกับแชทบอท</option>
                                      <option value="3">เกียวกับเว็บไซต์</option>
                                  </select>
                                </div>
                            </div>
                        </th>
                    </tr>
                </thead>

                <tbody role="rowgroup" id="tableArea">

                </tbody>
            </table>
      </div>
        {{csrf_field()}}
        <script>
            $(document).ready(function(){
              var value = $('#sortBy').val();
              var _token = $('input[name="_token"]').val();
              $.ajax({
                url:"{{route('dropdown.mail_ad')}}",
                method:"POST",
                data:{value:value,_token:_token},
                success:function(result){
                  console.log(result);
                  $('#tableArea').html(result);
                }
              })
            }); //เรียกข้อมูลขจดหมายdefaultเมื่อเพจโหลด

            $('#sortBy').change(function(){
              var value = $(this).val();
              console.log(value);
              var _token = $('input[name="_token"]').val();
              $.ajax({
                url:"{{route('dropdown.mail_ad')}}",
                method:"POST",
                data:{value:value,_token:_token},
                success:function(result){
                  console.log(result);
                  $('#tableArea').html(result);

                }
              })
            }); //เรียกข้อมูลเมื่อมีการใช้ filter

            $('#checkAll').change(function(){
               $('input:checkbox').not(this).prop('checked', this.checked);
            }); //เลือกจดหมายทุกอัน

            $(document).on('click', '#delete', function(){
              var feedback_id = [];
              $('.delete_checkbox:checked').each(function(){
                feedback_id.push($(this).val());
              });
              if(feedback_id.length > 0){
                if (confirm("คุณต้องการลบรายงานปัญหานี้ใช่ไหม?")) {
                  $.ajax({
                    url:"{{route('delete.feedback')}}",
                    method: "GET",
                    data:{feedback_id:feedback_id},
                    success:function(result){
                      alert(result);
                      document.location.reload();
                    }
                  });
                }
              } else {
                alert("กรุณาเลือกการรายงานปัญหา");
              }
            });
            var x, i, j, selElmnt, a, b, c;
            /*look for any elements with the class "custom-select":*/
            x = document.getElementsByClassName("custom-select");
                for (i = 0; i < x.length; i++) {
                    selElmnt = x[i].getElementsByTagName("select")[0];
                    /*for each element, create a new DIV that will act as the selected item:*/
                    a = document.createElement("DIV");
                    a.setAttribute("class", "select-selected");
                    a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
                    x[i].appendChild(a);
                    /*for each element, create a new DIV that will contain the option list:*/
                    b = document.createElement("DIV");
                    b.setAttribute("class", "select-items select-hide");

                    for (j = 0; j < selElmnt.length; j++) {
                        /*for each option in the original select element,
                        create a new DIV that will act as an option item:*/
                        c = document.createElement("DIV");
                        c.innerHTML = selElmnt.options[j].innerHTML;
                        c.addEventListener("click", function(e) {
                        /*when an item is clicked, update the original select box,
                        and the selected item:*/
                        var y, i, k, s, h;
                        s = this.parentNode.parentNode.getElementsByTagName("select")[0];
                        h = this.parentNode.previousSibling;
                        for (i = 0; i < s.length; i++) {
                            if (s.options[i].innerHTML == this.innerHTML) {
                                s.selectedIndex = i;
                                h.innerHTML = this.innerHTML;
                                y = this.parentNode.getElementsByClassName("same-as-selected");
                                for (k = 0; k < y.length; k++) {
                                    y[k].removeAttribute("class");
                                }
                                console.log(document.getElementById("sortBy").value);
                                this.setAttribute("class", "same-as-selected");
                                break;
                            }
                        }
                    h.click();
                });
                b.appendChild(c);
              }
              x[i].appendChild(b);
              a.addEventListener("click", function(e) {
                  /*when the select box is clicked, close any other select boxes,
                  and open/close the current select box:*/
                  e.stopPropagation();
                  closeAllSelect(this);
                  this.nextSibling.classList.toggle("select-hide");
                    this.classList.toggle("select-arrow-active");
                    });
                }
                function closeAllSelect(elmnt) {
                    /*a function that will close all select boxes in the document,
                    except the current select box:*/
                    var x, y, i, arrNo = [];
                    x = document.getElementsByClassName("select-items");
                    y = document.getElementsByClassName("select-selected");
                    for (i = 0; i < y.length; i++) {
                        if (elmnt == y[i]) {
                            arrNo.push(i)
                        } else {
                            y[i].classList.remove("select-arrow-active");
                        }
                    }
                    for (i = 0; i < x.length; i++) {
                        if (arrNo.indexOf(i)) {
                            x[i].classList.add("select-hide");
                        }
                    }
                }
            /*if the user clicks anywhere outside the select box,
            then close all select boxes:*/
            document.addEventListener("click", closeAllSelect);
        </script>
    </body>
</html>
