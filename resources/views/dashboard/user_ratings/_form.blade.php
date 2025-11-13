<div class="row">
    <div class="col-md-12">
        <div class="mb-3 border shadow card border-1">
            <div class="pt-4 card-body">
                <div class="row">
                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.user') }}" :value="$userRating->user_name" name="user_name"
                            placeholder="{{ __('admin.user') }}" disabled />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.content_type') }}" :value="$userRating->content_type" name="content_type"
                            placeholder="{{ __('admin.content_type') }}" disabled />
                    </div>

                     <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.content') }}" :value="$userRating->content->slug" name="content"
                            placeholder="{{ __('admin.content') }}" disabled />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.rating') }}" disabled :value="$userRating->rating" name="rating"
                            placeholder="{{ __('admin.rating') }}" disabled />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.review') }}" :value="$userRating->review" name="review"
                            placeholder="{{ __('admin.review') }}" disabled />
                    </div>
                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.helpful_count') }}" :value="$userRating->helpful_count" name="helpful_count"
                            placeholder="{{ __('admin.helpful_count') }}" disabled />
                    </div>

                    <div class="mb-4 col-md-6">
                        <x-form.input label="{{ __('admin.reviewed_at') }}" disabled :value="$userRating->reviewed_at"
                            name="reviewed_at" placeholder="{{ __('admin.reviewed_at') }}" />
                    </div>
                </div>

                <div class="row">
                    <div class="mb-4 col-md-6">
                        <x-form.select label="{{ __('admin.status') }}" id="status" :value="$userRating->status"
                            name="status" placeholder="{{ __('admin.status') }}" :options="['pending', 'approved' , 'rejected' ]" />
                    </div>
                </div>
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
</div>
