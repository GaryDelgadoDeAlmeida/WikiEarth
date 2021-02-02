$('.slider').owlCarousel({
    loop:true,
    autoplay: true,
    autoplayTimeout: 3000,
    autoplayHoverPause: true,
    responsiveClass:true,
    responsive:{
        0:{
            items: 1,
            nav: false,
            loop: true
        },
        600:{
            items: 2,
            nav: false,
            loop: true
        },
        1000:{
            items: 3,
            nav: false,
            loop: true
        }
    }
});

$('#livingThing-menu').click(() => {
    let livingThingSubmenu = $('#livingThing-submenu');
    $(this).find('img').css({'transform': 'rotate(0)'});
    
    if(livingThingSubmenu.css('display') == "none") {
        livingThingSubmenu.css({'display': 'block'});
        $('#naturalElements-submenu').css({'display': 'none'});
    }
    else {
        livingThingSubmenu.css({'display': 'none'});
    }
});

$('#naturalElements-menu').click(() => {
    let naturalElementsSubmenu = $('#naturalElements-submenu');
    $(this).find('img').css({'transform': 'rotate(0)'});

    if(naturalElementsSubmenu.css('display') == "none") {
        naturalElementsSubmenu.css({'display': 'block'});
        $('#livingThing-submenu').css({'display': 'none'});
    }
    else 
        naturalElementsSubmenu.css({'display': 'none'});
});