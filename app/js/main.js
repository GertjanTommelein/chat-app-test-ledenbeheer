$('#sessionForm').submit(function(event) {
    event.preventDefault();

    $.post('http://localhost:8000/?q=chatsession', $('#sessionForm').serialize())
     .done(function(data) {
        console.log(data);
        $('#register').hide();
        $('.chat-messages').show();
        $('.chat-textarea').show();
     });
});

$('#chatButton').click(function() {
   $('#chat').show("slow");
   $('#chatButton').hide();

   $('#register').show();
   $('.chat-messages').hide();
   $('.chat-textarea').hide();
});

$('#chatClose').click(function() {
   $('#chat').hide("slow");
   $('#chatButton').show();
});