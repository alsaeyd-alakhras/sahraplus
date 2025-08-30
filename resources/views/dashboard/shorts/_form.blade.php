<div class="row">
    <div class="mb-4 col-md-6">
        <x-form.input label="عنوان الفيديو" :value="$short->title" name="title" required />
    </div>
    <div class="mb-4 col-md-6">
        <x-form.input label="رابط المشاركة" :value="$short->share_url" name="share_url" />
    </div>

    <div class="mb-4 col-md-12">
        <label class="form-label">الوصف</label>
        <textarea class="form-control" name="description" rows="3">{{ $short->description }}</textarea>
    </div>

    <div class="mb-4 col-md-6">
        <x-form.input label="رابط البوستر" :value="$short->poster_path" name="poster_path" />
        <input type="file" name="posterUpload" class="form-control mt-2" />
        @if($short->poster_path)
            <img src="{{ Storage::url($short->poster_path) }}" style="max-height:80px" />
        @endif
    </div>

    <div class="mb-4 col-md-6">
        <x-form.input label="رابط الفيديو" :value="$short->video_path" name="video_path" required />
        <input type="file" name="videoUpload" class="form-control mt-2" />
    </div>

    <div class="mb-4 col-md-4">
        <label class="form-label">نسبة العرض</label>
        <select class="form-control" name="aspect_ratio">
            <option value="vertical" @selected($short->aspect_ratio=='vertical')>عمودي</option>
            <option value="horizontal" @selected($short->aspect_ratio=='horizontal')>أفقي</option>
        </select>
    </div>

    <div class="mb-4 col-md-4">
        <label class="form-label">الحالة</label>
        <select class="form-control" name="status">
            <option value="active" @selected($short->status=='active')>نشط</option>
            <option value="inactive" @selected($short->status=='inactive')>غير نشط</option>
        </select>
    </div>

    <div class="mb-4 col-md-4">
        <label class="form-label">مميز</label>
        <div class="form-check form-switch">
            <input type="hidden" name="is_featured" value="0">
            <input class="form-check-input" type="checkbox" name="is_featured" value="1" @checked($short->is_featured)>
        </div>
    </div>

    <div class="mb-3 border shadow card border-1">
            <div class="card-body">
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        {{ $btn_label ?? 'أضف' }}
                    </button>
                </div>
            </div>
        </div>
        
</div>
