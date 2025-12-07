<div class="row">
    <div class="col-md-12">
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    {{-- EPG Channel Selector (Only for Create) --}}
                    @if(!$channel->exists)
                    <div class="mb-4 col-md-12">
                        <div class="alert alert-info">
                            <i class="ph ph-info me-2"></i>
                            <strong>{{ __('admin.EPG_Channel_Import') }}:</strong>
                            {{ __('admin.epg_channel_import_hint') }}
                        </div>
                        <label class="form-label">{{ __('admin.Import_from_EPG') }}</label>
                        <select id="epg-channel-selector" class="form-control">
                            <option value="">{{ __('admin.select_epg_channel_or_manual') }}</option>
                            @foreach($epgChannels as $epgChannel)
                            <option value="{{ $epgChannel['id'] }}" 
                                    data-name="{{ $epgChannel['name'] }}"
                                    data-icon="{{ $epgChannel['icon'] ?? '' }}">
                                {{ $epgChannel['name'] }} ({{ $epgChannel['id'] }})
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">{{ __('admin.epg_selector_hint') }}</small>
                    </div>
                    @endif

                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.Name_ar') }}" :value="$channel->name_ar" name="name_ar"
                            placeholder="{{ __('admin.name_ar_placeholder') }}" required autofocus />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.Name_en') }}" :value="$channel->name_en" name="name_en"
                            placeholder="{{ __('admin.name_en_placeholder') }}" required />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.Slug_optional') }}" :value="$channel->slug" name="slug"
                            placeholder="{{ __('admin.slug_placeholder') }}" />
                        <small class="text-muted">{{ __('admin.slug_hint') }}</small>
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{ __('admin.Category') }} <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-control" required>
                            <option value="">{{ __('admin.select_category') }}</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id', $channel->category_id) ==
                                $cat->id)>
                                {{ $cat->name_ar }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{ __('admin.Stream_Name') }} <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" id="stream_url" name="stream_url" class="form-control"
                                value="{{ old('stream_url', $channel->stream_url) }}"
                                placeholder="{{ __('admin.stream_name_placeholder') }}" maxlength="100" required>
                            <button type="button" class="btn btn-info" id="testStreamBtn">
                                <i class="ph ph-play-circle"></i> {{ __('admin.Test_Stream') }}
                            </button>
                        </div>
                        <small class="text-muted">{{ __('admin.stream_name_hint') }}</small>
                        @error('stream_url')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div id="stream-test-result" class="mt-2" style="display: none;"></div>
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{ __('admin.Stream_Type') }} <span
                                class="text-danger">*</span></label>
                        <select name="stream_type" id="stream_type" class="form-control" required>
                            <option value="">{{ __('admin.select_stream_type') }}</option>
                            <option value="hls" @selected(old('stream_type', $channel->stream_type) == 'hls')>
                                {{ __('admin.HLS') }}
                            </option>
                            <option value="dash" @selected(old('stream_type', $channel->stream_type) == 'dash')>
                                {{ __('admin.DASH') }}
                            </option>
                            <option value="rtmp" @selected(old('stream_type', $channel->stream_type) == 'rtmp')>
                                {{ __('admin.RTMP') }}
                            </option>
                        </select>
                        @error('stream_type')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input name="epg_id" label="{{ __('admin.EPG_ID') }}" :value="$channel->epg_id"
                            placeholder="{{ __('admin.epg_id_placeholder') }}" />
                        <small class="text-muted">{{ __('admin.epg_id_hint') }}</small>
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input type="number" label="{{ __('admin.Sort_order') }}"
                            :value="$channel->sort_order ?? 0" name="sort_order" placeholder="0" min="0" max="9999" />
                        <small class="text-muted">{{ __('admin.sort_order_hint') }}</small>
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input name="language" label="{{ __('admin.Language') }}" :value="$channel->language"
                            placeholder="{{ __('admin.language_placeholder') }}" />
                        <small class="text-muted">{{ __('admin.language_hint') }}</small>
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{ __('admin.Country') }}</label>
                        <select name="country" class="form-control">
                            <option value="">{{ __('admin.select_country') }}</option>
                            @foreach($countries as $country)
                            <option value="{{ $country->code }}" @selected(old('country', $channel->country) ==
                                $country->code)>
                                {{ $country->name_ar }} ({{ $country->code }})
                            </option>
                            @endforeach
                        </select>
                        @error('country')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{ __('admin.Description_ar') }}</label>
                        <textarea class="form-control" name="description_ar" rows="4"
                            placeholder="{{ __('admin.description_ar_placeholder') }}">{{ old('description_ar', $channel->description_ar) }}</textarea>
                        @error('description_ar')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{ __('admin.Description_en') }}</label>
                        <textarea class="form-control" name="description_en" rows="4"
                            placeholder="{{ __('admin.description_en_placeholder') }}">{{ old('description_en', $channel->description_en) }}</textarea>
                        @error('description_en')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{ __('admin.Logo') }}</label>

                        @if(isset($channel->logo_url) && $channel->logo_url)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $channel->logo_url) }}" alt="Logo" class="img-thumbnail"
                                style="max-height: 100px;">
                        </div>
                        @endif

                        <input type="file" name="logo_url_out" class="form-control" accept="image/*"
                            onchange="previewImage(this, 'logo-preview')">

                        <img id="logo-preview" class="img-thumbnail mt-2" style="max-height: 150px; display: none;">

                        <small class="text-muted d-block mt-1">
                            {{ __('admin.logo_hint') }}
                        </small>

                        @error('logo_url_out')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label">{{ __('admin.Poster') }}</label>

                        @if(isset($channel->poster_url) && $channel->poster_url)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $channel->poster_url) }}" alt="Poster" class="img-thumbnail"
                                style="max-height: 100px;">
                        </div>
                        @endif

                        <input type="file" name="poster_url_out" class="form-control" accept="image/*"
                            onchange="previewImage(this, 'poster-preview')">

                        <img id="poster-preview" class="img-thumbnail mt-2" style="max-height: 150px; display: none;">

                        <small class="text-muted d-block mt-1">
                            {{ __('admin.poster_hint') }}
                        </small>

                        @error('poster_url_out')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label d-block">{{ __('admin.Is_featured') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_featured" value="0">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured"
                                value="1" @checked($channel->is_featured ?? false)>
                            <label class="form-check-label" for="is_featured">{{ __('admin.featured_label') }}</label>
                        </div>
                    </div>

                    <div class="mb-4 col-md-6">
                        <label class="form-label d-block">{{ __('admin.Status') }}</label>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                @checked($channel->is_active ?? true)>
                            <label class="form-check-label" for="is_active">{{ __('admin.active_label') }}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-3 border shadow card border-1">
            <div class="card-body">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('dashboard.live-tv-channels.index') }}" class="btn btn-secondary">
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
    
    // EPG Channel Import
    @if(!$channel->exists)
    document.addEventListener('DOMContentLoaded', function() {
        const epgSelector = document.getElementById('epg-channel-selector');
        
        if (epgSelector) {
            epgSelector.addEventListener('change', async function() {
                const channelId = this.value;
                
                if (!channelId) {
                    return;
                }
                
                // Show loading state
                const originalHtml = this.innerHTML;
                this.disabled = true;
                
                try {
                    const response = await fetch('{{ route('dashboard.live-tv-channels.epg-channel-details') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ channel_id: channelId })
                    });
                    
                    const result = await response.json();
                    
                    if (result.success && result.data) {
                        const channel = result.data;
                        
                        // Fill in the form fields
                        const nameEn = channel.display_names[0] || channel.name;
                        
                        // Set English name
                        document.querySelector('input[name="name_en"]').value = nameEn;
                        
                        // Set EPG ID
                        document.querySelector('input[name="epg_id"]').value = channel.id;
                        
                        // If icon URL exists, you can optionally set it
                        if (channel.icon) {
                            // You can add logic here to download and set the logo
                            console.log('Channel icon URL:', channel.icon);
                        }
                        
                        // Show success message
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-success alert-dismissible fade show mt-2';
                        alertDiv.innerHTML = `
                            <i class="ph ph-check-circle me-2"></i>
                            <strong>{{ __('admin.Success') }}!</strong> 
                            {{ __('admin.epg_channel_imported') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        `;
                        epgSelector.parentElement.appendChild(alertDiv);
                        
                        // Auto-remove after 5 seconds
                        setTimeout(() => alertDiv.remove(), 5000);
                    }
                } catch (error) {
                    console.error('Error fetching EPG channel details:', error);
                    alert('{{ __('admin.Error_loading_channel_details') }}');
                } finally {
                    this.disabled = false;
                }
            });
        }
    });
    @endif
    
    // Test Flussonic Stream
    document.addEventListener('DOMContentLoaded', function() {
        const testBtn = document.getElementById('testStreamBtn');
        const streamInput = document.getElementById('stream_url');
        const streamType = document.getElementById('stream_type');
        const resultDiv = document.getElementById('stream-test-result');
        
        testBtn.addEventListener('click', async function() {
            const streamName = streamInput.value.trim();
            const protocol = streamType.value;
            
            if (!streamName) {
                showResult('danger', '{{ __('admin.enter_stream_name_first') }}');
                return;
            }
            
            if (!protocol) {
                showResult('danger', '{{ __('admin.select_stream_type_first') }}');
                return;
            }
            
            // Disable button and show loading
            testBtn.disabled = true;
            testBtn.innerHTML = '<i class="ph ph-spinner ph-spin"></i> {{ __('admin.Testing') }}...';
            
            try {
                const response = await fetch('{{ route('dashboard.live-tv-channels.test-stream') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ 
                        stream_name: streamName,
                        protocol: protocol
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    const expiresDate = new Date(data.expires_at * 1000).toLocaleString('ar-EG');
                    const alertType = data.server_reachable ? 'success' : 'warning';
                    let message = `<strong>${data.server_reachable ? '✅' : '⚠️'} ${data.message}</strong><br><small>`;
                    
                    message += `<strong>{{ __('admin.Stream_Status') }}:</strong> ${data.status || 'unknown'}<br>`;
                    message += `<strong>{{ __('admin.Generated_URL') }}:</strong><br>`;
                    message += `<code style="word-break: break-all; font-size: 11px;">${data.url}</code><br>`;
                    message += `<strong>{{ __('admin.Expires_at') }}:</strong> ${expiresDate}`;
                    
                    if (data.warning) {
                        message += `<br><br><strong style="color: #dc3545;">⚠️ ${data.warning}</strong>`;
                    }
                    
                    message += `</small>`;
                    showResult(alertType, message);
                } else {
                    showResult('danger', `❌ ${data.message}`);
                }
            } catch (error) {
                showResult('danger', `❌ {{ __('admin.Connection_Error') }}: ${error.message}`);
            } finally {
                // Re-enable button
                testBtn.disabled = false;
                testBtn.innerHTML = '<i class="ph ph-play-circle"></i> {{ __('admin.Test_Stream') }}';
            }
        });
        
        function showResult(type, message) {
            resultDiv.className = `alert alert-${type} mt-2`;
            resultDiv.innerHTML = message;
            resultDiv.style.display = 'block';
            
            // Auto-hide after 10 seconds for success
            if (type === 'success') {
                setTimeout(() => {
                    resultDiv.style.display = 'none';
                }, 10000);
            }
        }
    });
</script>
@endpush