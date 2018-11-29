<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<div class="footer">
    <div class="footer-layout">
        <div class="footerCenter">
            <div class="tooltip">
                <img class="imgContact" src="picture/facebook-logo-button.png">
                <span class="tooltiptext">พี่หมีติวเตอร์</span>
            </div>
            <div class="tooltip">
                <img class="imgContact" src="picture/linefooter.png">
                <span class="tooltiptext">@พี่หมีติวเตอร์</span>
            </div>
            <div class="tooltip">
                <img class="imgContact" src="picture/web.png">
                <span class="tooltiptext">www.พี่หมีติวเตอร์.com</span>
            </div>
            <div class="tooltip" onclick="document.getElementById('report-footer').style.display = 'block';">
                <img class="imgContact" src="picture/icon.png">
                <span class="tooltiptext">รายงานปัญหา</span>
            </div>
        </div>
        <div class="footerRight">
            <img class="logo-nectec" src="picture/nectec_logo-1.png">
            <img class="logo-nectec" src="picture/nstda.png">
        </div>
    </div>
    <div id="report-footer" class="modal-footer">
        <form method="post" action="/addFeedback"  autocomplete="off" >
            <div class="modal-content-footer animate-footer" action="/action_page.php">
                <h2>รายงานปัญหา</h2>
                <div class="padding-pup-footer">
                    <p>รายงานเกียวกับ</p>
                    <select class="style-select-footer" name="type_id" id="type_id">
                        <option value="2">เกี่ยวกับแชทบอท</option>
                        <option value="3">เกียวกับเว็บไซต์</option>
                    </select>
                    <p>หัวข้อ</p>
                    <input class="input-head" type="text" name="head" id="head" required>
                    <p>รายละเอียด</p>
                    <textarea name="detail" id="detail" required></textarea>
                    <a id="report-close" class="close-footer" onclick="document.getElementById('report-footer').style.display='none';">
                        <img class="logo" src="picture/close.png">
                    </a>
                </div>
                <div class="btn-send-footer">
                    <button class="checkbox-layout-footer add-footer" type="submit" id="submitfeedback">
                        <img class="logo" src="picture/sent-mail.png">
                        <p>รายงานปัญหา</p>
                    </button>
                </div>
            </div>
              {{csrf_field()}}
        </form>

        <script>
        // $(document).on('click', '#submitfeedback', function(){
        //   alert("hi");
        //   var type_id = $('#about').val();
        //   var head = $('#head').val();
        //   var detail = $('#detail').val();
        //   console.log(type_id, head, detail);
        //   $.ajax({
        //     url:,
        //     method: "POST",
        //     data:{type_id:type_id},
        //     success:function(result){
        //       alert(result);
        //     }
        //   });
        // });
        </script>
    </div>
</div>
