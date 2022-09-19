const swiper = new Swiper('.swiper', {
    // Optional parameters
    slidesPerView: 6,
    slidesPerColumn: 2,
    spaceBetween: 30,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
    breakpoints: {
        640: {
          slidesPerView: 1,
          spaceBetween: 20,
        },
        768: {
          slidesPerView: 2,
          spaceBetween: 40,
        },
        1024: {
          slidesPerView: 4,
          spaceBetween: 30,
        },
        1600: {
            slidesPerView: 6,
            spaceBetween: 30,
          },
    },
  });
