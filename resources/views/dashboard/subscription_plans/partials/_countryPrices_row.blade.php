@php
    $id_countryPrices = $row['id'] ?? '';
    $country_id = $row['country_id'] ?? '';
@endphp
<div class="p-3 mb-3 rounded border shadow-sm country-row card">

    <div class="row g-3 align-items-end">
        <input type="hidden" name="countryPrices[{{ $i }}][id]" class="countryPrices-id"
            value="{{ $id_countryPrices }}">

        <div class="col-md-3">
            <label class="form-label small fw-bold">{{ __('admin.country') }}</label>

            <select name="countryPrices[{{ $i }}][country_id]" class="form-select country-select" data-index="{{ $i }}">
                <option value=""> {{ __('admin.select_country') }}</option>
                @foreach ($countries as $country)
                    <option value="{{ $country->id }}" @if (isset($row['country_id']) && $row['country_id'] == $country->id)
                    selected @endif>
                        {{ app()->getLocale() == 'ar' ? $country->name_ar : $country->name_en }}
                    </option>
                @endforeach
            </select>

        </div>

        <div class="col-md-3">
            <label class="form-label small fw-bold">{{ __('admin.currency') }}</label>
            <input type="text" name="countryPrices[{{ $i }}][currency]" readonly
                class="form-control currency-input text-left" value="{{ $row['currency'] ?? '' }}">
        </div>

        <div class="col-md-2">
            <label class="form-label small fw-bold">{{ __('admin.price_sar') }}</label>
            <input type="text" class="form-control" name="countryPrices[{{ $i }}][price_sar]"
                value="{{ $row['price_sar'] ?? '' }}" placeholder="{{ __('admin.price_sar') }}">
        </div>

        <div class="col-md-2">
            <label class="form-label small fw-bold">{{ __('admin.price_currency') }}</label>
            <input type="text" class="form-control" name="countryPrices[{{ $i }}][price_currency]"
                value="{{ $row['price_currency'] ?? '' }}" placeholder="{{ __('admin.price_currency') }}">
        </div>

        <!-- الترتيب + حذف -->
        <div class="col-md-2">
            <div class="input-group">
                <button type="button" class="btn btn-outline-danger remove-country-row">
                    {{ __('admin.delete_row') }}
                </button>
            </div>
        </div>

    </div>
</div>



@push('scripts')
    <script>
        document.addEventListener("input", function (e) {

            let row = e.target.closest('.country-row');
            if (!row) return;

            // لو المستخدم عدل على السعر بالريال
            if (e.target.matches('[name*="[price_sar]"]')) {
                convertPrice(row, 'SAR');
            }

            // لو عدل على السعر بعملة الدولة
            if (e.target.matches('[name*="[price_currency]"]')) {
                convertPrice(row, 'CURRENCY');
            }

        });

        // دالة مساعدة للحصول على سعر العملة
        function getRate(currency) {
            if (!currency || !window.currencyRates) return 0;
            let rate = parseFloat(window.currencyRates[currency.trim().toUpperCase()]);
            return rate > 0 ? rate : 0;
        }

        // تحويل السعر حسب نوع العملية
        function convertPrice(row, type) {
            let currencyInput = row.querySelector('[name*="[currency]"]');
            if (!currencyInput) return;

            let currency = currencyInput.value;
            let rate = getRate(currency);
            if (!rate) return;

            let sarInput = row.querySelector('[name*="[price_sar]"]');
            let currencyPriceInput = row.querySelector('[name*="[price_currency]"]');
            if (!sarInput || !currencyPriceInput) return;

            if (type === 'SAR') {
                let sarPrice = parseFloat(sarInput.value);
                if (isNaN(sarPrice)) return;
                currencyPriceInput.value = (sarPrice * rate).toFixed(2);
            } else {
                let currencyPrice = parseFloat(currencyPriceInput.value);
                if (isNaN(currencyPrice)) return;
                sarInput.value = (currencyPrice / rate).toFixed(2);
            }
        }
    </script>
    <script>
        $(document).on('change', '.country-select', function (e) {
            var countryId = $(this).val();
            var rowIndex = $(this).data('index');

            if (!countryId) {
                $('input[name="countryPrices[' + rowIndex + '][currency]"]').val('');
                return;
            };
            let row = e.target.closest('.country-row');
            if (!row) return;

            $.ajax({
                url: '/countries/' + countryId + '/currency',
                type: 'GET',
                success: function (response) {
                    var currencyInput = $('input[name="countryPrices[' + rowIndex + '][currency]"]');
                    var row = currencyInput.closest('.country-row');

                    if (!row.length) return;

                    // تحديث العملة
                    currencyInput.val(response.currency);

                    // مسح حقل سعر العملة القديم
                    var currencyPriceInput = row.find('[name*="[price_currency]"]');
                    currencyPriceInput.val('');

                    // أخذ قيمة السعر بالريال وإعادة حساب سعر العملة الجديدة
                    var sarInput = row.find('[name*="[price_sar]"]');
                    if (sarInput.length && sarInput.val()) {
                        // تحويل من الريال للعملة الجديدة
                        convertPrice(row[0], 'SAR');
                    }
                }
            });
        });
    </script>
@endpush