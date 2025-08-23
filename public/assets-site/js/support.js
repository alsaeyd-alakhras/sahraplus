$(document).ready(function () {
  let currentLang = "ar";

  // Language Toggle Function
  function toggleLanguage() {
    currentLang = currentLang === "ar" ? "en" : "ar";
    const html = $("html");

    if (currentLang === "en") {
      html.attr("lang", "en").attr("dir", "ltr");
      $("#langText").text("العربية");
    } else {
      html.attr("lang", "ar").attr("dir", "rtl");
      $("#langText").text("English");
    }

    // Update all text elements
    $("[data-ar][data-en]").each(function () {
      const $element = $(this);
      if (currentLang === "ar") {
        $element.text($element.data("ar"));
      } else {
        $element.text($element.data("en"));
      }
    });

    // Update placeholders
    $("[data-ar-placeholder][data-en-placeholder]").each(function () {
      const $element = $(this);
      if (currentLang === "ar") {
        $element.attr("placeholder", $element.data("ar-placeholder"));
      } else {
        $element.attr("placeholder", $element.data("en-placeholder"));
      }
    });

    // Update main titles
    if (currentLang === "ar") {
      $("#mainTitle").text("الدعم الفني");
      $("#mainSubtitle").text("نحن هنا لمساعدتك في أي وقت");
      $("#contactTitle").text("تواصل معنا");
      $("#faqTitle").text("الأسئلة الشائعة");
      $("#supportInfoTitle").text("طرق التواصل الأخرى");
      $("#supportLangText").text("لغات الدعم المتاحة:");
    } else {
      $("#mainTitle").text("Technical Support");
      $("#mainSubtitle").text("We are here to help you anytime");
      $("#contactTitle").text("Contact Us");
      $("#faqTitle").text("Frequently Asked Questions");
      $("#supportInfoTitle").text("Other Contact Methods");
      $("#supportLangText").text("Available Support Languages:");
    }
  }

  // Language toggle event
  $("#langToggle").click(function () {
    toggleLanguage();
  });

  // Accordion functionality
  $(".accordion-header").click(function () {
    const target = $(this).data("target");
    const content = $("#" + target);
    const icon = $(this).find("i");

    // Close all other accordions
    $(".accordion-content").not(content).removeClass("active").slideUp(300);
    $(".accordion-header").not(this).find("i").removeClass("rotate-180");

    // Toggle current accordion
    if (content.hasClass("active")) {
      content.removeClass("active").slideUp(300);
      icon.removeClass("rotate-180");
    } else {
      content.addClass("active").slideDown(300);
      icon.addClass("rotate-180");
    }
  });

  // Contact Form Submission
  $("#contactForm").submit(function (e) {
    e.preventDefault();

    // Get form data
    const fullName = $("#fullName").val().trim();
    const email = $("#email").val().trim();
    const subject = $("#subject").val();
    const message = $("#message").val().trim();

    // Basic validation
    if (!fullName || !email || !subject || !message) {
      showMessage(
        "error",
        currentLang === "ar"
          ? "يرجى ملء جميع الحقول المطلوبة"
          : "Please fill in all required fields"
      );
      return;
    }

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      showMessage(
        "error",
        currentLang === "ar"
          ? "يرجى إدخال بريد إلكتروني صحيح"
          : "Please enter a valid email address"
      );
      return;
    }

    // Show loading state
    const submitBtn = $("#submitBtn");
    const btnText = $("#btnText");
    const btnLoader = $("#btnLoader");

    submitBtn.prop("disabled", true);
    btnText.hide();
    btnLoader.show();

    // Simulate form submission
    setTimeout(function () {
      // Reset button state
      submitBtn.prop("disabled", false);
      btnText.show();
      btnLoader.hide();

      // Show success message
      showMessage(
        "success",
        currentLang === "ar"
          ? "تم إرسال رسالتك بنجاح! سنتواصل معك قريباً."
          : "Your message has been sent successfully! We will contact you soon."
      );

      // Reset form
      $("#contactForm")[0].reset();
    }, 2000);
  });

  // Message display function
  function showMessage(type, text) {
    const messageDiv = $("#formMessage");
    messageDiv.removeClass(
      "hidden bg-green-100 bg-red-100 text-green-800 text-red-800 border-green-300 border-red-300"
    );

    if (type === "success") {
      messageDiv.addClass(
        "bg-green-100 text-green-800 border border-green-300"
      );
    } else {
      messageDiv.addClass("bg-red-100 text-red-800 border border-red-300");
    }

    messageDiv.text(text).fadeIn(300);

    // Hide message after 5 seconds
    setTimeout(function () {
      messageDiv.fadeOut(300);
    }, 5000);
  }

  // Smooth scrolling for anchor links
  $('a[href^="#"]').click(function (e) {
    e.preventDefault();
    const target = $($(this).attr("href"));
    if (target.length) {
      $("html, body").animate(
        {
          scrollTop: target.offset().top - 80,
        },
        800
      );
    }
  });

  // Add hover effects for interactive elements
  $(".bg-\\[\\#1f232b\\]").hover(
    function () {
      $(this).addClass("shadow-lg transform scale-105");
    },
    function () {
      $(this).removeClass("shadow-lg transform scale-105");
    }
  );

  // Initialize tooltips for better UX
  $("[title]").each(function () {
    $(this).tooltip();
  });

  // Add loading animation to external links
  $('a[target="_blank"]').click(function () {
    const link = $(this);
    const originalText = link.html();
    link.html('<i class="mr-2 fas fa-spinner fa-spin"></i>' + originalText);

    setTimeout(function () {
      link.html(originalText);
    }, 1000);
  });

  // Add floating effect to contact cards
  $(".support-info .bg-\\[\\#1f232b\\]").hover(
    function () {
      $(this).addClass("transform -translate-y-2 shadow-2xl");
    },
    function () {
      $(this).removeClass("transform -translate-y-2 shadow-2xl");
    }
  );

  // Auto-resize textarea
  $("textarea").on("input", function () {
    this.style.height = "auto";
    this.style.height = this.scrollHeight + "px";
  });

  // Add focus effects to form inputs
  $("input, select, textarea")
    .focus(function () {
      $(this).parent().addClass("ring-2 ring-sky-500");
    })
    .blur(function () {
      $(this).parent().removeClass("ring-2 ring-sky-500");
    });

  // Keyboard navigation for accordion
  $(".accordion-header").keydown(function (e) {
    if (e.key === "Enter" || e.key === " ") {
      e.preventDefault();
      $(this).click();
    }
  });

  // Initialize page with default language
  if (currentLang === "ar") {
    $("html").attr("lang", "ar").attr("dir", "rtl");
  }
});
