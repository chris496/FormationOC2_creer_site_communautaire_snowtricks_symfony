const swiper = new Swiper(".swiper", {
  // Optional parameters
  slidesPerView: 6,
  slidesPerColumn: 2,
  spaceBetween: 30,
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
  breakpoints: {
    0: {
      slidesPerView: 1,
      spaceBetween: 10,
    },
    768: {
      slidesPerView: 3,
      spaceBetween: 10,
    },
    1024: {
      slidesPerView: 4,
      spaceBetween: 10,
    },
    1600: {
      slidesPerView: 5,
      spaceBetween: 30,
    },
  },
});
