const maxProfiles = 5;
let profiles = [];
let profile = {};

$(document).ready(() => {
    if (auth_user_check) {
        loadProfiles();
    }
});

// تحميل الملفات من الباك
function loadProfiles() {
    $.ajax({
        url: urlIndex,
        type: "GET",
        success: function (response) {
            profiles = response.profiles;
            const activeProfileId = localStorage.getItem("active_profile_id");
            const activeProfile = profiles.find((p) => p.id == activeProfileId);

            // لو الملف موجود فعلًا، فعّله، غير هيك اعرض نافذة اختيار الملفات
            if (activeProfile && auth_user_check) {
                profile = activeProfile;
                $("#profile-name").text(profile.name);
                $(".profile-img").attr("src", "/storage/" + profile.avatar_url);
            } else {
                localStorage.removeItem("active_profile_id");
                $("#profileModal").removeClass("hidden");
            }

            if (!profile.id && auth_user_check) {
                if (profiles.length > 0) {
                    $("#profileModal").removeClass("hidden");
                } else {
                    openEdit(null);
                }
            }
            renderProfiles();
            renderManageList();
        },
        error: function (xhr) {
            const msg = xhr.responseJSON?.message || "فشل في تحميل الملفات";
            toastr.error(msg, "خطأ!");
        },
    });
}

function renderProfiles() {
    const container = $("#profileList");
    container.empty();
    profiles.forEach((profile) => {
        let img = profile.avatar_url && profile.avatar_url.startsWith('uploads') ? `/storage/${profile.avatar_url}` : profile.avatar_url ? profile.avatar_url : avatarImg;
        container.append(`
      <div class="cursor-pointer group" onclick="switchProfile(${profile.id})">
        <img src="${img}" class="w-44 h-44 rounded-full border-4 border-transparent transition group-hover:border-sky-500 group-hover:scale-110" />
        <p class="mt-2 font-semibold">${profile.name}</p>
      </div>
    `);
    });
    if (profiles.length == 0) {
        container.append(`
      <div class="flex flex-col justify-center items-center w-44 h-44 rounded-full border-2 border-gray-500 border-dashed cursor-pointer" onclick="openEdit(null)">
        <span class="text-4xl">+</span>
        <p class="mt-1 text-sm">إضافة</p>
      </div>
    `);
    }
}

function renderManageList() {
    const manage = $("#manageList");
    manage.empty();

    profiles.forEach((p, i) => {
        let img = p.avatar_url && p.avatar_url.startsWith('uploads') ? `/storage/${p.avatar_url}` : p.avatar_url ? p.avatar_url : avatarImg;
        manage.append(`
            <div class="relative cursor-pointer group" onclick="openEdit(${i})">
                <img src="${img}" class="mx-auto w-44 h-44 rounded-full">
                <p class="mt-2 text-center">${p.name}</p>
                <div class="flex absolute inset-0 justify-center items-center w-44 h-44 bg-black bg-opacity-50 rounded-full opacity-0 transition group-hover:opacity-100">
                    <i class="text-white fas fa-pen"></i>
                </div>
            </div>
        `);
    });

    if (profiles.length < maxProfiles) {
        manage.append(`
            <div class="flex flex-col justify-center items-center w-44 h-44 rounded-full border-2 border-gray-500 border-dashed cursor-pointer" onclick="openEdit(null)">
                <span class="text-4xl">+</span>
                <p class="mt-1 text-sm">إضافة</p>
            </div>
        `);
    }
}

function openEdit(index) {
    const p = profiles[index] || {
        id: null,
        name: "",
        avatar_url: avatarImg,
        language: "ar",
        is_child_profile: false,
        pin_code: "",
    };
    profile = p;

    $("#editModal").removeClass("hidden");
    $("#editModalTitle").text(
        index != null ? `تعديل ملف : ${p.name}` : "إضافة ملف"
    );
    $("#editSaveBtn").text(index != null ? "تعديل" : "إضافة");
    $("#editDeleteBtn").toggleClass("hidden", index == null);
    $("#editCloseBtn").toggleClass("hidden", index == null);
    $("#editName").val(p.name);
    $("#editLang").val(p.language);
    let img = profile.avatar_url && profile.avatar_url.startsWith('uploads') ? `/storage/${profile.avatar_url}` : profile.avatar_url ? profile.avatar_url : avatarImg;
    $("#editAvatarImg").attr("src", img);
    $("#editKids").prop("checked", p.is_child_profile);
    if (require_pin_for_children && p.is_child_profile) {
        $("#editPin").attr("required", true);
    } else {
        $("#editPin").removeAttr("required");
    }
    $("#editModal").data("index", index);
}
$("#editKids").on("change", function () {
    if (require_pin_for_children && $(this).prop("checked")) {
        $("#editPin").attr("required", true);
    } else {
        $("#editPin").removeAttr("required");
    }
});

function selectAvatar(src) {
    $("#editAvatarImg").attr("src", src);
    $("#avatarPickerModal").addClass("hidden");
    //   toastr.success("تم الحفظ بنجاح", "نجاح!");
}

function saveProfile() {
    const index = $("#editModal").data("index");
    const id = profile.id;

    const data = {
        name: $("#editName").val(),
        avatar_url: $("#editAvatarImg").attr("src").replace("/storage/", ""),
        language: $("#editLang").val(),
        is_child_profile: $("#editKids").prop("checked") ? 1 : 0,
        pin_code: $("#editPin").val(),
    };
    const pin = $("#editPin").val();
    if (pin && pin.length !== 6) {
        toastr.error("الـ PIN يجب أن يكون مكون من 6 أرقام", "خطأ!");
        return;
    }

    if (
        require_pin_for_children &&
        $("#editKids").prop("checked") &&
        !$("#editPin").val()
    ) {
        toastr.error("ملفات الأطفال يجب أن تحتوي على PIN", "خطأ!");
        return;
    }

    // تحقق قبل الإرسال لو الملف طفل
    if (profile.is_child_profile) {
        Swal.fire({
            title: "التحقق مطلوب",
            html: `
                <input type="password" id="verifyPin" class="swal2-input" placeholder="PIN (اختياري)">
                <input type="password" id="verifyPass" class="swal2-input" placeholder="كلمة مرور الحساب (اختياري)">
            `,
            preConfirm: () => {
                data.pin_code = $("#verifyPin").val();
                data.password = $("#verifyPass").val();
                if (!data.pin_code && !data.password) {
                    Swal.showValidationMessage(
                        "أدخل PIN أو كلمة مرور المستخدم"
                    );
                    return false;
                }
                return data;
            },
        }).then((result) => {
            if (result.isConfirmed) {
                submitProfileForm(id, index, data);
            }
        });
    } else {
        submitProfileForm(id, index, data);
    }
}

function submitProfileForm(id, index, data) {
    const method = id ? "PUT" : "POST";
    const url = id ? `${urlUpdate.replace(":id", id)}` : urlStore;

    $.ajax({
        url: url,
        type: method,
        data: data,
        headers: {
            "X-CSRF-TOKEN": _token,
        },
        success: function (response) {
            let p = response.profile;
            if (profiles.length == 0) {
                profile = p;
                switchProfile(p.id);
            }
            loadProfiles();
            closeModal("editModal");
            toastr.success("تم الحفظ بنجاح", "نجاح!");
        },
        error: function (xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = xhr.responseJSON.errors;
                for (const field in errors) {
                    if (errors.hasOwnProperty(field)) {
                        errors[field].forEach((msg) => {
                            toastr.error(msg, "خطأ في الإدخال");
                        });
                    }
                }
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                toastr.error(xhr.responseJSON.message, "خطأ!");
            } else {طيب
                toastr.error("حدث خطأ غير متوقع", "فشل!");
            }
        },
    });
}

function deleteProfile() {
    const index = $("#editModal").data("index");
    const id = profiles[index]?.id;
    if (!id) return;

    $.ajax({
        url: `${urlDestroy.replace(":id", id)}`,
        type: "DELETE",
        success: () => {
            // إذا الملف الحالي هو المحذوف، نظف الحالة واطلب من المستخدم اختيار ملف جديد
            const activeProfileId = localStorage.getItem("active_profile_id");

            if (activeProfileId == id) {
                localStorage.removeItem("active_profile_id");
                profile = {};
                $("#profile-name").text("...");
                $(".profile-img").attr("src", avatarImg); // الصورة الافتراضية
                setTimeout(() => {
                    $("#profileModal").removeClass("hidden");
                }, 200);
            }

            loadProfiles();
            closeModal("editModal");
            toastr.success("تم الحذف بنجاح", "نجاح!");
        },
        error: (xhr) => {
            if (xhr.responseJSON?.message) {
                toastr.error(xhr.responseJSON.message, "خطأ في الحذف");
            } else {
                toastr.error("فشل في الحذف", "خطأ!");
            }
        },
    });
}

// الأحداث
$("#openProfileModal").on("click", () => {
    renderProfiles();
    $("#profileModal").removeClass("hidden");
});

function closeModal(id) {
    $(`#${id}`).addClass("hidden");
}

function switchProfile(profileId) {
    const p = profiles.find((x) => x.id == profileId);
    if (!p) return;

    if (profile.id == p.id) {
        toastr.info("الملف محدد مسبقًا", "معلومة!");
        closeModal("profileModal");
        return;
    }

    if (p.is_child_profile) {
        Swal.fire({
            title: "أدخل كود PIN",
            input: "password",
            confirmButtonText: "تأكيد",
            showCancelButton: true,
            preConfirm: (pin) => {
                return $.ajax({
                    url: `${urlVerifyPin.replace(":id", p.id)}`,
                    type: "POST",
                    data: { pin_code: pin },
                    headers: {
                        "X-CSRF-TOKEN": _token,
                    },
                    success: (res) => {
                        if (!res.valid) {
                            throw new Error("كود PIN غير صحيح");
                        }
                        return true;
                    },
                    error: (xhr) => {
                        const msg = xhr.responseJSON?.message || "فشل في التحقق";
                        Swal.showValidationMessage(msg);
                    },
                });
            },
            footer: `<button class="swal2-confirm swal2-styled" id="forgotPinBtn">هل نسيت PIN؟</button>`,
            didOpen: () => {
                $("#forgotPinBtn").on("click", () => {
                    Swal.close();
                    openResetPinModal(p.id);
                });
            },
        }).then((result) => {
            if (result.isConfirmed) {
                activateProfile(p);
            }
        });
    } else {
        activateProfile(p);
    }
}

function activateProfile(p) {
    profile = p;
    localStorage.setItem("active_profile_id", profile.id);
    $("#profile-name").text(profile.name);
    let img = profile.avatar_url && profile.avatar_url.startsWith('uploads') ? `/storage/${profile.avatar_url}` : profile.avatar_url ? profile.avatar_url : avatarImg;
    $(".profile-img").attr("src", img);
    closeModal("profileModal");
    toastr.success("تم التبديل بنجاح", "نجاح!");
}

function openResetPinModal(profileId) {
    Swal.fire({
        title: "إعادة تعيين PIN",
        html: `
            <input type="password" id="accountPassword" class="swal2-input" placeholder="كلمة مرور الحساب">
            <input type="password" id="newPin" class="swal2-input" placeholder="PIN جديد">
        `,
        confirmButtonText: "تحديث",
        showCancelButton: true,
        preConfirm: () => {
            const password = $("#accountPassword").val();
            const newPin = $("#newPin").val();

            if (!password || !newPin) {
                Swal.showValidationMessage("أدخل جميع الحقول");
                return false;
            }

            return $.ajax({
                url: urlResetPin.replace(":id", profileId),
                type: "POST",
                data: { password, new_pin: newPin },
                headers: {
                    "X-CSRF-TOKEN": _token,
                },
            })
                .then((res) => {
                    toastr.success(res.message, "تم");
                    return true;
                })
                .catch((xhr) => {
                    const msg = xhr.responseJSON?.message || "فشل في التحديث";
                    Swal.showValidationMessage(msg);
                });
        },
    });
}

$("#manageProfilesBtn").on("click", () => {
    closeModal("profileModal");
    $("#manageModal").removeClass("hidden");
    renderManageList();
});
$("#openManageModal").on("click", () => {
    $("#manageModal").removeClass("hidden");
    renderManageList();
});
