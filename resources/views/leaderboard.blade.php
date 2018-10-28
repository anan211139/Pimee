<html>
    <head>
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>พี่หมีติวเตอร์</title>
        <link rel="stylesheet" href="../css/studentinfo.css" />
        <link rel="stylesheet" href="../css/leaderboard.css" />
        <link href="https://fonts.googleapis.com/css?family=Athiti|Kanit|Mitr|Roboto" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Prompt" rel="stylesheet">
        <link rel="shortcut icon" href="picture/bear_N.png">
    </head>
    <body>
        <h1>ตารางคะแนน</h1>
        <div class="box">
            <input type="text" name="line_code" id="useridprofilefield" style ="display:none;"></p>
            <div class="tabs">
                <div class="tab-2 friend">
                    <label for="tab2-1">เพื่อน</label>
                    <input id="tab2-1" name="tabs-two" type="radio" checked="checked">
                    <div>
                        <p>กำลังพัฒนา</p> 
                        <!-- <p id="useridfield"></p> -->
                    </div>
                </div>
                <div class="tab-2 all">
                    <label for="tab2-2">ทั้งระบบ</label>
                    <input id="tab2-2" name="tabs-two" type="radio">
                    <div>
                        <table>
                            @foreach($top_students as $top_students)
                                <?php
                                    $class_rank = "o";
                                    if($top_students->rank == 1){
                                        $class_rank = "st_rank";
                                    }else if ($top_students->rank == 2) {
                                        $class_rank = "nd_rank";
                                    }else if ($top_students->rank == 3) {
                                        $class_rank = "th_rank";
                                    }else{
                                        $class_rank = "other_rank";
                                    }
                                ?>
                                <tr class="<?php echo($class_rank) ?>">
                                    <td><img class="img_leader" src="{{ $top_students->local_pic }}"/></td>
                                    <td>{{ $top_students->name }}</td> 
                                    <td>{{ $top_students->point }}</td>
                                    <?php if($top_students->rank <= 3) {?>
                                            <td class="trophy"><img src="https://pimee.softbot.ai/img/{{$top_students->rank}}.png"></td>
                                    <?php }else{ ?>
                                            <td>{{ $top_students->rank }}</td>
                                    <?php } ?>
                                    
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div> 
        <div class="lb-myself flex">
            @foreach($rank_ms as $rank_ms)
                <table class="ms">
                    <tr>
                        <td><img class="img_leader" src="{{ $rank_ms->local_pic }}"/></td>
                        <td>{{ $rank_ms->name }}</td> 
                        <td>{{ $rank_ms->point }}</td>
                        <?php if($rank_ms->rank <= 3) {?>
                                <td class="trophy"><img src="https://pimee.softbot.ai/img/{{$rank_ms->rank}}.png"></td>
                        <?php }else{ ?>
                                <td>{{ $rank_ms->rank }}</td>
                        <?php } ?>
                        <p id="useridprofilefield"></p>
                    </tr>
                </table>
            @endforeach
        </div>
    </body>
</html>