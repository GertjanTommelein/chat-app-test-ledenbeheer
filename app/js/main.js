$('#sessionForm').submit(function(event) {
    event.preventDefault();

    $.post('http://localhost:8000/?q=chatsession', $('#sessionForm').serialize())
     .done(function(data) {
        let pdata = JSON.parse(data);
        console.log(pdata);
        localStorage.setItem('sessionId',pdata.id);

        $.get('http://localhost:8000/?q=getmessages', { sessionId: localStorage.getItem('sessionId') })
        .done(function(data) {
          let pdata = JSON.parse(data);
          console.log(pdata);
          for(let i = 0; i < pdata.length;i++) {
             console.log(i);
             createChatMessage(pdata[i].moderator_id, pdata[i].firstname, pdata[i].message);
          }
          // scroll down to recent messages
          document.getElementsByClassName('chat-messages')[0].scrollTop = document.getElementsByClassName('chat-messages')[0].scrollHeight
        });

        $('#register').hide();
        $('.chat-messages').show();
        $('.chat-textarea').show();
     });
});

$('#submitMessage').click(function() {
   let message = document.getElementById('chatMessage');
   $.post('http://localhost:8000/?q=sendmessage', { sessionId: localStorage.getItem('sessionId'), message: message.value })
    .done(function(data) {
      console.log(data);
      $.get('http://localhost:8000/?q=getmessages', { sessionId: localStorage.getItem('sessionId') })
       .done(function(data) {
         let pdata = JSON.parse(data);
         document.getElementsByClassName('chat-messages')[0].innerHTML = '';
         console.log(pdata);
         for(let i = 0; i < pdata.length;i++) {
            console.log(i);
            createChatMessage(pdata[i].moderator_id, pdata[i].firstname, pdata[i].message);
         }
         // scroll down to recent messages
         document.getElementsByClassName('chat-messages')[0].scrollTop = document.getElementsByClassName('chat-messages')[0].scrollHeight
       });
    })
});

$('#chatButton').click(function() {
   $('#chat').show("slow");
   $('#chatButton').hide();
   // check if a session ID is active
   if(localStorage.getItem('sessionId') === null) {
      $('#register').show();
      $('.chat-messages').hide();
      $('.chat-textarea').hide();
   }else {

      $.get('http://localhost:8000/?q=getmessages', { sessionId: localStorage.getItem('sessionId') })
      .done(function(data) {
        let pdata = JSON.parse(data);
        document.getElementsByClassName('chat-messages')[0].innerHTML = '';
        console.log(pdata);
        for(let i = 0; i < pdata.length;i++) {
           createChatMessage(pdata[i].moderator_id, pdata[i].firstname, pdata[i].message);
        }
        // scroll down to recent messages
        document.getElementsByClassName('chat-messages')[0].scrollTop = document.getElementsByClassName('chat-messages')[0].scrollHeight
      });

      $('#register').hide();
      $('.chat-messages').show();
      $('.chat-textarea').show();
   }

});

$('#chatClose').click(function() {
   $('#chat').hide("slow");
   $('#chatButton').show();
});

function createChatMessage(moderator, moderator_firstname = null, message) {
  let messagesContainer = document.getElementsByClassName('chat-messages')[0];
  let messageContainer = document.createElement('div');
  messageContainer.classList.add('message-container');
  let heading = document.createElement('h6');
  let chatBubble = document.createElement('div');
  chatBubble.classList.add('chat-bubble');
  let paragraph = document.createElement('p');
  let pText = document.createTextNode(message);
  if(moderator) {
     messageContainer.classList.add('align-left');
     let headingText = document.createTextNode(moderator_firstname);
     heading.appendChild(headingText);
  }else {
     messageContainer.classList.add('align-right');
     chatBubble.classList.add('bg-blue');
  }
  paragraph.appendChild(pText);
  chatBubble.appendChild(paragraph);
  messageContainer.appendChild(heading);
  messageContainer.appendChild(chatBubble);
  messagesContainer.appendChild(messageContainer);
}