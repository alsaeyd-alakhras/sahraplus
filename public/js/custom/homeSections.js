$(document).ready(function () {
    let sectionItemIndex = $("#sectionItems-rows .sectionItem-r-row").length
        ? $("#sectionItems-rows .sectionItem-r-row").length - 1
        : 0;

    $("#add-sectionItem-row").on("click", function () {
        sectionItemIndex++;
        $.get(
            sectionItemRowPartial,
            {
                i: sectionItemIndex,
            },
            function (html) {
                const $newRow = $(html);
                $("#sectionItems-rows").append($newRow);
            }
        );
    });

    $(document).on('click', '.remove-sectionItem-row', function () {
        $(this).closest('.sectionItem-r-row').remove();
        sectionItemIndex--;
    });

    function loadContents(type, index, selectedId = null) {
        let contentSelect = $('select[name="sectionItems[' + index + '][content_id]"]');
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

    let rows = $('#sectionItems-rows').find('.sectionItem-r-row');
    rows.each(function () {
        let index = $(this).data('index');
        let initialType = $(this).find('.content_type_select').val();
        let initialContentId = $(this).find('select[name="sectionItems[' + index + '][content_id]"]').data('value');
        if (initialType) {
            loadContents(initialType, index, initialContentId);
        }
    });
});

