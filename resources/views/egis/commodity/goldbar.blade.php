@props(['data' => []])

<div class="rounded-lg border border-[#D4A574]/30 bg-[#FFF8F0] p-4 mb-6">
    <h3 class="mb-4 text-sm font-bold text-[#D4A574]">{{ __('pa_heritage.goldbar_details') }}</h3>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        {{-- Weight --}}
        <div>
            <label for="commodity_data_weight" class="mb-2 block text-sm font-medium text-gray-700">
                {{ __('pa_heritage.field_weight') }} <span class="text-red-500">*</span>
            </label>
            <div class="flex">
                <input type="number" step="0.001" id="commodity_data_weight" name="commodity_data[weight]"
                    placeholder="es. 10.00" value="{{ old('commodity_data.weight', $data['weight'] ?? '') }}"
                    class="w-full rounded-l-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:border-[#D4A574] focus:outline-none focus:ring-1 focus:ring-[#D4A574]">
                <select name="commodity_data[unit]" class="rounded-r-lg border-y border-r border-gray-300 bg-gray-50 px-2 py-2 text-sm text-gray-700">
                    <option value="g">g</option>
                    <option value="oz">oz</option>
                </select>
            </div>
        </div>
        
        {{-- Purity --}}
        <div>
            <label for="commodity_data_purity" class="mb-2 block text-sm font-medium text-gray-700">
                {{ __('pa_heritage.field_purity') }} <span class="text-red-500">*</span>
            </label>
            <input type="text" id="commodity_data_purity" name="commodity_data[purity]"
                value="{{ old('commodity_data.purity', $data['purity'] ?? '999.9') }}"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:border-[#D4A574] focus:outline-none focus:ring-1 focus:ring-[#D4A574]">
        </div>

        {{-- Margin Percent --}}
        <div>
            <label for="commodity_data_margin_percent" class="mb-2 block text-sm font-medium text-gray-700">
                {{ __('pa_heritage.field_markup') }} (%) <span class="text-red-500">*</span>
            </label>
            <input type="number" step="0.01" id="commodity_data_margin_percent" name="commodity_data[margin_percent]"
                value="{{ old('commodity_data.margin_percent', $data['margin_percent'] ?? '10.00') }}"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:border-[#D4A574] focus:outline-none focus:ring-1 focus:ring-[#D4A574]">
        </div>

        {{-- Margin Fixed --}}
        <div>
            <label for="commodity_data_margin_fixed" class="mb-2 block text-sm font-medium text-gray-700">
                {{ __('pa_heritage.field_margin_fixed') }} (€)
            </label>
            <input type="number" step="0.01" id="commodity_data_margin_fixed" name="commodity_data[margin_fixed]"
                value="{{ old('commodity_data.margin_fixed', $data['margin_fixed'] ?? '0.00') }}"
                class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:border-[#D4A574] focus:outline-none focus:ring-1 focus:ring-[#D4A574]">
        </div>
    </div>
</div>
