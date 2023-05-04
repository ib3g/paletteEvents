const toggleSwitch = document.querySelector('.theme-switch input[type="checkbox"]');
const currentTheme = localStorage.getItem('theme');

if (currentTheme) {
    document.documentElement.setAttribute('data-theme', currentTheme);
  
    if (currentTheme === 'dark') {
        toggleSwitch.checked = true;
    }
}

function switchTheme(e) {
    if (e.target.checked) {
        document.documentElement.setAttribute('data-theme', 'dark');
        localStorage.setItem('theme', 'dark');
    }
    else {        document.documentElement.setAttribute('data-theme', 'light');
          localStorage.setItem('theme', 'light');
    }    
}

toggleSwitch.addEventListener('change', switchTheme, false);
$('.paiment-btn').click(function(){
    var route = $(this).data('endpoint');
    var type = $(this).data('type');
    var event = $(this).data('event');
    var redirect = $(this).data('redirect');
    let stripe = Stripe($(this).data('stripe'));
    console.log('stripeddd', stripe);
    $.ajax({
        url: route,
        type: 'POST',
        data: {'type':type, 'event':event },
        dataType: 'json',
        success: function (response) {
           if(response.unlogged) {
               window.location.href = redirect;
           }  else if(response.session_id) {
               stripe
                   .redirectToCheckout({
                       sessionId: response.session_id
                   })
                   .then(function (result) {
                   });
           }
        },
        error: function (xhr, textStatus, errorThrown) {
            console.log(xhr);
        }
    });
});