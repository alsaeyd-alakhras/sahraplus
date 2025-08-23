<x-dashboard-layout>
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/custom/media.css') }}">
    @endpush
    <div class="card">
        <div class="my-2 card-header d-flex justify-content-between">
            <h3 class="card-title">📁 مكتبة الوسائط</h3>
            <div>
                <form id="uploadForm" enctype="multipart/form-data" class="mb-3">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="file" name="images[]" id="imageInput" class="mb-2 form-control d-none" multiple
                        accept="image/*" required>
                    <button type="button" class="btn btn-primary" id="uploadBtn">رفع</button>
                </form>
            </div>
        </div>
        <hr class="m-0">
        <div class="card-body">


            <div id="mediaGrid" class="masonry">
                {{-- الصور ستُملأ تلقائيًا عبر jQuery --}}
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog"
        aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تأكيد الحذف</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    هل أنت متأكد من حذف هذه الصورة؟
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        id="closeDeleteModal">إلغاء</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">نعم، حذف</button>
                </div>
            </div>
        </div>
    </div>
    <!-- مودال التعديل -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form id="editForm" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">تعديل بيانات الوسيط</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="p-4 modal-body">
                    <div class="row">

                        <!-- صورة المعاينة -->
                        <div class="col-6">
                            <img id="editPreview" src="" alt="preview" class="rounded border shadow img-fluid"
                                style="max-height: 300px;">

                            <h6 class="mb-3 fw-bold" style="font-size: 1.1rem; color: #333;">تفاصيل الوسيط</h6>

                            <div class="mb-4"
                                style="font-size: 0.9rem; color: #555; background-color: #f8f9fa; padding: 1rem; border-radius: 8px; border: 1px solid #dee2e6;">
                                <div style="margin-bottom: 0.5rem;">
                                    <strong style="min-width: 60px; display: inline-block;">الاسم:</strong>
                                    <span id="infoName">---</span>
                                </div>
                                <div style="margin-bottom: 0.5rem;">
                                    <strong style="min-width: 60px; display: inline-block;">النوع:</strong>
                                    <span id="infoMime">---</span>
                                </div>
                                <div style="margin-bottom: 0.5rem;">
                                    <strong style="min-width: 60px; display: inline-block;">الحجم:</strong>
                                    <span id="infoSize">---</span> KB
                                </div>
                                <div style="margin-bottom: 0;">
                                    <strong style="min-width: 60px; display: inline-block;">الرابط:</strong>
                                    <input type="text" id="infoURL"
                                        class="mt-1 form-control form-control-sm d-inline-block"
                                        style="width: 100%; font-size: 0.8rem; color: #6c757d; background-color: #e9ecef;"
                                        readonly onclick="navigator.clipboard.writeText(this.value)">
                                </div>
                            </div>
                        </div>

                        <!-- التفاصيل -->
                        <div class="col-6">
                            <input type="hidden" id="editId">

                            <div class="mb-3">
                                <label class="form-label">Alt Text</label>
                                <input type="text" id="editAlt" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" id="editTitle" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Caption</label>
                                <textarea id="editCaption" class="form-control" rows="2"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea id="editDescription" class="form-control" rows="2"></textarea>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="px-4 py-3 modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        id="closeEditModal">إلغاء</button>
                    <button type="submit" class="btn btn-success">💾 حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>

    <!-- زر سري لفتح مودال التعديل -->
    <button type="button" class="hidden btn btn-primary d-none" data-bs-toggle="modal" data-bs-target="#editModal"
        id="openEditModalBtn"></button>

    <!-- زر سري لفتح مودال الحذف -->
    <button type="button" class="hidden btn btn-primary d-none" data-bs-toggle="modal"
        data-bs-target="#confirmDeleteModal" id="openDeleteModalBtn"></button>

    @push('scripts')
        <script>
            const urlUpload = "{{ route('dashboard.media.store') }}";
            const urlIndex = "{{ route('dashboard.media.index') }}";
            const urlDelete = "{{ route('dashboard.media.destroy', ':id') }}";
            const urlEdit = "{{ route('dashboard.media.update', ':id') }}";
            const _token = "{{ csrf_token() }}";
        </script>
        <script src="{{ asset('js/custom/media.js') }}"></script>
    @endpush
</x-dashboard-layout>
