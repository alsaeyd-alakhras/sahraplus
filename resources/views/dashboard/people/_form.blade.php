<div class="row">
    @push('styles')
        <style>
            /* Tags Input Styles - متوافق مع Bootstrap */
            .tags-container {
                border: 1px solid #dee2e6;
                border-radius: 0.375rem;
                padding: 0.5rem;
                min-height: 60px;
                background: #fff;
                cursor: text;
                transition: all 0.15s ease-in-out;
            }

            .tags-container:hover {
                border-color: #adb5bd;
            }

            .tags-container.focused {
                border-color: #86b7fe;
                outline: 0;
                box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            }

            .tags-wrapper {
                display: flex;
                flex-wrap: wrap;
                gap: 0.375rem;
                margin-bottom: 0.5rem;
            }

            .tag {
                background: #0d6efd;
                color: white;
                padding: 0.25rem 0.75rem;
                border-radius: 1rem;
                font-size: 0.875rem;
                display: flex;
                align-items: center;
                gap: 0.375rem;
            }

            .tag .remove {
                cursor: pointer;
                width: 1rem;
                height: 1rem;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.3);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.75rem;
                line-height: 1;
            }

            .tag .remove:hover {
                background: rgba(255, 255, 255, 0.5);
            }

            .tag-input {
                border: none;
                outline: none;
                background: transparent;
                min-width: 120px;
                padding: 0.25rem;
                font-size: 0.875rem;
            }

            .suggestions-list {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                border: 1px solid #dee2e6;
                border-radius: 0.375rem;
                max-height: 200px;
                overflow-y: auto;
                z-index: 1050;
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                display: none;
            }

            .suggestion-item {
                padding: 0.75rem 1rem;
                cursor: pointer;
                border-bottom: 1px solid #f8f9fa;
                font-size: 0.875rem;
            }

            .suggestion-item:last-child {
                border-bottom: none;
            }

            .suggestion-item:hover,
            .suggestion-item.highlighted {
                background: #f8f9fa;
                color: #0d6efd;
            }

            .suggestion-item .badge {
                background: #e9ecef;
                color: #6c757d;
                padding: 0.125rem 0.5rem;
                border-radius: 0.75rem;
                font-size: 0.75rem;
                margin-left: 0.5rem;
            }

            .placeholder {
                color: #6c757d;
                font-size: 0.875rem;
                padding: 0.25rem;
                pointer-events: none;
            }
        </style>
    @endpush
    <div class="col-md-12">
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.Name_ar') }}" :value="$person->name_ar" name="name_ar"
                            placeholder="محمد..." required autofocus />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.Name_en') }}" :value="$person->name_en" name="name_en"
                            placeholder="Mohammad" />
                    </div>
                    <div class="col-md-6">
                        <x-form.textarea label="{{ __('admin.Bio_ar') }}" name="bio_ar" rows="2"
                            :value="$person->bio_ar" placeholder="السيرة الذاتية بالعربية..." />
                    </div>
                    <div class="col-md-6">
                        <x-form.textarea label="{{ __('admin.Bio_en') }}" name="bio_en" rows="2"
                            :value="$person->bio_en" placeholder="Biography in English..." />
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="mb-4 col-md-4 col-sm-12">
                        <x-form.input type="date" label="{{ __('site.birth_date') }}" :value="$person->birth_date?->format('Y-m-d')"
                            name="birth_date" />
                    </div>
                    <div class="mb-4 col-md-4 col-sm-12">
                        <x-form.input label="{{ __('admin.birth_place') }}" :value="$person->birth_place" name="birth_place" />
                    </div>
                    <div class="mb-4 col-md-4 col-sm-12">
                        <x-form.input label="{{ __('admin.nationality') }}" :value="$person->nationality" name="nationality" />
                    </div>
                    <div class="mb-4 col-md-4 col-sm-12">
                        @php
                            $curGender = $person->gender ?? 'male';
                            $genderOptions = [
                                'male' => 'ذكر',
                                'female' => 'أنثى',
                            ];
                        @endphp
                        <x-form.selectkey label="{{ __('admin.gender') }}" name="gender" required :selected="$curGender"
                            :options="$genderOptions" />
                    </div>

                    <div class="mb-4 col-md-4">
                        <x-form.input type="number" min="0" label="TMDB ID" :value="$person->tmdb_id" name="tmdb_id"
                            placeholder="مثال: 550" />
                    </div>

                    {{-- ✅ حالة النشاط --}}
                    <div class="mb-4 col-md-4">
                        <label class="form-label d-block">{{ __('admin.Status') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                value="1" @checked($person->is_active)>
                            <label class="form-check-label" for="is_active">{{ __('admin.active') }}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">

                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{ __('admin.known_for') }}</label>
                        <div class="position-relative">
                            <div class="tags-container" id="known_for_container">
                                <div class="tags-wrapper" id="known_for_wrapper"></div>
                                <input type="text" class="tag-input" id="known_for_input"
                                    placeholder="ابحث أو أضف مهنة جديدة...">
                                <div class="placeholder" id="known_for_placeholder">ابحث أو أضف مهنة جديدة...</div>
                            </div>
                            <div class="suggestions-list" id="known_for_suggestions"></div>
                        </div>
                        <input type="hidden" name="known_for" id="known_for"
                            value="{{ is_array($person->known_for) ? implode(',', $person->known_for) : $person->known_for ?? '' }}">
                    </div>

                    <div class="mb-4 col-md-6">
                        @php
                            $photo_url = Str::startsWith($person->photo_url, ['http', 'https']);
                            $photo_url_out = $photo_url ? $person->photo_url : null;
                        @endphp
                        <x-form.input type="url" label="{{ __('admin.photo_url') }}" :value="$photo_url_out"
                            name="photo_url_out" placeholder="{{ __('admin.photo_url_placeholder') }}" />
                        <input type="text" id="imageInput" name="photo_url" value="{{ $person->photo_url }}"
                            class="d-none form-control">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#mediaModal"
                                data-clear-btn="#clearImageBtn1" data-img="#poster_img" data-mode="single"
                                data-input="#imageInput" data-out-input="#photo_url_out" class="mt-3 btn btn-primary openMediaModal">
                                {{ __('admin.choose_from_media') }}
                            </button>
                            <button type="button"
                                class="clear-btn mt-3 btn btn-danger {{ !empty($person->photo_url) ? '' : 'd-none' }}"
                                id="clearImageBtn1" data-img="#poster_img" data-input="#imageInput" data-out-input="#photo_url_out">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="mt-2">
                            <img src="{{ $person->photo_full_url }}" alt="poster" id="poster_img"
                                class="{{ !empty($person->photo_url) ? '' : 'd-none' }}" style="max-height:100px">
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary">
                {{ $btn_label ?? __('admin.Save') }}
            </button>
        </div>
    </div>
</div>
@include('layouts.partials.dashboard.mediamodel')

@push('scripts')
    <script>
        // نظام Tags متعدد - يمكن استخدامه مع أي حقل
        function initTagsInput(fieldId) {
            const containerId = fieldId + '_container';
            const inputId = fieldId + '_input';
            const suggestionsId = fieldId + '_suggestions';
            const wrapperId = fieldId + '_wrapper';
            const placeholderId = fieldId + '_placeholder';

            // المهن المقترحة
            const suggestions = [
                'ممثل', 'مخرج', 'كاتب', 'منتج', 'مصور سينمائي', 'مونتير',
                'ملحن', 'مؤدي أصوات', 'راقص', 'مغني', 'عارض أزياء', 'كوميديان',
                'مذيع', 'صحفي', 'مقدم برامج', 'ناقد سينمائي', 'كاتب سيناريو',
                'مصمم أزياء', 'مصمم ديكور', 'مدير تصوير', 'مهندس صوت'
            ];

            let selectedTags = [];
            let currentHighlight = -1;

            // القيم الأولية من الـ hidden input
            const initialValue = $('#' + fieldId).val();
            if (initialValue) {
                selectedTags = initialValue.split(',').map(tag => tag.trim()).filter(tag => tag);
                renderTags();
                updateHiddenInput();
            }

            const $container = $('#' + containerId);
            const $input = $('#' + inputId);
            const $suggestions = $('#' + suggestionsId);
            const $placeholder = $('#' + placeholderId);

            // إظهار/إخفاء placeholder
            function togglePlaceholder() {
                if (selectedTags.length === 0 && $input.val() === '') {
                    $placeholder.show();
                } else {
                    $placeholder.hide();
                }
            }

            // رندر الـ tags
            function renderTags() {
                const $wrapper = $('#' + wrapperId);
                $wrapper.empty();

                selectedTags.forEach((tag, index) => {
                    const $tag = $(`
                        <div class="tag">
                            <span>${tag}</span>
                            <div class="remove" data-field="${fieldId}" data-index="${index}">×</div>
                        </div>
                    `);
                    $wrapper.append($tag);
                });

                togglePlaceholder();
            }

            // تحديث الـ hidden input
            function updateHiddenInput() {
                $('#' + fieldId).val(selectedTags.join(','));
            }

            // إضافة tag جديد
            function addTag(tag) {
                const trimmedTag = tag.trim();
                if (trimmedTag && !selectedTags.includes(trimmedTag)) {
                    selectedTags.push(trimmedTag);
                    renderTags();
                    updateHiddenInput();
                    $input.val('');
                    hideSuggestions();
                    updateSelectedValues();
                }
            }

            // حذف tag
            function removeTag(index) {
                selectedTags.splice(index, 1);
                renderTags();
                updateHiddenInput();
                updateSelectedValues();
            }

            // إظهار الاقتراحات
            function showSuggestions(query = '') {
                const filteredSuggestions = suggestions.filter(suggestion =>
                    suggestion.toLowerCase().includes(query.toLowerCase()) &&
                    !selectedTags.includes(suggestion)
                );

                $suggestions.empty();

                if (filteredSuggestions.length > 0) {
                    filteredSuggestions.forEach((suggestion, index) => {
                        const $item = $(`
                            <div class="suggestion-item" data-value="${suggestion}">
                                <span class="badge">مقترح</span>${suggestion}
                            </div>
                        `);
                        $suggestions.append($item);
                    });

                    // إضافة خيار إنشاء tag جديد
                    if (query && !filteredSuggestions.includes(query) && !selectedTags.includes(query)) {
                        const $newItem = $(`
                            <div class="suggestion-item" data-value="${query}">
                                <span class="badge" style="background: #48bb78; color: white;">جديد</span>${query}
                            </div>
                        `);
                        $suggestions.prepend($newItem);
                    }

                    $suggestions.show();
                    currentHighlight = -1;
                } else {
                    hideSuggestions();
                }
            }

            // إخفاء الاقتراحات
            function hideSuggestions() {
                $suggestions.hide();
                currentHighlight = -1;
            }

            // تحديد الاقتراح المميز
            function highlightSuggestion(direction) {
                const $items = $suggestions.find('.suggestion-item');
                const maxIndex = $items.length - 1;

                $items.removeClass('highlighted');

                if (direction === 'down') {
                    currentHighlight = currentHighlight >= maxIndex ? 0 : currentHighlight + 1;
                } else if (direction === 'up') {
                    currentHighlight = currentHighlight <= 0 ? maxIndex : currentHighlight - 1;
                }

                if (currentHighlight >= 0 && currentHighlight <= maxIndex) {
                    $items.eq(currentHighlight).addClass('highlighted');
                }
            }

            // تحديث القيم المعروضة
            function updateSelectedValues() {
                const $display = $('#selectedValues');
                if (selectedTags.length > 0) {
                    $display.html(
                        `<strong>المهن المحددة:</strong> ${selectedTags.join(' | ')}<br><strong>القيمة النهائية:</strong> ${selectedTags.join(',')}`
                        );
                } else {
                    $display.text('لم يتم تحديد أي قيم بعد');
                }
            }

            // Event listeners
            $container.on('click', function() {
                $input.focus();
            });

            $input.on('focus', function() {
                $container.addClass('focused');
                togglePlaceholder();
                if ($(this).val()) {
                    showSuggestions($(this).val());
                }
            });

            $input.on('blur', function() {
                setTimeout(() => {
                    $container.removeClass('focused');
                    hideSuggestions();
                    togglePlaceholder();
                }, 200);
            });

            $input.on('input', function() {
                const query = $(this).val();
                showSuggestions(query);
                togglePlaceholder();
            });

            $input.on('keydown', function(e) {
                const value = $(this).val().trim();

                if (e.key === 'Enter') {
                    e.preventDefault();
                    const $highlighted = $suggestions.find('.suggestion-item.highlighted');
                    if ($highlighted.length > 0) {
                        addTag($highlighted.data('value'));
                    } else if (value) {
                        addTag(value);
                    }
                } else if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    highlightSuggestion('down');
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    highlightSuggestion('up');
                } else if (e.key === 'Escape') {
                    hideSuggestions();
                } else if (e.key === 'Backspace' && !value && selectedTags.length > 0) {
                    removeTag(selectedTags.length - 1);
                }
            });

            // النقر على الاقتراحات
            $suggestions.on('click', '.suggestion-item', function() {
                addTag($(this).data('value'));
            });

            // تهيئة أولية
            togglePlaceholder();
        }

        // Event handler عام لحذف الـ tags
        $(document).on('click', '.tag .remove', function() {
            const fieldId = $(this).data('field');
            const index = parseInt($(this).data('index'));

            // نجد الـ function المناسبة للحقل
            if (window['removeTag_' + fieldId]) {
                window['removeTag_' + fieldId](index);
            }
        });

        // تهيئة حقل known_for عند تحميل الصفحة
        $(document).ready(function() {
            if ($('#known_for').length) {
                initTagsInput('known_for');

                // إنشاء function خاصة بهذا الحقل لحذف الـ tags
                window['removeTag_known_for'] = function(index) {
                    // نحتاج نوصل للمتغيرات داخل النطاق
                    const event = new CustomEvent('removeTag', {
                        detail: {
                            fieldId: 'known_for',
                            index: index
                        }
                    });
                    document.dispatchEvent(event);
                };
            }
        });

        // عرض القيم الحالية
        function showCurrentValues() {
            const values = $('#known_for').val();
            alert(values ? `القيم المحددة: ${values}` : 'لم يتم تحديد أي قيم');
        }

        function updateSelectedValues() {
            const $display = $('#selectedValues');
            const selectedTags = $('#known_for').val().split(',').filter(tag => tag.trim());
            if (selectedTags.length > 0 && selectedTags[0] !== '') {
                $display.html(
                    `<strong>المهن المحددة:</strong> ${selectedTags.join(' | ')}<br><strong>القيمة النهائية:</strong> ${selectedTags.join(',')}`
                    );
            } else {
                $display.text('لم يتم تحديد أي قيم بعد');
            }
        }

        // مراقبة تغيير القيم
        setInterval(updateSelectedValues, 500);
    </script>
@endpush
