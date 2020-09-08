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
})