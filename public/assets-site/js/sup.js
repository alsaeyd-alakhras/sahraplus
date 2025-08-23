$(document).ready(function () {
  // Language Toggle Functionality
  let currentLang = "ar";

  $("#langToggle").click(function () {
    currentLang = currentLang === "ar" ? "en" : "ar";
    const isRTL = currentLang === "ar";

    // Update HTML attributes
    $("html")
      .attr("lang", currentLang)
      .attr("dir", isRTL ? "rtl" : "ltr");
    $("body").toggleClass("font-arabic font-english");

    // Update content
    const t = translations[currentLang];
    $(".lang-text").text(t.langText);
    $(".main-title").text(t.mainTitle);
    $(".main-subtitle").text(t.mainSubtitle);
    $(".popular-text").text(t.popularText);
    $(".additional-info").text(t.additionalInfo);
    $(".secure-text").text(t.secureText);
    $(".payment-text").text(t.paymentText);
    $(".support-text").text(t.supportText);
    $(".modal-title").text(t.modalTitle);
    $(".modal-message").text(t.modalMessage);
    $(".confirm-text").text(t.confirmText);
    $(".cancel-text").text(t.cancelText);

    // Update plan details
    $(".plan-name").each(function (index) {
      $(this).text(t.plans[index].name);
    });
    $(".price").each(function (index) {
      $(this).text(t.plans[index].price);
    });
    $(".period").each(function (index) {
      $(this).text(t.plans[index].period);
    });
    $(".plan-desc").each(function (index) {
      $(this).text(t.plans[index].desc);
    });
    $(".btn-text").each(function (index) {
      $(this).text(t.plans[index].btnText);
    });

    // Add smooth transition effect
    $("body").addClass("transition-all duration-300");
  });

  // Subscription Modal Functionality
  let selectedPlan = "";

  $(".subscribe-btn").click(function () {
    const plan = $(this).data("plan");
    const planName = $(this)
      .closest(".subscription-card")
      .find(".plan-name")
      .text();
    selectedPlan = plan;

    $("#selectedPlan").text(planName);
    $("#subscriptionModal").removeClass("hidden").addClass("flex");

    // Animate modal
    setTimeout(() => {
      $("#subscriptionModal .bg-slate-800")
        .removeClass("scale-95")
        .addClass("scale-100");
    }, 10);

    // Add pulse effect to the selected card
    $(this).closest(".subscription-card").addClass("card-glow");
  });

  $("#cancelSubscription, #subscriptionModal").click(function (e) {
    if (e.target === this) {
      closeModal();
    }
  });

  $("#confirmSubscription").click(function () {
    // Simulate subscription process
    $(this).html(
      '<i class="mr-2 fas fa-spinner fa-spin"></i> جاري المعالجة...'
    );

    setTimeout(() => {
      // Success animation
      $(this)
        .html('<i class="mr-2 fas fa-check"></i> سيتم الإنتقال الآن للدفع!')
        .addClass("bg-green-500 hover:bg-green-600");

      setTimeout(() => {
        closeModal();
        window.location.href = paymentUrl;
      }, 1500);
    }, 2000);
  });

  function closeModal() {
    $("#subscriptionModal .bg-slate-800")
      .removeClass("scale-100")
      .addClass("scale-95");

    setTimeout(() => {
      $("#subscriptionModal").removeClass("flex").addClass("hidden");
      $(".subscription-card").removeClass("card-glow");
      $("#confirmSubscription")
        .html(
          '<i class="mr-2 fas fa-credit-card"></i> <span class="confirm-text">تأكيد الاشتراك</span>'
        )
        .removeClass("bg-green-500 hover:bg-green-600");
    }, 300);
  }

  function showSuccessMessage() {
    // Create success notification
    const successMsg = $(`
            <div class="fixed top-4 right-4 z-50 px-6 py-4 text-white bg-green-500 rounded-lg shadow-lg transition-transform duration-300 transform translate-x-full">
                <div class="flex items-center">
                    <i class="mr-3 fas fa-check-circle"></i>
                    <span>تم الاشتراك بنجاح! مرحباً بك في عائلتنا</span>
                </div>
            </div>
        `);

    $("body").append(successMsg);

    setTimeout(() => {
      successMsg.removeClass("translate-x-full");
    }, 100);

    setTimeout(() => {
      successMsg.addClass("translate-x-full");
      setTimeout(() => successMsg.remove(), 300);
    }, 4000);
  }

  // Card hover effects
  $(".subscription-card").hover(
    function () {
      $(this)
        .find(".feature-item")
        .each(function (index) {
          setTimeout(() => {
            $(this).addClass("text-pink-300");
          }, index * 50);
        });
    },
    function () {
      $(this).find(".feature-item").removeClass("text-pink-300");
    }
  );

  // Scroll animations
  $(window).scroll(function () {
    const scrollTop = $(window).scrollTop();
    const windowHeight = $(window).height();

    $(".slide-in").each(function () {
      const elementTop = $(this).offset().top;
      if (scrollTop + windowHeight > elementTop + 100) {
        $(this)
          .addClass("opacity-100 translate-y-0")
          .removeClass("opacity-0 translate-y-8");
      }
    });
  });

  // Initialize scroll animations
  $(".slide-in").addClass(
    "opacity-0 translate-y-8 transition-all duration-700"
  );

  // Parallax effect for background
  $(window).scroll(function () {
    const scrolled = $(window).scrollTop();
    const parallax = scrolled * 0.5;
    $("body").css("background-position", `center ${parallax}px`);
  });

  // Price animation on hover
  $(".subscription-card").hover(
    function () {
      $(this).find(".price").addClass("animate-pulse");
    },
    function () {
      $(this).find(".price").removeClass("animate-pulse");
    }
  );

  // Feature tooltip improvements
  $(".tooltip").hover(
    function () {
      $(this).addClass("scale-110 text-pink-400");
    },
    function () {
      $(this).removeClass("scale-110 text-pink-400");
    }
  );

  // Keyboard navigation
  $(document).keydown(function (e) {
    if (e.key === "Escape" && $("#subscriptionModal").hasClass("flex")) {
      closeModal();
    }
  });

  // Add ripple effect to buttons
  $(".btn-primary").click(function (e) {
    const button = $(this);
    const ripple = $('<span class="ripple"></span>');
    const rect = this.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = e.clientX - rect.left - size / 2;
    const y = e.clientY - rect.top - size / 2;

    ripple.css({
      width: size,
      height: size,
      left: x,
      top: y,
      position: "absolute",
      borderRadius: "50%",
      background: "rgba(255, 255, 255, 0.3)",
      transform: "scale(0)",
      animation: "ripple-animation 0.6s linear",
      pointerEvents: "none",
    });

    button.css("position", "relative").css("overflow", "hidden").append(ripple);

    setTimeout(() => ripple.remove(), 600);
  });

  // Add CSS for ripple animation
  $("<style>")
    .prop("type", "text/css")
    .html(
      `
        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        
        .subscription-card {
            transform-style: preserve-3d;
        }
        
        .subscription-card:hover {
            transform: translateY(-8px) rotateX(5deg) rotateY(5deg);
        }
        
        .feature-item {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        [dir="rtl"] .subscription-card:hover {
            transform: translateY(-8px) rotateX(5deg) rotateY(-5deg);
        }
    `
    )
    .appendTo("head");

  // Add loading animation for page
  $(window).on("load", function () {
    $(".slide-in").each(function (index) {
      setTimeout(() => {
        $(this).removeClass("opacity-0 translate-y-8");
      }, index * 200);
    });
  });

  // Mobile touch improvements
  if ("ontouchstart" in window) {
    $(".subscription-card")
      .on("touchstart", function () {
        $(this).addClass("scale-105");
      })
      .on("touchend", function () {
        $(this).removeClass("scale-105");
      });
  }

  // Auto-trigger animations on load
  setTimeout(() => {
    $(".slide-in").each(function (index) {
      setTimeout(() => {
        $(this)
          .removeClass("opacity-0 translate-y-8")
          .addClass("opacity-100 translate-y-0");
      }, index * 150);
    });
  }, 100);
});
