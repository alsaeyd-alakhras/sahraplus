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

$(document).ready(function () {
    let subIndex = $("#sub-rows .sub-row").length
        ? $("#sub-rows .sub-row").length - 1
        : 0;
    let planAccessIndex = $("#planAccess-rows .planAccess-r-row").length
        ? $("#planAccess-rows .planAccess-r-row").length - 1
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

    function loadContents(type, index, selectedId = null) {
        let contentSelect = $('select[name="planAccess[' + index + '][content_id]"]');
        contentSelect.empty().append('<option value="">' + loadingMessage + '</option>');
        if (type) {
            $.ajax({
                url: urlGetContents,
                type: 'GET',
                data: {
                    type: type
                },
                success: function (data) {
                    contentSelect.empty().append(
                        '<option value="">' + successMessage + '</option>');
                    $.each(data, function (key, value) {
                        let selected = selectedId && selectedId == value.id ?
                            'selected' : '';
                        contentSelect.append('<option value="' + value.id + '" ' +
                            selected + '>' + value.name + '</option>');
                    });
                },
                error: function () {
                    contentSelect.empty().append(
                        '<option value="">' + errorMessage + '</option>');
                }
            });
        } else {
            contentSelect.empty().append('<option value="">' + successMessage + '</option>');
        }
    }

    $(document).on('change', '.content_type_select', function () {
        let type = $(this).val();
        let index = $(this).data('index');
        loadContents(type, index);
    });

    let rows = $('#planAccess-rows').find('.planAccess-r-row');
    rows.each(function () {
        let index = $(this).data('index');
        let initialType = $(this).find('.content_type_select').val();
        let initialContentId = $(this).find('select[name="planAccess[' + index + '][content_id]"]').data('value');
        if (initialType) {
            loadContents(initialType, index, initialContentId);
        }
    });
});