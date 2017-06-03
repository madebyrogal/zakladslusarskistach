$(document).ready(function(){   
    
    $(".del").click(function(e){ //Potwierdzenie usunięcia
        var ans = confirm('Czy napewno usunąć')
        if(ans){
            return true;
        }
        else{            
            e.preventDefault();
            return false;
        }
    });

    $('#formularz').submit(function(){
        var name = $('#name').val();
        var mail = $('#mail').val();
        var msg = $('#message').val();
        if(name && mail && msg){
            return true;
        }
        else{
            $('#alert').html('Uzupełnij brakujące pola!')
            if(name=='')$('#name').css({'border':'1px solid #d80000'});
            if(mail=='')$('#mail').css({'border':'1px solid #d80000'});
            if(msg=='')$('#message').css({'border':'1px solid #d80000'});
                return false;
        }

    });

    $("#tinyBrowser").click(function(e){ // Uruchomienie przeglądarki tinybrowser
        e.preventDefault()
        tinyBrowser ('zdjecie', 'url', 'image', window);
    });
});

//Wczytanie obrazków do dokumentu przed jego załadowaniem
function simplePreload()
{
  var args = simplePreload.arguments;
  document.imageArray = new Array(args.length);
  for(var i=0; i<args.length; i++)
  {
    document.imageArray[i] = new Image;
    document.imageArray[i].src = args[i];
  }
}



