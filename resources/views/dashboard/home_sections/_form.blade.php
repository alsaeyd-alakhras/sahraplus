<div class="row">
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="{{ asset('css/custom/media.css') }}">
    @endpush
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @php
        $locale = app()->getLocale();
        $oldSectionItems = old('sectionItems', isset($section) ? ($sectionItems ?? []) : []);
    @endphp

    <div class="col-md-12">
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- العناوين --}}
                    <div class="col-md-6">
                        <x-form.input label="{{ __('admin.title_ar') }}" class="text-right" :value="old('title_ar', $section->title_ar ?? '')"
                            name="title_ar" required />
                    </div>
                    <div class="col-md-6">
                        <x-form.input label="{{ __('admin.title_en') }}" class="text-left" :value="old('title_en', $section->title_en ?? '')"
                            name="title_en" />
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="mb-4 col-md-3">
                        <x-form.selectkey label="{{ __('admin.platform') }}" name="platform"
                            :selected="$section->platform ?? 'both'" :options="$platformOptions" />
                    </div>

                    <div class="mb-4 col-md-3">
                        <x-form.input type="number" placeholder="0" min="0"
                            label="{{ __('admin.Sort_order') }}" :value="old('sort_order', $section->sort_order ?? 0)" name="sort_order" min="0" />
                    </div>

                    <div class="mb-4 col-md-3">
                        <label class="form-label d-block">{{ __('admin.starts_at') }}</label>
                        <input type="datetime-local" class="form-control" name="starts_at" 
                            value="{{ old('starts_at', $section->starts_at ? $section->starts_at->format('Y-m-d\TH:i') : '') }}">
                    </div>

                    <div class="mb-4 col-md-3">
                        <label class="form-label d-block">{{ __('admin.ends_at') }}</label>
                        <input type="datetime-local" class="form-control" name="ends_at" 
                            value="{{ old('ends_at', $section->ends_at ? $section->ends_at->format('Y-m-d\TH:i') : '') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label d-block">{{ __('admin.is_active') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                value="1" @checked(old('is_active', $section->is_active ?? 1))>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label d-block">{{ __('admin.is_kids') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_kids" value="0">
                            <input class="form-check-input" type="checkbox" id="is_kids"
                                name="is_kids" value="1" @checked(old('is_kids', $section->is_kids ?? 0))>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="sectionItems-section" class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="mb-2 d-flex justify-content-between align-items-center">
                            <label class="fw-semibold">{{ __('admin.section_items') }}</label>
                            <button type="button" id="add-sectionItem-row" class="btn btn-dark btn-sm">
                                + {{ __('admin.add') }}
                            </button>
                        </div>

                        <div id="sectionItems-rows" class="gap-3 d-grid">
                            @if (empty($oldSectionItems))
                                @include('dashboard.home_sections.partials._sectionItem_row', [
                                    'i' => 0,
                                    'row' => [],
                                ])
                            @else
                                @foreach ($oldSectionItems as $i => $row)
                                    @include('dashboard.home_sections.partials._sectionItem_row', [
                                        'i' => $i,
                                        'row' => $row,
                                    ])
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary" id="submitBtn">
                {{ $btn_label ?? __('admin.Save') }}
            </button>
        </div>
    </div>
</div>

@include('layouts.partials.dashboard.mediamodel')

@push('scripts')
    <script>
        const sectionItemRowPartial = "{{ route('dashboard.home_sections.sectionItemRowPartial') }}";
        const urlGetContents = "{{ route('dashboard.plan_access.getContents') }}";
        const successMessage = "{{ __('admin.select_content') }}";
        const errorMessage = "{{ __('admin.error_loading') }}";
        const loadingMessage = "{{ __('admin.loading') }}...";
    </script>
    <script src="{{ asset('js/custom/homeSections.js') }}"></script>
@endpush

