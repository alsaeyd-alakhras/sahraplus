<!-- ملف: profile-modal -->
<div id="profileModal" class="flex flex-col hidden fixed inset-0 justify-center items-center bg-background-base z-[9999]">
    <div class="flex justify-end p-4 w-full">
        <button onclick="closeModal('profileModal')" class="text-2xl">&times;</button>
    </div>
    <div class="px-4 w-full max-w-6xl text-center text-white">
        <div class="flex justify-center items-center space-x-4 rtl:space-x-reverse">
            <h1 class="mb-6 text-5xl font-black text-fire-red font-arabic">
                سهرة بلس
            </h1>
        </div>
        <h2 class="mb-6 text-2xl font-bold">مرحبًا👋 اختر من يُشاهد الآن</h2>
        <div id="profileList" class="flex flex-wrap gap-6 justify-center">
            <!-- يتم توليد العناصر ديناميكياً في متغير تحت اسمه profiles من خلاله يمكن تعبئة المتغيرات هين -->
        </div>
        <button id="manageProfilesBtn" class="mt-10 underline hover:text-sky-400">إدارة الملفات</button>
    </div>
</div>

<!-- إدارة الملفات -->
<div id="manageModal"
    class="hidden overflow-y-auto fixed inset-0 py-10 bg-background-base z-[9999] flex flex-col justify-center items-center ">
    <div class="px-4 mx-auto max-w-5xl text-white">
        <div class="flex justify-between items-center mb-10">
            <h2 class="text-3xl font-bold">إدارة الملفات</h2>
            <button onclick="closeModal('manageModal')" class="text-2xl">&times;</button>
        </div>

        <div id="manageList" class="grid grid-cols-2 gap-6 md:grid-cols-3 lg:grid-cols-5">
            <!-- يتم توليد العناصر ديناميكياً -->
        </div>
    </div>
</div>

<!-- فورم تعديل/إضافة -->
<div id="editModal" class="flex hidden fixed inset-0 justify-center items-center px-4 bg-background-base z-[9999]">
    <div class="bg-[#1e222a] rounded-lg w-full max-w-md p-6 text-white">
        <div class="text-center">
            <div class="flex justify-between items-center mb-4">
                <h2 class="mb-4 text-2xl font-bold">إدارة الملف</h2>
                <button onclick="closeModal('editModal')" class="text-2xl">&times;</button>
            </div>
            <div class="flex justify-center mb-4">
                <div id="editAvatar" class="overflow-hidden relative w-28 h-28 rounded-full cursor-pointer"
                    onclick="$('#avatarPickerModal').removeClass('hidden')">
                    <img id="editAvatarImg" src="" class="object-cover w-full h-full">
                    <div
                        class="flex absolute inset-0 justify-center items-center bg-black bg-opacity-50 opacity-0 transition hover:opacity-100">
                        <i class="text-lg text-white fas fa-pen"></i>
                    </div>
                </div>
            </div>
            <label class="block mb-1 text-sm text-gray-300">اسم الملف</label>
            <input id="editName" type="text" class="py-2 mb-4 w-full text-center text-black rounded-full">

            <label class="block mb-1 text-sm text-gray-300">اللغة</label>
            <select id="editLang" class="py-2 mb-4 w-full text-center text-black rounded-full">
                <option value="ar">العربية</option>
                <option value="en">English</option>
            </select>

            <label class="block mb-2 text-sm text-gray-300">حساب أطفال</label>
            <label class="inline-flex items-center mb-4 cursor-pointer">
                <input id="editKids" type="checkbox" class="sr-only peer">
                <div class="relative w-11 h-6 bg-gray-600 rounded-full transition peer peer-checked:bg-emerald-500">
                    <div
                        class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-full">
                    </div>
                </div>
            </label>

            <div id="ageSelector" class="hidden">
                <label class="block mb-1 text-sm text-gray-300">تاريخ الميلاد</label>
                <input id="editBirth" type="month" class="py-2 mb-2 w-full text-center text-black rounded-full">
                <label class="inline-flex items-center text-sm">
                    <input type="checkbox" checked class="ml-2 form-checkbox accent-emerald-500">
                    تحديد المحتوى المناسب لعمر الطفل
                </label>
            </div>

            <button onclick="saveProfile()"
                class="py-2 mt-6 w-full font-bold text-white bg-gradient-to-r from-green-600 to-green-800 rounded-full transition-all duration-300 hover:from-green-700 hover:to-green-900">
                حفظ
            </button>

            <button onclick="deleteProfile()"
                class="py-2 mt-6 w-full font-bold text-white bg-gradient-to-r from-red-700 to-gray-900 rounded-full transition-all duration-300 hover:from-red-800 hover:to-black">حذف</button>
        </div>
    </div>
</div>

<!-- Modal اختيار الصور -->
<div id="avatarPickerModal"
    class="fixed inset-0 z-[99999] bg-black bg-opacity-80 flex justify-center items-center hidden">
    <div class="bg-[#1e222a] text-white p-6 rounded-lg w-full max-w-xl text-center">
        <h2 class="mb-4 text-xl font-bold">اختر صورة الملف</h2>
        <div class="grid grid-cols-4 gap-4 mb-6">
            <!-- صور جاهزة -->
            <img src="./assets/images/avatars/1.jpg"
                class="w-20 h-20 rounded-full transition cursor-pointer hover:scale-105"
                onclick="selectAvatar(this.src)">
            <img src="./assets/images/avatars/2.png"
                class="w-20 h-20 rounded-full transition cursor-pointer hover:scale-105"
                onclick="selectAvatar(this.src)">
            <img src="./assets/images/avatars/3.png"
                class="w-20 h-20 rounded-full transition cursor-pointer hover:scale-105"
                onclick="selectAvatar(this.src)">
            <img src="./assets/images/avatars/4.png"
                class="w-20 h-20 rounded-full transition cursor-pointer hover:scale-105"
                onclick="selectAvatar(this.src)">
            <img src="./assets/images/avatars/5.png"
                class="w-20 h-20 rounded-full transition cursor-pointer hover:scale-105"
                onclick="selectAvatar(this.src)">
            <img src="./assets/images/avatars/6.png"
                class="w-20 h-20 rounded-full transition cursor-pointer hover:scale-105"
                onclick="selectAvatar(this.src)">
            <img src="./assets/images/avatars/7.jpg"
                class="w-20 h-20 rounded-full transition cursor-pointer hover:scale-105"
                onclick="selectAvatar(this.src)">
            <img src="./assets/images/avatars/8.jpg"
                class="w-20 h-20 rounded-full transition cursor-pointer hover:scale-105"
                onclick="selectAvatar(this.src)">
            <img src="./assets/images/avatars/9.png"
                class="w-20 h-20 rounded-full transition cursor-pointer hover:scale-105"
                onclick="selectAvatar(this.src)">
            <img src="./assets/images/avatars/10.jpg"
                class="w-20 h-20 rounded-full transition cursor-pointer hover:scale-105"
                onclick="selectAvatar(this.src)">
        </div>
        <button onclick="$('#avatarPickerModal').addClass('hidden')"
            class="px-6 py-2 bg-gray-700 rounded hover:bg-gray-600">إغلاق</button>
    </div>
</div>
