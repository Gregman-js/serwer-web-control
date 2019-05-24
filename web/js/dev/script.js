var clas = (clas) => {return document.getElementsByClassName(clas)}
var ident = (id) => {return document.getElementById(id)}

window.addEventListener('DOMContentLoaded', (event) => {
    basic_s = clas('circle')[0].classList.contains('b-c-green');
    eventer();
    so = !(stat ? (!basic_s ? false : true) : (!basic_s ? true : false));
    updata();
    clockpick()
});

var modeles = 'true';

function clockpick() {
  $('.clockpicker').clockpicker()
	.find('input').change(function(){
		// TODO: time changed
	});
$('#demo-input').clockpicker({
	autoclose: true
});

if (true) {
	// Manual operations (after clockpicker is initialized).
	$('#demo-input').clockpicker('show') // Or hide, remove ...
			.clockpicker('toggleView', 'minutes');
}
}



var timermin = 60000;
var minutimer = false;
var uptimes = false;
var monolog = -1;

var newSess = false;
function updata() {
  if (so)
  $.ajax({
               url:        Routing.generate('update_dev', {
                 'id': id
             }),
               type:       'POST',
               data:        {
                 model: modeles,
                 force: 'false'
               },
               dataType:   'json',
               async:      true,

               success: function(data, status) {
                 console.log('ajax recived');
                 if(data == 'error'){
                   alert("Comenda nieudana");
                 } else {
                   dane = JSON.parse(data);
                   if (dane['status'] == '404' && so)click();
                   console.log(dane);
                   uptimes = dane['uptime'];
                   updateHtml(dane);
                   minutimer = setTimeout(updata, timermin);
                 }
               },
               error : function(xhr, textStatus, errorThrown) {
                  alert('Ajax request failed.');
               }
            });
}

function updateHtml(datek) {
  if (!so) {
    ident('uptime-text').innerHTML = "OFF";
  } else {
      var obj = ident('uptime-text');
      if (newSess) {
        var tim = "0 min";
        newSess = false;
      } else {
        var tim = uptimes != false ? uptimes['text'] : "OFFLINE";
      }
      obj.innerHTML = tim;
  }
  ident('mem-us').innerHTML = so ? datek['ram']['used']+" MB" : "OFF";
  ident('ram-proc-bar').style.width = so ? datek['ram']['procent']+"%" : "0%";
  ident('ram-proc-big').innerHTML = datek['ram']['procent'];
  var temp = ident('temp-wykaz');
  var txt = '';
  for (temarr of datek['temp']) {
    txt += '<li><a href="#">'+temarr[0]+' <span class="pull-right badge bg-navy">'+temarr[1]+'</span></a></li>';
  }
  temp.innerHTML = txt;
  var fans = ident('fan-wykaz');
  txt = '';
  for (fanarr of datek['fan']) {
    txt += '<li><a href="#">'+fanarr[0]+' <span class="pull-right badge bg-aqua">'+(so ? fanarr[1] : 0)+'</span></a></li>';
  }
  fans.innerHTML = txt;
}


var dane;
var clicked = false;
var statold = false;
var stat = false;
var block = false;
var basic_s = false;
var so = false;
function eventer() {
  clas('out-c')[0].addEventListener("click", click);
  clas('out-c')[0].addEventListener("mouseover", mouseover);
  clas('out-c')[0].addEventListener("mouseout", mouseout);
  ident('time-generator').addEventListener("click", sentShutdown);

}

function sentShutdown() {
  if (so)
  $.ajax({
               url:        Routing.generate('shutdown_dev', {
                 'id': id
             }),
               type:       'POST',
               data:        {
                 model: modeles,
                 time: ident('clock-inp').value.split(':')
               },
               dataType:   'json',
               async:      true,

               success: function(data, status) {
                 if(data == 'error'){
                   alert("Comenda nieudana");
                 } else {
                   console.log('success');
                 }
               },
               error : function(xhr, textStatus, errorThrown) {
                  alert('Ajax request failed.');
               }
            });
}

function click() {
  if (!block) {
  clas(stat ? "circle" : "circle-out")[0].style.top = '0%';
  clicked = true;
  stat = !stat;
  setTimeout(function() {
    if(!clicked)clas(!statold ? "circle" : "circle-out")[0].style.zIndex = '1';
    if(!clicked)clas(statold ? "circle" : "circle-out")[0].style.zIndex = '2';
    clas(!statold ? "circle" : "circle-out")[0].style.top = '100%';
    statold = stat;
  }, 100);
  var prop = stat ? (!basic_s ? 'on_dev' : 'off_dev') : (!basic_s ? 'off_dev' : 'on_dev');
  so = stat ? (!basic_s ? false : true) : (!basic_s ? true : false);
  $.ajax({
               url:        Routing.generate(prop, {
                 'id': id
             }),
               type:       'POST',
               data:        {
                 model: modeles
               },
               dataType:   'json',
               async:      true,

               success: function(data, status) {
                 if(data == 'error'){
                   alert("Comenda nieudana");
                 } else {
                   so = !so;
                   for (var i = 0; i < clas('active').length; i++) {
                     if(clas('active')[i].classList.contains('treeview'))continue;
                     iclas = clas('active')[i];
                   }
                   if(iclas.getElementsByTagName('i')[0].classList.contains('green'))
                   iclas.getElementsByTagName('i')[0].classList.remove('green');
                   if(iclas.getElementsByTagName('i')[0].classList.contains('red'))
                   iclas.getElementsByTagName('i')[0].classList.remove('red');
                   iclas.getElementsByTagName('i')[0].classList.add(so ? 'green' : 'red');
                   ident('grayer').style.filter = 'grayscale('+(so ? '0' : '1')+')';

                   if (so == true){
                     updata();
                     newSess = true;
                     ident('uptime-text').innerHTML = "0 min";
                   } else {
                     clearTimeout(minutimer);
                     updateHtml(dane);
                   }
                 }
               },
               error : function(xhr, textStatus, errorThrown) {
                  alert('Ajax request failed.');
               }
            });
          }
}

function mouseout() {
  if (!block) {
  if(statold != stat){
    clas(stat ? "circle" : "circle-out")[0].style.zIndex = '2';
    clas(stat ? "circle-out" : "circle")[0].style.zIndex = '1';
  }
    if (clicked) {
    clicked = false;
  }
  setTimeout(function() {
    clas(stat ? "circle" : "circle-out")[0].style.top = '100%';
    clas("circle-out")[0].style.transform = 'scale(1)';
    clas("circle")[0].style.transform = 'scale(1)';
  }, (statold != stat ? 200 : 0));
  clas("circle-shadow")[0].style.boxShadow = 'inset 0px 0px 0px 0px rgba(0,0,0,0.75)';
}
}
function mouseover() {
  if (!block) {
  clas(stat ? "circle" : "circle-out")[0].style.zIndex = '2';
  clas(stat ? "circle-out" : "circle")[0].style.zIndex = '1';
  clas(stat ? "circle" : "circle-out")[0].style.top = '50%';
  clas(!stat ? "circle" : "circle-out")[0].style.transform = 'scale(0.9)';
  clas("circle-shadow")[0].style.boxShadow = 'inset 0px 0px 10px 3px rgba(0,0,0,0.75)';
  clicked = false;
}
}
