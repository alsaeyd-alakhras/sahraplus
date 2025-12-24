$(document).ready(function () {

    let imgInput = "";
    let imgView = "";
    let clearBtn = "";
    let mode = "";
    let outInput = "";
    let SelectedImage = [];
    $(document).on("click", "#openMediaModalBtn, .openMediaModal", function () {
        mode = $(this).data("mode");
        imgInput = $(this).data("input");
        imgView = $(this).data("img");
        clearBtn = $(this).data("clear-btn");
        outInput = $(this).data("out-input");
        loadMedia();

        if(mode == "single"){
            $('#selectMediaBtn').hide();
        }else{
            $('#selectMediaBtn').show();
        }
    });

    // رفع صورة
    $(document).on("click", "#uploadFormBtn", function (e) {
        e.preventDefault();

        const $form = $(this).closest("form");
        const formEl = $form[0];
    
        const fileInput = $form.find('#imageInputMedia')[0];
    
        if (!fileInput || !fileInput.files.length) {
            alert("من فضلك اختر صورة قبل الرفع.");
            return;
        }

        const formData = new FormData(formEl);
        formData.append("_token", _token);
        formData.append("image", fileInput.files[0]);

        $.ajax({
            url: urlStore,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function () {
                $(fileInput).val("");
                loadMedia();
            },
            error: function (xhr) {
                console.error(xhr.responseText || xhr.statusText);
                alert("تعذّر رفع الصورة.");
            },
        });
    });

    // جلب الصور
    function loadMedia() {
        $.get(urlIndex, function (data) {
            let html = "";
            data.forEach((item) => {
                html += `
                <div class="overflow-hidden masonry-item position-relative" data-path="${item.file_path}">
                        <img src="${urlAssetPath}storage/${item.file_path}" class="img-fluid media-image">
                        <div class="top-0 p-2 media-actions position-absolute" style="display: none;">
                            <button type="button" class="border btn btn-sm btn-light rounded-circle me-1 delete-image-btn" data-id="${item.id}" title="حذف">
                                <i class="fas fa-trash text-danger"></i>
                            </button>
                        </div>
                        <div class="p-2 text-center info">
                            <small>${item.name}</small>
                        </div>
                </div>
            `;
            });
            $("#mediaGrid").html(html);
        });
    }

    let deleteId = null;

    $(document).on("click", ".delete-image-btn", function () {
        deleteId = $(this).data("id");

        // افتح المودال بضغط الزر
        $("#confirmDeleteModal").modal('show');
    });

    $("#confirmDeleteBtn").click(function () {
        if (deleteId) {
            $.ajax({
                url: urlDelete.replace(":id", deleteId),
                method: "DELETE",
                data: {
                    _token: _token,
                },
                success: function () {
                    $("#closeDeleteModal").click();
                    loadMedia();
                },
            });
        }
    });
    // اختيار الصورة
    $(document).on("click", ".masonry-item", function () {
        let path = $(this).data("path");
        if(mode == "single"){
            $("#mediaModal").modal('hide');
            $(imgView).attr("src",urlAssetPath + "storage/" + path);
            $(imgInput).val(path);
            $(clearBtn).removeClass("d-none");
            $(imgView).removeClass("d-none");
            $(outInput).val('');
        }else{
            SelectedImage.push(path);
        }
    });

    $(document).on("click", ".clear-btn", function () {
        $(imgView).attr('src','');
        $(imgInput).val('');
        $(clearBtn).addClass("d-none");
        $(imgView).addClass("d-none");
        $(outInput).val('');
    });
});