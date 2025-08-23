$(document).ready(function () {
  // Initialize Swiper
  const swiper = new Swiper(".worksSwiper", {
    slidesPerView: 1,
    spaceBetween: 20,
    loop: true,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    breakpoints: {
      640: {
        slidesPerView: 2,
      },
      768: {
        slidesPerView: 3,
      },
      1024: {
        slidesPerView: 4,
      },
    },
  });

  // Biography toggle
  $("#toggleBio").click(function () {
    const $moreText = $("#bioMore");
    const $button = $(this);
    const $icon = $button.find("i");

    if ($moreText.hasClass("hidden")) {
      $moreText.removeClass("hidden");
      $icon.removeClass("fa-chevron-down").addClass("fa-chevron-up");
      $button
        .find("span")
        .text($("html").attr("dir") === "rtl" ? "اقرأ أقل" : "Read less");
    } else {
      $moreText.addClass("hidden");
      $icon.removeClass("fa-chevron-up").addClass("fa-chevron-down");
      $button
        .find("span")
        .text($("html").attr("dir") === "rtl" ? "اقرأ المزيد" : "Read more");
    }
  });

  // Filter works
  $(".filter-btn").click(function () {
    const filter = $(this).data("filter");

    // Update active button
    $(".filter-btn").removeClass("active bg-sky-500").addClass("bg-[#2a2d35]");
    $(this).addClass("active bg-sky-500").removeClass("bg-[#2a2d35]");

    // Filter slides
    if (filter === "all") {
      $(".swiper-slide").show();
    } else {
      $(".swiper-slide").hide();
      $(`.swiper-slide .work-card[data-type="${filter}"]`).parent().show();
    }

    swiper.update();
  });

  // Language toggle
  $("#langToggle").click(function () {
    const currentLang = $("html").attr("lang");
    const currentDir = $("html").attr("dir");

    if (currentLang === "ar") {
      // Switch to English
      $("html").attr("lang", "en").attr("dir", "ltr");
      $("#langText").text("العربية");

      // Update all data-en elements
      $("[data-en]").each(function () {
        const $this = $(this);
        const arabicText = $this.text();
        const englishText = $this.attr("data-en");
        $this.attr("data-ar", arabicText).text(englishText);
      });

      // Update title
      document.title = "Ahmed Zaki - Profile";

      // Update biography toggle text
      const $bioButton = $("#toggleBio span");
      if ($("#bioMore").hasClass("hidden")) {
        $bioButton.text("Read more");
      } else {
        $bioButton.text("Read less");
      }
    } else {
      // Switch to Arabic
      $("html").attr("lang", "ar").attr("dir", "rtl");
      $("#langText").text("English");

      // Update all data-ar elements
      $("[data-ar]").each(function () {
        const $this = $(this);
        const englishText = $this.text();
        const arabicText = $this.attr("data-ar");
        $this.attr("data-en", englishText).text(arabicText);
      });

      // Update title
      document.title = "أحمد زكي - الملف الشخصي";

      // Update biography toggle text
      const $bioButton = $("#toggleBio span");
      if ($("#bioMore").hasClass("hidden")) {
        $bioButton.text("اقرأ المزيد");
      } else {
        $bioButton.text("اقرأ أقل");
      }
    }

    // Update Swiper direction
    swiper.changeLanguageDirection();
    swiper.update();
  });

  // Smooth scrolling for internal links
  $('a[href^="#"]').click(function (e) {
    e.preventDefault();
    const target = $($(this).attr("href"));
    if (target.length) {
      $("html, body").animate(
        {
          scrollTop: target.offset().top - 100,
        },
        800
      );
    }
  });
  // Add hover effects to social media icons
  $(".actor-header a").hover(
    function () {
      $(this).addClass("transform scale-110");
    },
    function () {
      $(this).removeClass("transform scale-110");
    }
  );

  // Animate cards on scroll
  function animateOnScroll() {
    $(".work-card").each(function () {
      const elementTop = $(this).offset().top;
      const elementBottom = elementTop + $(this).outerHeight();
      const viewportTop = $(window).scrollTop();
      const viewportBottom = viewportTop + $(window).height();

      if (elementBottom > viewportTop && elementTop < viewportBottom) {
        $(this).addClass("animate-fade-in");
      }
    });
  }

  $(window).scroll(animateOnScroll);
  animateOnScroll(); // Initial check

  // Add CSS animation class
  $("<style>")
    .prop("type", "text/css")
    .html(
      `
            .animate-fade-in {
                animation: fadeInUp 0.6s ease-out forwards;
            }
            
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .toast {
                animation: slideInRight 0.3s ease-out;
            }
            
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `
    )
    .appendTo("head");
});
