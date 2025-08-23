$(document).ready(function () {
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
    $(".security-text").text(t.securityText);
    $(".personal-info-title").text(t.personalInfoTitle);
    $(".payment-info-title").text(t.paymentInfoTitle);
    $(".payment-method-title").text(t.paymentMethodTitle);
    $(".full-name-label").text(t.fullNameLabel);
    $(".email-label").text(t.emailLabel);
    $(".phone-label").text(t.phoneLabel);
    $(".card-method-title").text(t.cardMethodTitle);
    $(".paypal-method-title").text(t.paypalMethodTitle);
    $(".apple-method-title").text(t.appleMethodTitle);
    $(".paypal-desc").text(t.paypalDesc);
    $(".apple-desc").text(t.appleDesc);
    $(".card-number-label").text(t.cardNumberLabel);
    $(".expiry-date-label").text(t.expiryDateLabel);
    $(".cvv-label").text(t.cvvLabel);
    $(".card-holder-name-label").text(t.cardHolderNameLabel);
    $(".card-holder-label").text(t.cardHolderLabel);
    $(".expiry-label").text(t.expiryLabel);
    $(".submit-text").text(t.submitText);
    $(".secure-payment-text").text(t.securePaymentText);
    $(".paypal-connect-text").text(t.paypalConnectText);
    $(".connect-paypal-text").text(t.connectPaypalText);
    $(".apple-pay-text").text(t.applePayText);
    $(".pay-with-apple-text").text(t.payWithAppleText);
    $(".success-title").text(t.successTitle);
    $(".success-message-text").text(t.successMessageText);
    $(".continue-text").text(t.continueText);

    // Update subscription options
    if (currentLang === "en") {
      $('#subscriptionType option[value="basic"]').text("Basic Plan - $5/week");
      $('#subscriptionType option[value="premium"]').text(
        "Premium Plan - $12/week"
      );
      $('#subscriptionType option[value="vip"]').text("VIP Plan - $20/week");
      $('#subscriptionType option[value="ultimate"]').text(
        "Ultimate Plan - $35/week"
      );
      $('#subscriptionType option[value=""]').text("Choose subscription type");
    } else {
      $('#subscriptionType option[value="basic"]').text(
        "الباقة الأساسية - $5/أسبوع"
      );
      $('#subscriptionType option[value="premium"]').text(
        "الباقة المتميزة - $12/أسبوع"
      );
      $('#subscriptionType option[value="vip"]').text("باقة VIP - $20/أسبوع");
      $('#subscriptionType option[value="ultimate"]').text(
        "الباقة المطلقة - $35/أسبوع"
      );
      $('#subscriptionType option[value=""]').text("اختر نوع الاشتراك");
    }
  });

  // Payment Method Selection
  $(".payment-method").click(function () {
    $(".payment-method").removeClass("active");
    $(this).addClass("active");

    const method = $(this).data("method");
    $(this).find('input[type="radio"]').prop("checked", true);

    // Hide all payment forms
    $(".payment-form").removeClass("active");

    // Show selected payment form
    $(`#${method}Form`).addClass("active");

    updateProgress();
  });

  // Card Number Formatting
  $("#cardNumber").on("input", function () {
    let value = $(this)
      .val()
      .replace(/\s/g, "")
      .replace(/[^0-9]/gi, "");
    let formattedValue = value.match(/.{1,4}/g)?.join(" ") || value;

    if (formattedValue !== $(this).val()) {
      $(this).val(formattedValue);
    }

    // Update card preview
    $("#cardPreview").text(formattedValue || "•••• •••• •••• ••••");

    // Validate card number
    validateCardNumber(value);
  });

  // Expiry Date Formatting
  $("#expiryDate").on("input", function () {
    let value = $(this).val().replace(/\D/g, "");
    let formattedValue = value.replace(/(\d{2})(\d)/, "$1/$2");

    if (formattedValue !== $(this).val()) {
      $(this).val(formattedValue);
    }

    // Update preview
    $("#expiryPreview").text(formattedValue || "MM/YY");

    // Validate expiry date
    validateExpiryDate(formattedValue);
  });

  // CVV Input
  $("#cvv").on("input", function () {
    let value = $(this)
      .val()
      .replace(/[^0-9]/g, "");
    $(this).val(value);
    validateCVV(value);
  });

  // Card Holder Name
  $("#cardHolderName").on("input", function () {
    const value = $(this).val();
    $("#cardHolderPreview").text(value || "الاسم الكامل");
    validateCardHolderName(value);
  });

  // Form Validation Functions
  function validateCardNumber(cardNumber) {
    const isValid = cardNumber.length >= 13 && cardNumber.length <= 19;
    toggleError("#cardNumberError", !isValid);
    return isValid;
  }

  function validateExpiryDate(expiry) {
    const regex = /^(0[1-9]|1[0-2])\/\d{2}$/;
    const isValid = regex.test(expiry);

    if (isValid) {
      const [month, year] = expiry.split("/");
      const currentDate = new Date();
      const currentYear = currentDate.getFullYear() % 100;
      const currentMonth = currentDate.getMonth() + 1;

      const isNotExpired =
        parseInt(year) > currentYear ||
        (parseInt(year) === currentYear && parseInt(month) >= currentMonth);

      toggleError("#expiryError", !isNotExpired);
      return isNotExpired;
    }

    toggleError("#expiryError", !isValid);
    return isValid;
  }

  function validateCVV(cvv) {
    const isValid = cvv.length >= 3 && cvv.length <= 4;
    toggleError("#cvvError", !isValid);
    return isValid;
  }

  function validateCardHolderName(name) {
    const isValid = name.trim().length >= 2;
    toggleError("#cardHolderError", !isValid);
    return isValid;
  }

  function validateEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const isValid = regex.test(email);
    toggleError("#emailError", !isValid);
    toggleSuccess("#emailSuccess", isValid);
    return isValid;
  }

  function validatePhone(phone) {
    const isValid = phone.length >= 7;
    toggleError("#phoneError", !isValid);
    return isValid;
  }

  function validateFullName(name) {
    const isValid = name.trim().length >= 2;
    toggleError("#fullNameError", !isValid);
    return isValid;
  }

  function validateSubscription() {
    const isValid = $("#subscriptionType").val() !== "";
    toggleError("#subscriptionError", !isValid);
    return isValid;
  }

  function toggleError(selector, show) {
    if (show) {
      $(selector).show();
      $(selector)
        .closest(".input-group")
        .find(".input-field")
        .addClass("border-red-500");
    } else {
      $(selector).hide();
      $(selector)
        .closest(".input-group")
        .find(".input-field")
        .removeClass("border-red-500");
    }
  }

  function toggleSuccess(selector, show) {
    if (show) {
      $(selector).show();
    } else {
      $(selector).hide();
    }
  }

  // Real-time validation
  $("#fullName").on("blur", function () {
    validateFullName($(this).val());
    updateProgress();
  });

  $("#email").on("blur", function () {
    validateEmail($(this).val());
    updateProgress();
  });

  $("#phoneNumber").on("blur", function () {
    validatePhone($(this).val());
    updateProgress();
  });

  $("#subscriptionType").on("change", function () {
    validateSubscription();
    updateProgress();
  });

  // Progress Bar Update
  function updateProgress() {
    const fields = [
      $("#fullName").val().trim() !== "",
      validateEmail($("#email").val()),
      $("#phoneNumber").val().trim() !== "",
      $("#subscriptionType").val() !== "",
      $('input[name="paymentMethod"]:checked').val() === "card"
        ? $("#cardNumber").val().replace(/\s/g, "").length >= 13 &&
          $("#expiryDate").val().length === 5 &&
          $("#cvv").val().length >= 3 &&
          $("#cardHolderName").val().trim() !== ""
        : true,
    ];

    const completedFields = fields.filter(Boolean).length;
    const progress = (completedFields / fields.length) * 100;

    $("#progressBar").css("width", progress + "%");
  }

  // Submit Payment
  $("#submitPayment").click(function () {
    const isFormValid = validateForm();

    if (!isFormValid) {
      // Scroll to first error
      const firstError = $(".error-message:visible").first();
      if (firstError.length) {
        $("html, body").animate(
          {
            scrollTop: firstError.offset().top - 100,
          },
          500
        );
      }
      return;
    }

    // Show loading
    $("#loadingSpinner").removeClass("hidden");
    $(this).prop("disabled", true);
    $(".submit-text").text(
      currentLang === "ar" ? "جاري المعالجة..." : "Processing..."
    );

    // Simulate payment processing
    setTimeout(() => {
      showSuccessModal();
      $("#loadingSpinner").addClass("hidden");
      $(this).prop("disabled", false);
      $(".submit-text").text(translations[currentLang].submitText);
    }, 3000);
  });

  function validateForm() {
    const fullNameValid = validateFullName($("#fullName").val());
    const emailValid = validateEmail($("#email").val());
    const phoneValid = validatePhone($("#phoneNumber").val());
    const subscriptionValid = validateSubscription();

    const paymentMethod = $('input[name="paymentMethod"]:checked').val();
    let paymentValid = true;

    if (paymentMethod === "card") {
      const cardNumberValid = validateCardNumber(
        $("#cardNumber").val().replace(/\s/g, "")
      );
      const expiryValid = validateExpiryDate($("#expiryDate").val());
      const cvvValid = validateCVV($("#cvv").val());
      const cardHolderValid = validateCardHolderName(
        $("#cardHolderName").val()
      );

      paymentValid =
        cardNumberValid && expiryValid && cvvValid && cardHolderValid;
    }

    return (
      fullNameValid &&
      emailValid &&
      phoneValid &&
      subscriptionValid &&
      paymentValid
    );
  }

  function showSuccessModal() {
    $("#successModal").removeClass("hidden").addClass("flex");
    setTimeout(() => {
      $("#successModal .bg-gray-800")
        .removeClass("scale-95")
        .addClass("scale-100");
    }, 10);
  }

  $("#closeSuccessModal").click(function () {
    $("#successModal .bg-gray-800")
      .removeClass("scale-100")
      .addClass("scale-95");
    setTimeout(() => {
      $("#successModal").removeClass("flex").addClass("hidden");
    }, 300);
  });

  // Initialize animations
  setTimeout(() => {
    updateProgress();
  }, 1000);

  // Auto-select subscription type from URL parameter
  const urlParams = new URLSearchParams(window.location.search);
  const plan = urlParams.get("plan");
  if (plan) {
    $("#subscriptionType").val(plan);
    validateSubscription();
    updateProgress();
  }

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
    `
    )
    .appendTo("head");

  // Auto-focus first input
  setTimeout(() => {
    $("#fullName").focus();
  }, 500);
});
