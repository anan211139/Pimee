function topnav() {
    var x = document.getElementById("myTopnav");
    if (x.className === "topnav") {
        x.className += " responsive";
    } else {
        x.className = "topnav";
    }
}

function opentab(evt, idTab) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("menu-a");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(idTab).style.display = "block";
    evt.currentTarget.className += " active";
}
            
document.getElementById("defaultOpen").click();

// var iconMenu = document.getElementById("icon-menu");
// var countIcon = 1;
// iconMenu.addEventListener("click", openNav);
// function openNav() {
//     if(countIcon%2 != 0){
//         document.getElementById("sideMenu-res").style.width = "calc(230px + 2vw)";
//         document.getElementById("icon-res").src="picture/close.png";
//         countIcon++;
//     }else{
//         document.getElementById("sideMenu-res").style.width = "0";
//         document.getElementById("icon-res").src="picture/grid.png";
//         countIcon++;
//     }
// }
    
// var modal = document.getElementById('id01');
// var modal2 = document.getElementById('report');
// var reportClose = document.getElementById('report-close');
// var report = document.getElementById('btn-report');

// report.addEventListener("click",openReport);
// function openReport(){
//     modal2.style.display='block';
//     modal.style.display='none';  
// }

// reportClose.addEventListener("click",reportClosePupup);
// function reportClosePupup(){
//     modal2.style.display = "none";
// }

// window.onclick = function(event) {
//     if (event.target == modal) {
//         modal.style.display = "none";
//     }
//     if (event.target == modal2) {
//         modal2.style.display = "none";
//     }
// } 
var hambergerBtn = document.getElementById('hamberger');
var imgham = document.getElementById('imgham');
var hamcount = 0;

hambergerBtn.addEventListener("click",closeBtn);
function closeBtn(){
    if(hamcount%2 != 0){
        imgham.src = "picture/hamberger.png";
        hamcount++;
    }
    else{
        imgham.src = "picture/close.png";
        hamcount++;
    }
}  

function pop(id){
    var num = document.getElementById(id);

    var sta = num.getElementsByClassName('sta')[0].textContent;
    var chap = num.getElementsByClassName('chap')[0].textContent;
    var question = num.getElementsByClassName('question')[0].textContent;
    var ch_a = num.getElementsByClassName('ch_a')[0].textContent;
    var ch_b = num.getElementsByClassName('ch_b')[0].textContent;
    var ch_c = num.getElementsByClassName('ch_c')[0].textContent;
    var ch_d = num.getElementsByClassName('ch_d')[0].textContent;
    var solution = num.getElementsByClassName('solution')[0].textContent;
    var stu_ans = num.getElementsByClassName('stu-ans')[0].textContent;

    var main = document.getElementById("main-container");
    var createmodal = document.createElement("div");
    createmodal.className = "modal";
    createmodal.setAttribute("id", "id01");
    main.appendChild(createmodal);
    var modalContent = document.createElement("div");
    modalContent.className = "modal-content animate";
    createmodal.appendChild(modalContent);
    var container = document.createElement("div");
    container.className = "pop-container";
    modalContent.appendChild(container);
    var headAns = document.createElement("div");
    headAns.className = "head-ans" +" "+ "correct";
    container.appendChild(headAns);
    var headLable = document.createElement("label");
    headAns.appendChild(headLable);
    var textHead = document.createTextNode("ถูก");
    headLable.appendChild(textHead);

    var padding = document.createElement("div");
    padding.className = "pop-padding";
    container.appendChild(padding);

    var pB = document.createElement("p");
    padding.appendChild(pB);
    var b3 = document.createElement("b");
    pB.appendChild(b3);
    var textB = document.createTextNode("บท : ");
    b3.appendChild(textB);
    var span3 = document.createElement("span");
    pB.appendChild(span3);
    var BB = document.createTextNode(chap);
    span3.appendChild(BB);

    var p1 = document.createElement("p");
    p1.className = "bold";
    var text1 = document.createTextNode("คำถาม");
    p1.appendChild(text1);
    padding.appendChild(p1);
    var pCon = document.createElement("p");
    var textCon = document.createTextNode(question);
    pCon.appendChild(textCon);
    padding.appendChild(pCon);

    var ans = document.createElement("div");
    ans.className = "ans";
    padding.appendChild(ans);

    var ansDiv1 = document.createElement("div");
    ans.appendChild(ansDiv1);

    var p2 = document.createElement("p");
    p2.className = "bold";
    var text2 = document.createTextNode("ตัวเลือก");
    p2.appendChild(text2);
    ansDiv1.appendChild(p2);
    
    var ol = document.createElement("OL");
    ansDiv1.appendChild(ol);
    
    var choice = [ch_a,ch_b,ch_c,ch_d];

    for(var i = 0; i < 4; i++){
        var li = document.createElement("LI");
        var t = document.createTextNode(choice[i]);
        li.appendChild(t);
        ol.appendChild(li)
    }


    var ansDiv2 = document.createElement("div");
    ansDiv2.className = "ans-con";
    ans.appendChild(ansDiv2);

    var pA = document.createElement("div");
    ansDiv2.appendChild(pA);
    var b = document.createElement("b");
    pA.appendChild(b);
    var textA = document.createTextNode("เฉลย ข้อ : ");
    b.appendChild(textA);
    var span = document.createElement("span");
    pA.appendChild(span);
    var correctA = document.createTextNode(solution);
    span.appendChild(correctA);

    var pA2 = document.createElement("div");
    ansDiv2.appendChild(pA2);
    var b2 = document.createElement("b");
    pA2.appendChild(b2);
    var textA2 = document.createTextNode("นักเรียนตอบ ข้อ : ");
    b2.appendChild(textA2);
    var span2 = document.createElement("span");
    pA2.appendChild(span2);
    var correctA2 = document.createTextNode(stu_ans);
    span2.appendChild(correctA2);

    var closeBtn = document.createElement("a");
    closeBtn.className ="close";
    closeBtn.setAttribute("id", "closeBtn");
    container.appendChild(closeBtn);
    var imgClose = document.createElement("img");
    imgClose.src = "picture/close.png";
    imgClose.className = "logo";
    closeBtn.appendChild(imgClose);
    
    var modal = document.getElementById('id01');
    window.onclick = function(event) {
        if (event.target == modal) {
            $(".modal").remove();
        }
    }

    var closeBtn = document.getElementById("closeBtn");
    closeBtn.addEventListener("click", function(){
        $(".modal").remove();
    });
}