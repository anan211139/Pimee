<script>
    $('#subid').change(function(){
        var subject = $(this).val();
        var onwer = $('#onwer').val();
        var chapter = $('#chapter').val();
        var level = $('#level').val();
        console.log(subject,onwer,chapter,level);
        var _token =$('input[name="_token"]').val(); 
        console.log(subject);
        $.ajax({
            url:"{{route('selectchapterAjax')}}",
            method:"POST",
            data:{subject:subject,_token:_token},
            success:function(result){
                // $('chapter').html(result);
                var chap = document.getElementById('chapter');
                chap.innerHTML = result;
            },
        })
        $.ajax({
            url:"{{route('updateexamlist')}}",
            method:"POST",
            data:{subject:subject,onwer:onwer,chapter:chapter,level:level,_token:_token},
            success:function(result){
                var examlist = document.getElementById('examlist');
                examlist.innerHTML = result;
            },
        })
    });
    $('#onwer').change(function(){
        var subject = $('#subid').val();
        var onwer = $('#onwer').val();
        var chapter = $('#chapter').val();
        var level = $('#level').val();
        console.log(subject,onwer,chapter,level);
        var _token =$('input[name="_token"]').val(); 
        $.ajax({
            url:"{{route('updateexamlist')}}",
            method:"POST",
            data:{subject:subject,onwer:onwer,chapter:chapter,level:level,_token:_token},
            success:function(result){
                console.log('#onwer');
                var examlist = document.getElementById('examlist');
                examlist.innerHTML = result;
            },
        })
    });
    $('#level').change(function(){
        var subject = $('#subid').val();
        var onwer = $('#onwer').val();
        var chapter = $('#chapter').val();
        var level = $('#level').val();
        console.log(subject,onwer,chapter,level);
        var _token =$('input[name="_token"]').val(); 
        $.ajax({
            url:"{{route('updateexamlist')}}",
            method:"POST",
            data:{subject:subject,onwer:onwer,chapter:chapter,level:level,_token:_token},
            success:function(result){
                var examlist = document.getElementById('examlist');
                examlist.innerHTML = result;
            },
        })
    });
    $('#chapter').change(function(){
        var subject = $('#subid').val();
        var onwer = $('#onwer').val();
        var chapter = $('#chapter').val();
        var level = $('#level').val();
        console.log(subject,onwer,chapter,level);
        var _token =$('input[name="_token"]').val(); 
        $.ajax({
            url:"{{route('updateexamlist')}}",
            method:"POST",
            data:{subject:subject,onwer:onwer,chapter:chapter,level:level,_token:_token},
            success:function(result){
                console.log('#chapter');
                var examlist = document.getElementById('examlist');
                examlist.innerHTML = result;
            },
        })
    });

    $('.btmfeedback').on("click",function(){
        // console.log("Got it");
        var examid = $(this).parent().children('#examid').val();
        var report = $(this).parent().children('#inputreport').children('.report').val();
        var _token =$('input[name="_token"]').val(); 
        var test = $(this);
        // console.log(examid,report);
        $.ajax({
            url : "{{route('Ajaxsendreport')}}",
            method :"POST",
            data : {exam_id:examid,report:report,_token:_token},
            success : function(result){
                test.html(result);
            },
        })
    });
    @foreach($group as $obj)
        $('.examgroup{{$obj["id"]}}').on("click",function(){
            var id = $(this).val();
            var asd = '{{$obj["id"]}}';
            console.log(asd);
            var _token =$('input[name="_token"]').val(); 
            $.ajax({
                url : "{{route('Ajaxquerygroupexam')}}",
                method :"POST",
                data : {id:id,_token:_token},
                success : function(result){
                    $('.manage-grid').html(result);
                    $('.manage-con').children('h2').html('แบบฝึกหัดใน');
                    $('#formgroupexam').val(id);
                },
            })
        });
    @endforeach
    $('.btmsendgroup').on("click",function(){
        $(this).parent().parent().children('form').children('.send').click();
    });

</script>