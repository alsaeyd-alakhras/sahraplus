<x-front-layout>
    @php
        $tabActive = request('tab', 'subscriptions');
    @endphp
    <div class="flex" style="margin-top: 100px;">
        <!-- الشريط الجانبي -->
        <aside class="w-72 bg-[#181c23] p-6 space-y-2 border-l border-gray-800 text-white h-full rounded-3xl m-3">
            <!-- Tabs -->
            <button
                class="flex justify-between items-center px-4 py-3 w-full rounded-lg transition-all duration-300 tab-btn hover:bg-red-700 hover:pr-6 group"
                data-tab="subscriptions">
                <span class="transition-all duration-300 group-hover:pr-2">{{ __('site.subscription_management') }}</span>
                <i class="text-gray-400 transition-all duration-300 fas fa-chevron-left group-hover:translate-x-1"></i>
            </button>

            <button
                class="flex justify-between items-center px-4 py-3 w-full rounded-lg transition-all duration-300 tab-btn hover:bg-red-700 hover:pr-6 group"
                data-tab="password">
                <span class="transition-all duration-300 group-hover:pr-2">{{ __('site.change_password') }}</span>
                <i class="text-gray-400 transition-all duration-300 fas fa-chevron-left group-hover:translate-x-1"></i>
            </button>

            <button
                class="flex justify-between items-center px-4 py-3 w-full rounded-lg transition-all duration-300 tab-btn hover:bg-red-700 hover:pr-6 group"
                data-tab="account">
                <span class="transition-all duration-300 group-hover:pr-2">{{ __('site.account_management') }}</span>
                <i class="text-gray-400 transition-all duration-300 fas fa-chevron-left group-hover:translate-x-1"></i>
            </button>

            <button
                class="flex justify-between items-center px-4 py-3 w-full rounded-lg transition-all duration-300 tab-btn hover:bg-red-700 hover:pr-6 group"
                data-tab="devices">
                <span class="transition-all duration-300 group-hover:pr-2">{{ __('site.device_management') }}</span>
                <i class="text-gray-400 transition-all duration-300 fas fa-chevron-left group-hover:translate-x-1"></i>
            </button>

            <button
                class="flex justify-between items-center px-4 py-3 w-full rounded-lg transition-all duration-300 tab-btn hover:bg-red-700 hover:pr-6 group"
                data-tab="parental">
                <span class="transition-all duration-300 group-hover:pr-2">{{ __('site.parental_interface') }}</span>
                <i class="text-gray-400 transition-all duration-300 fas fa-chevron-left group-hover:translate-x-1"></i>
            </button>

            <button
                class="flex justify-between items-center px-4 py-3 w-full rounded-lg transition-all duration-300 tab-btn hover:bg-red-700 hover:pr-6 group"
                data-tab="offers">
                <span class="transition-all duration-300 group-hover:pr-2">{{ __('site.offers') }}</span>
                <i class="text-gray-400 transition-all duration-300 fas fa-chevron-left group-hover:translate-x-1"></i>
            </button>
        </aside>
        <!-- المحتوى الرئيسي -->
        <main class="flex-1 p-10">
            <div id="subscriptions" class="{{ $tabActive == 'subscriptions' ? 'block' : 'hidden' }} space-y-6 text-white tab-content">
                <!-- العنوان -->
                <div class="text-center">
                    <h2 class="text-2xl font-bold">{{ __('site.subscription_management') }}</h2>
                    <p class="mt-1 text-sm text-gray-400">{{ __('site.subscription_active') }}</p>
                    <p class="text-sm text-gray-500">{{ __('site.subscription_until') }} <span class="text-green-400">{{ \Carbon\Carbon::now()->addMonths(3)->format('d F Y') }}</span></p>
                </div>

                <!-- تفاصيل الاشتراك -->
                <div class="overflow-hidden rounded-lg border border-gray-700">
                    <details class="bg-[#1e2430] rounded-lg overflow-hidden border border-gray-700">
                        <summary
                            class="flex justify-between items-center px-4 py-3 w-full bg-gradient-to-l from-blue-800 to-purple-800 transition-all hover:brightness-110">
                            <span>{{ __('site.subscription_details') }} :</span>
                            <i class="fas fa-chevron-down"></i>
                        </summary>
                        <div class="p-4 border-t border-gray-700">
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm border border-gray-700 rounded-lg overflow-hidden bg-[#1e2430]">
                                <div class="p-3 border-b border-gray-700 md:border-b-0 md:border-r">
                                    <div class="text-gray-400">{{ __('site.package') }}:</div>
                                    <div class="font-bold text-white">VIP</div>
                                </div>
                                <div class="p-3">
                                    <div class="text-gray-400">{{ __('site.subscription') }}:</div>
                                    <div class="font-bold text-white">3 {{ __('site.months') }}</div>
                                </div>
                                <div class="col-span-2 p-3 border-t border-gray-700">
                                    <div class="text-gray-400">{{ __('site.display_name') }}:</div>
                                    <div class="font-bold text-white">{{ __('site.marketing_offer_coupon') }}</div>
                                </div>
                            </div>
                        </div>
                    </details>
                    <p class="px-4 py-3 text-sm text-green-400 border-t border-gray-700">
                        للمزيد من المعلومات حول كيفية تغيير باقتك <a href="#" class="underline">اضغط هنا</a>
                    </p>
                </div>

                <!-- خدمات أخرى -->
                <div class="space-y-2">
                    <h3 class="font-bold">خدمات أخرى:</h3>

                    <!-- زر تمديد القسيمة -->
                    <details class="bg-[#1e2430] rounded-lg overflow-hidden border border-gray-700">
                        <summary class="flex justify-between items-center px-4 py-3 cursor-pointer">
                            <span>مدد اشتراك القسيمة الخاص بك</span>
                            <i class="fas fa-chevron-down"></i>
                        </summary>
                        <div class="p-4 border-t border-gray-700">
                            <h4 class="mb-2 font-bold">تفعيل قسيمة الاشتراك</h4>
                            <p class="mb-2 text-sm text-gray-400">الباقة الحالية: <span
                                    class="font-bold text-white">VIP</span></p>
                            <input type="text" placeholder="قسيمة الاشتراك"
                                class="p-3 mb-3 w-full text-white bg-gray-800 rounded-lg border border-gray-700 focus:outline-none focus:ring-2 focus:ring-fire-red">
                            <button
                                class="py-2 w-full bg-gray-600 rounded-lg transition hover:bg-gray-500">تحقق</button>

                            <p class="mt-6 text-sm text-center text-gray-400">تحتاج لبطاقة هدية؟</p>
                            <div class="grid grid-cols-2 gap-4 mt-3 md:grid-cols-4">
                                <div class="space-y-1 text-center">
                                    <img src="https://placehold.co/100x50?text=MTG" class="mx-auto rounded" />
                                    <a href="#" class="text-xs text-sky-400 hover:underline">المزيد من
                                        المعلومات</a>
                                </div>
                                <div class="space-y-1 text-center">
                                    <img src="https://placehold.co/100x50?text=Bee" class="mx-auto rounded" />
                                    <a href="#" class="text-xs text-sky-400 hover:underline">المزيد من
                                        المعلومات</a>
                                </div>
                                <div class="space-y-1 text-center">
                                    <img src="https://placehold.co/100x50?text=Fawry" class="mx-auto rounded" />
                                    <a href="#" class="text-xs text-sky-400 hover:underline">المزيد من
                                        المعلومات</a>
                                </div>
                                <div class="space-y-1 text-center">
                                    <img src="https://placehold.co/100x50?text=Others" class="mx-auto rounded" />
                                    <a href="#" class="text-xs text-sky-400 hover:underline">المزيد من
                                        المعلومات</a>
                                </div>
                            </div>
                        </div>
                    </details>

                    <!-- المساعدة -->
                    <details class="bg-[#1e2430] rounded-lg overflow-hidden border border-gray-700">
                        <summary class="flex justify-between items-center px-4 py-3 cursor-pointer">
                            <span>هل تحتاج للمساعدة بخصوص اشتراكك؟</span>
                            <i class="fas fa-chevron-down"></i>
                        </summary>
                        <div class="p-4 space-y-2 text-sm text-gray-300 border-t border-gray-700">
                            <p>اتصل على الأرقام التالية:</p>
                            <div>
                                <p><span class="text-white">+966 115 101 940</span> : المملكة العربية السعودية</p>
                                <p><span class="text-white">04 5677 400</span> : الإمارات العربية المتحدة</p>
                                <p><span class="text-white">06 5777 577</span> : المملكة الأردنية الهاشمية</p>
                                <p><span class="text-white">+966 115 101 940</span> : الرقم الدولي</p>
                            </div>
                        </div>
                    </details>
                </div>

                <!-- رجوع -->
                <div class="pt-6 text-center">
                    <a href="#" class="text-sm text-sky-400 hover:underline">
                        ← الرجوع للخطوة السابقة
                    </a>
                </div>
            </div>

            <div id="password" class="{{ $tabActive == 'password' ? 'block' : 'hidden' }} tab-content">
                <h3 class="mb-4 text-2xl font-bold">{{ __('site.change_password') }}</h3>
                <form class="space-y-4 max-w-md" action="{{ route('site.change-password') }}" method="POST">
                    @csrf
                    <input type="password" name="current_password" placeholder="{{ __('site.current_password') }}"
                        class="p-3 w-full bg-gray-800 rounded-md" />
                    <input type="password" name="new_password" placeholder="{{ __('site.new_password') }}"
                        class="p-3 w-full bg-gray-800 rounded-md" />
                    <input type="password" name="confirm_password" placeholder="{{ __('site.confirm_password') }}"
                        class="p-3 w-full bg-gray-800 rounded-md" />
                    <button type="submit"
                        class="px-4 py-2 font-bold bg-green-600 rounded-md hover:bg-green-700">
                        {{ __('site.save_changes') }}
                    </button>
                </form>
            </div>

            <div id="account" class="{{ $tabActive == 'account' ? 'block' : 'hidden' }} space-y-6 text-white tab-content">
                <!-- العنوان -->
                <div class="text-center">
                    <h2 class="text-2xl font-bold">{{ __('site.edit_personal_info') }}</h2>
                    <p class="mt-1 text-sm text-gray-400">{{ __('site.edit_personal_info_desc') }}</p>
                </div>

                <!-- النموذج -->
                <form class="mx-auto space-y-4 max-w-2xl" action="{{ route('site.update-personal-info') }}" method="POST">
                    @csrf
                    <input name="first_name" type="text" placeholder="{{ __('site.first_name') }}" value="{{ $auth_user->first_name }}"
                        class="px-4 py-3 w-full bg-gray-800 rounded-lg border border-gray-700 focus:outline-none focus:ring-2 focus:ring-sky-500">

                    <input name="last_name" type="text" placeholder="{{ __('site.last_name') }}" value="{{ $auth_user->last_name }}"
                        class="px-4 py-3 w-full bg-gray-800 rounded-lg border border-gray-700 focus:outline-none focus:ring-2 focus:ring-sky-500">

                    <!-- الجنس -->
                    <select name="gender" id="gender"
                        class="px-4 py-3 w-full text-white bg-gray-800 rounded-lg border border-gray-700 focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="male" {{ $auth_user->gender == 'male' ? 'selected' : '' }}>{{ __('site.male') }}</option>
                        <option value="female" {{ $auth_user->gender == 'female' ? 'selected' : '' }}>{{ __('site.female') }}</option>
                    </select>

                    <input name="phone" type="text" placeholder="{{ __('site.phone') }}" value="{{ $auth_user->phone }}"
                        class="px-4 py-3 w-full bg-gray-800 rounded-lg border border-gray-700 focus:outline-none focus:ring-2 focus:ring-sky-500">

                    <input name="birth_date" type="date" placeholder="{{ __('site.birth_date') }}" value="{{ $auth_user->date_of_birth ? $auth_user->date_of_birth->format('Y-m-d') : '' }}"
                        class="px-4 py-3 w-full bg-gray-800 rounded-lg border border-gray-700 focus:outline-none focus:ring-2 focus:ring-sky-500">

                    <!-- البريد الإلكتروني (ثابت) -->
                    <input type="email" value="{{ $auth_user->email }}" disabled
                        class="px-4 py-3 w-full text-gray-400 bg-gray-900 rounded-lg border border-gray-700 cursor-not-allowed">

                    <!-- تنويه البريد -->
                    <p class="text-sm text-gray-400">
                        {{ __('site.email_cannot_be_edited') }}
                        <a href="#" class="text-green-400 underline">{{ __('site.customer_service') }}</a>
                    </p>
                    <!-- زر الحفظ -->
                    <button type="submit"
                        class="py-3 w-full font-bold text-white bg-gradient-to-l from-sky-500 to-green-500 rounded-full transition-all hover:brightness-110">
                        {{ __('site.save_changes') }}
                    </button>
                </form>
            </div>


            <div id="devices" class="{{ $tabActive == 'devices' ? 'block' : 'hidden' }} tab-content">
                <h3 class="mb-4 text-2xl font-bold">{{ __('site.device_management') }}</h3>
                <p class="mb-6 text-sm text-gray-300">{{ __('site.device_management_desc') }}</p>

                <!-- إدخال رمز الجهاز -->
                <div class="mb-4">
                    <input type="text" placeholder="قم بإدخال الرمز هنا"
                        class="px-4 py-3 w-full text-sm placeholder-gray-500 text-white bg-gray-900 rounded-full border border-gray-700 focus:outline-none focus:ring focus:ring-primary">
                </div>
                <div class="mb-6">
                    <button
                        class="px-4 py-2 w-full font-bold text-white bg-gray-700 rounded-full transition-all hover:bg-primary">
                        ربط الجهاز
                    </button>
                </div>
                <p class="mb-8 text-xs text-center text-gray-400">يمكنك ربط لغاية 20 جهاز مع حسابك على سهرة</p>

                <!-- الأجهزة المرتبطة -->
                <h4 class="mb-4 text-lg font-bold">الأجهزة المرتبطة حالياً بحسابك</h4>
                <div id="device-list" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- عنصر الجهاز -->
                    <div class="relative p-4 bg-gray-800 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-bold text-white">iPhone</span>
                            <div class="flex gap-2">
                                <button onclick="deleteDevice(this)" class="text-gray-400 hover:text-red-500">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button onclick="editDevice(this)" class="text-gray-400 hover:text-blue-500">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400">تاريخ الإضافة: 2025 يوليو 19</p>
                    </div>

                    <!-- مثال على جهاز آخر -->
                    <div class="relative p-4 bg-gray-800 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-bold text-white">Android TV</span>
                            <div class="flex gap-2">
                                <button onclick="deleteDevice(this)" class="text-gray-400 hover:text-red-500">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button onclick="editDevice(this)" class="text-gray-400 hover:text-blue-500">
                                    <i class="fas fa-pen"></i>
                                </button>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400">تاريخ الإضافة: 2025 يوليو 13</p>
                    </div>
                </div>

                <!-- مودال التعديل -->
                <div id="editModal"
                    class="flex hidden fixed inset-0 z-50 justify-center items-center bg-black bg-opacity-60">
                    <div class="p-6 w-full max-w-md bg-gray-900 rounded-lg">
                        <h4 class="mb-4 text-xl font-bold text-white">تعديل اسم الجهاز</h4>
                        <input type="text" id="editDeviceName"
                            class="px-4 py-2 mb-4 w-full text-white bg-gray-800 rounded"
                            placeholder="اسم الجهاز الجديد">
                        <div class="flex gap-2 justify-end">
                            <button onclick="closeDeviceModal()"
                                class="px-4 py-2 text-white bg-gray-600 rounded hover:bg-gray-700">إلغاء</button>
                            <button onclick="saveDeviceName()"
                                class="px-4 py-2 text-white rounded bg-primary hover:bg-blue-600">حفظ</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ✅ Notification -->
            <div id="successAlert"
                class="{{ $tabActive == 'password' ? 'block' : 'hidden' }} fixed top-0 right-0 left-0 z-50 py-4 font-bold text-center text-white bg-emerald-500 shadow-md transition-all duration-500">
                تم تحديث الرمز بنجاح.
            </div>
            <div id="parental" class="{{ $tabActive == 'parental' ? 'block' : 'hidden' }} space-y-6 text-center  tab-content">
                <h3 class="text-2xl font-bold">واجهة الرقابة الأبوية</h3>
                <p class="text-gray-300">رمز الرقابة الأبوية سيساعدك على توفير حماية أكثر لطفلك</p>
                <p class="text-sm text-gray-400">أدخل الرمز المكون من 4 أرقام</p>

                <div class="flex gap-3 justify-center" dir="ltr">
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                        class="w-16 h-16 text-2xl text-center text-black bg-white rounded-md shadow pin-input focus:outline-none focus:ring-2 focus:ring-fire-red" />
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                        class="w-16 h-16 text-2xl text-center text-black bg-white rounded-md shadow pin-input focus:outline-none focus:ring-2 focus:ring-fire-red" />
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                        class="w-16 h-16 text-2xl text-center text-black bg-white rounded-md shadow pin-input focus:outline-none focus:ring-2 focus:ring-fire-red" />
                    <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                        class="w-16 h-16 text-2xl text-center text-black bg-white rounded-md shadow pin-input focus:outline-none focus:ring-2 focus:ring-fire-red" />
                </div>


                <button id="savePinBtn" type="button"
                    class="py-3 mt-6 w-1/3 font-bold text-white bg-gradient-to-r from-green-400 to-blue-500 rounded-full hover:opacity-90">
                    حفظ
                </button>
            </div>


            <div id="offers" class="{{ $tabActive == 'offers' ? 'block' : 'hidden' }} tab-content">
                <h3 class="mb-4 text-2xl font-bold">{{ __('site.offers') }}</h3>
                <p>{{ __('site.offers_desc') }}</p>
            </div>
        </main>
    </div>


    @push('scripts')
        <script>
            let tabActive = "{{ request('tab') ?? 'subscriptions' }}";
            const buttons = document.querySelectorAll('.tab-btn');
            const tabs = document.querySelectorAll('.tab-content');

            buttons.forEach(btn => {
                btn.addEventListener('click', () => {
                    tabs.forEach(tab => tab.classList.add('hidden'));
                    document.getElementById(btn.dataset.tab).classList.remove('hidden');
                    tabActive = btn.dataset.tab;
                });
            });

            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById(tabActive).classList.remove('hidden');
                const btnActive = document.querySelector(`.tab-btn[data-tab="${tabActive}"]`);
                if (btnActive) btnActive.classList.add('active');
            });
        </script>
        <!-- توليد السنوات -->
        <script>
            let years = "";
            for (let i = 2007; i >= 1970; i--) {
                years += `<option>${i}</option>`;
            }
            document.querySelector("select#year").innerHTML = years;

            let months = ["يناير", "فبراير", "مارس", "أبريل", "مايو", "يونيو", "يوليو", "أغسطس", "سبتمبر", "أكتوبر", "نوفمبر",
                "ديسمبر"
            ];
            for (let i = 0; i < months.length; i++) {
                months[i] = `<option>${months[i]}</option>`;
            }
            document.querySelector("select#month").innerHTML = months.join("");
        </script>

        <!-- Device -->
        <script>
            function deleteDevice(btn) {
                btn.closest('.bg-gray-800').remove();
                toastr.error("تم الحذف بنجاح", "نجاح!");
            }

            let currentEditingCard = null;

            function editDevice(btn) {
                currentEditingCard = btn.closest('.bg-gray-800');
                const name = currentEditingCard.querySelector('span').textContent;
                document.getElementById('editDeviceName').value = name;
                document.getElementById('editModal').classList.remove('hidden');
                toastr.success("تم التبديل بنجاح", "نجاح!");
            }

            function closeDeviceModal() {
                document.getElementById('editModal').classList.add('hidden');
            }

            function saveDeviceName() {
                const newName = document.getElementById('editDeviceName').value;
                if (currentEditingCard && newName.trim()) {
                    currentEditingCard.querySelector('span').textContent = newName;
                    closeDeviceModal();
                }
                toastr.success("تم الحفظ بنجاح", "نجاح!");
            }
        </script>

        <!-- Pin -->
        <script>
            document.getElementById("savePinBtn").addEventListener("click", function() {
                toastr.success("تم الحفظ بنجاح", "نجاح!");
            });
            const inputs = document.querySelectorAll(".pin-input");

            inputs.forEach((input, index) => {
                input.addEventListener("input", (e) => {
                    const value = e.target.value;

                    // السماح برقم واحد فقط
                    if (!/^[0-9]$/.test(value)) {
                        e.target.value = "";
                        return;
                    }

                    // الانتقال للحقل التالي
                    if (index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                });

                // عند الضغط على Backspace يرجع للحقل السابق
                input.addEventListener("keydown", (e) => {
                    if (e.key === "Backspace" && !e.target.value && index > 0) {
                        inputs[index - 1].focus();
                    }
                });
            });
        </script>
    @endpush
</x-front-layout>
