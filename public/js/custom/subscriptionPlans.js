document.addEventListener("DOMContentLoaded", function () {
    const isCustomizeCheckbox = document.getElementById("is_customize");
    const countryPriceSection = document.getElementById("countryPrice-section");

    function toggleSection() {
        if (isCustomizeCheckbox.checked) {
            countryPriceSection.classList.remove("d-none");
            countryPriceSection
                .querySelectorAll("input, select, textarea")
                .forEach((el) => (el.disabled = false));
        } else {
            countryPriceSection.classList.add("d-none");
            countryPriceSection
                .querySelectorAll("input, select, textarea")
                .forEach((el) => (el.disabled = true));
        }
    }

    toggleSection(); // تنفيذ عند تحميل الصفحة
    isCustomizeCheckbox.addEventListener("change", toggleSection);
});

let subIndex = $("#sub-rows .sub-row").length
    ? $("#sub-rows .sub-row").length - 1
    : 0;
let planAccessIndex = $("#planAccess-rows .planAccess-row").length
    ? $("#planAccess-rows .planAccess-row").length - 1
    : 0;
$("#add-sub-row").on("click", function () {
    subIndex++;
    $.get(
        subtitleRowPartial,
        {
            i: subIndex,
        },
        function (html) {
            const $newRow = $(html);
            $("#sub-rows").append($newRow);
            initSubRow($newRow);
        }
    );
});
$("#add-planAccess-row").on("click", function () {
    planAccessIndex++;
    $.get(
        planAccessRowPartial,
        {
            i: planAccessIndex,
        },
        function (html) {
            const $newRow = $(html);
            $("#planAccess-rows").append($newRow);
        }
    );
});
$(document).on('click', '.remove-planAccess-row', function () {
    $(this).closest('.planAccess-r-row').remove();
    planAccessIndex--;
});
$(document).on('click', '.remove-country-row', function () {
    $(this).closest('.country-row').remove();
    subIndex--;
});
