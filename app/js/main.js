$('#sessionForm').submit(function(event) {
    event.preventDefault();

    $.post('localhost:8000/?q=chatsession', $('#sessionForm').serialize())
     .done(function(data) {
        console.log(data);
     });
});