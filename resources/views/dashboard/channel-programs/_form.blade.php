<div class="row">
    <div class="col-md-12">
        @if($errors->any())
        <div class="mb-3 alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>{{ __('admin.Error') }}:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.Title_ar') }}" :value="$program->title_ar" name="title_ar"
                            placeholder="{{ __('admin.title_ar_placeholder') }}" required autofocus />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.Title_en') }}" :value="$program->title_en" name="title_en"
                            placeholder="{{ __('admin.title_en_placeholder') }}" />
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{ __('admin.Channel') }} <span class="text-danger">*</span></label>
                        <select name="channel_id" class="form-control" required>
                            <option value="">{{ __('admin.select_channel') }}</option>
                            @foreach($channels as $channel)
                            <option value="{{ $channel->id }}" @selected(old('channel_id', $program->channel_id) ==
                                $channel->id)>
                                {{ $channel->name_ar }}
                            </option>
                            @endforeach
                        </select>
                        @error('channel_id')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{ __('admin.Genre') }}</label>
                        <select name="genre" class="form-control">
                            <option value="">{{ __('admin.select_genre') }}</option>
                            <option value="news" @selected(old('genre', $program->genre) == 'news')>
                                {{ __('admin.news') }}
                            </option>
                            <option value="sports" @selected(old('genre', $program->genre) == 'sports')>
                                {{ __('admin.sports') }}
                            </option>
                            <option value="drama" @selected(old('genre', $program->genre) == 'drama')>
                                {{ __('admin.drama') }}
                            </option>
                            <option value="documentary" @selected(old('genre', $program->genre) == 'documentary')>
                                {{ __('admin.documentary') }}
                            </option>
                            <option value="entertainment" @selected(old('genre', $program->genre) == 'entertainment')>
                                {{ __('admin.entertainment') }}
                            </option>
                            <option value="kids" @selected(old('genre', $program->genre) == 'kids')>
                                {{ __('admin.kids') }}
                            </option>
                            <option value="religious" @selected(old('genre', $program->genre) == 'religious')>
                                {{ __('admin.religious') }}
                            </option>
                            <option value="educational" @selected(old('genre', $program->genre) == 'educational')>
                                {{ __('admin.educational') }}
                            </option>
                            <option value="others" @selected(old('genre', $program->genre) == 'others')>
                                {{ __('admin.others') }}
                            </option>
                        </select>
                        @error('genre')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{ __('admin.Start_Time') }} <span
                                class="text-danger">*</span></label>
                        <input type="datetime-local" name="start_time" class="form-control"
                            value="{{ old('start_time', $program->start_time?->format('Y-m-d\TH:i') ?? '') }}" required>
                        @error('start_time')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{ __('admin.End_Time') }} <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="end_time" class="form-control"
                            value="{{ old('end_time', $program->end_time?->format('Y-m-d\TH:i') ?? '') }}" required>
                        <small class="text-muted">{{ __('admin.end_time_hint') }}</small>
                        @error('end_time')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{ __('admin.Description_ar') }}</label>
                        <textarea class="form-control" name="description_ar" rows="4"
                            placeholder="{{ __('admin.description_ar_placeholder') }}">{{ old('description_ar', $program->description_ar) }}</textarea>
                        @error('description_ar')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{ __('admin.Description_en') }}</label>
                        <textarea class="form-control" name="description_en" rows="4"
                            placeholder="{{ __('admin.description_en_placeholder') }}">{{ old('description_en', $program->description_en) }}</textarea>
                        @error('description_en')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{ __('admin.Poster') }}</label>

                        @if(isset($program->poster_url) && $program->poster_url)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $program->poster_url) }}" alt="Poster" class="img-thumbnail"
                                style="max-height: 100px;">
                        </div>
                        @endif

                        <input type="file" name="poster_url_out" class="form-control" accept="image/*"
                            onchange="previewImage(this, 'poster-preview')">

                        <img id="poster-preview" class="img-thumbnail mt-2" style="max-height: 150px; display: none;">

                        <small class="text-muted d-block mt-1">
                            {{ __('admin.poster_hint') }}
                        </small>

                        @error('poster_url')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label d-block">{{ __('admin.Is_Live') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_live" value="0">
                            <input class="form-check-input" type="checkbox" id="is_live" name="is_live" value="1"
                                @checked($program->is_live ?? false)>
                            <label class="form-check-label" for="is_live">{{ __('admin.is_live_label') }}</label>
                        </div>
                        <small class="text-muted d-block mt-1">
                            {{ __('admin.is_live_hint') }}
                        </small>
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label d-block">{{ __('admin.Is_Repeat') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_repeat" value="0">
                            <input class="form-check-input" type="checkbox" id="is_repeat" name="is_repeat" value="1"
                                @checked($program->is_repeat ?? false)>
                            <label class="form-check-label" for="is_repeat">{{ __('admin.is_repeat_label') }}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3 border shadow card border-1">
            <div class="card-body">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('dashboard.channel-programs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i> {{ __('admin.Cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> {{ $btn_label ?? __('admin.Save') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>
@endpush