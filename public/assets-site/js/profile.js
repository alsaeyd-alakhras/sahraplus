const maxProfiles = 5;

function renderProfiles() {
  const container = $("#profileList");
  container.empty();
  profiles.forEach((profile) => {
    container.append(`
      <div class="cursor-pointer group" onclick="switchProfile('${profile.name}','${profile.img}')">
        <img src="${profile.img}" class="w-44 h-44 rounded-full border-4 border-transparent transition group-hover:border-sky-500 group-hover:scale-110" />
        <p class="mt-2 font-semibold">${profile.name}</p>
      </div>
    `);
  });
}

function renderManageList() {
  const manage = $("#manageList");
  manage.empty();
  profiles.forEach((p, i) => {
    manage.append(`
      <div class="relative cursor-pointer group" onclick="openEdit(${i})">
        <img src="${p.img}" class="mx-auto w-44 h-44 rounded-full">
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
    name: "",
    img: "https://placehold.co/100x100",
    lang: "ar",
    kids: false,
  };
  $("#editModal").removeClass("hidden");
  $("#editName").val(p.name);
  $("#editLang").val(p.lang || "ar");
  $("#editAvatarImg").attr("src", p.img);
  $("#editKids").prop("checked", p.kids || false);
  $("#ageSelector").toggleClass("hidden", !p.kids);
  $("#editBirth").val(p.age || 2013);
  $("#editModal").data("index", index);
}

function selectAvatar(src) {
  $("#editAvatarImg").attr("src", src);
  $("#avatarPickerModal").addClass("hidden");
  toastr.success("تم الحفظ بنجاح", "نجاح!");
}

function saveProfile() {
  const name = $("#editName").val();
  const lang = $("#editLang").val();
  const img = $("#editAvatarImg").attr("src");
  const kids = $("#editKids").prop("checked");
  const age = $("#editBirth").val();
  const index = $("#editModal").data("index");

  const obj = { name, img, lang, kids, age };
  if (index === null || index === undefined) {
    profiles.push(obj);
  } else {
    profiles[index] = obj;
  }

  closeModal("editModal");
  renderManageList();
  renderProfiles();
  toastr.success("تم الحفظ بنجاح", "نجاح!");
}

function deleteProfile() {
  const index = $("#editModal").data("index");
  profiles.splice(index, 1);
  closeModal("editModal");
  renderManageList();
  renderProfiles();
  toastr.success("تم الحذف بنجاح", "نجاح!");
}

// الأحداث
$("#openProfileModal").on("click", () => {
  renderProfiles();
  $("#profileModal").removeClass("hidden");
});

function closeModal(id) {
  $(`#${id}`).addClass("hidden");
}

function switchProfile(name, img) {
  closeModal("profileModal");
  $("#profile-name").text(name);
  $(".profile-img").attr("src", img);
  toastr.success("تم التبديل بنجاح", "نجاح!");
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

$("#editKids").on("change", function () {
  $("#ageSelector").toggleClass("hidden", !this.checked);
});
