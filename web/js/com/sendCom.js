function sendCommand(that) {
  var commid = that.getAttribute("comm-id");
  var urzid = that.getAttribute("urz-id");
  var shout = that.getAttribute("sh-out") == 'true' ? true : false;
  $.ajax({
               url:        Routing.generate('exec_com', {
                 'id': urzid,
                 'command_id': commid
             }),
               type:       'POST',
               data:        {
                 model: true
               },
               dataType:   'json',
               async:      true,

               success: function(data, status) {
                 if(data == 'error'){
                   alert("Comenda nieudana");
                 } else {
                   console.log('success');
                   console.log(data);
                   dataHtml = data.replace(/(?:\r\n|\r|\n)/g, '<br>');
                   texte = '<div style="background-color: rgba(25,25,25, 0.1);padding: 10px;border-radius: 10px;">';
                   texte += dataHtml;
                   texte += '</div>';
                   if (shout) {
                     bootbox.alert({
                        title: "Console output",
                        message: texte,
                        onEscape: true,
                        backdrop: true
                    });
                   }
                 }
               },
               error : function(xhr, textStatus, errorThrown) {
                  alert('Ajax request failed.');
                  console.log(errorThrown);
               }
            });
}
