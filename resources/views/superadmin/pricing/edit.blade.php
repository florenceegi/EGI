<x-layouts.superadmin pageTitle="Modifica Feature Pricing">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-base-content">✏️ Modifica Feature Pricing</h1>
        <p class="mt-2 text-lg text-base-content/70">Aggiorna i dati della feature:
            <strong>{{ $pricing->feature_name }}</strong></p>
    </div>

    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <form action="{{ route('superadmin.pricing.update', $pricing) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Feature Code --}}
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-semibold">Codice Feature <span class="text-error">*</span></span>
                    </label>
                    <input type="text" name="feature_code" value="{{ old('feature_code', $pricing->feature_code) }}"
                        class="@error('feature_code') input-error @enderror input input-bordered"
                        placeholder="es: ai_egi_deep_analysis" required>
                    @error('feature_code')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                    <label class="label">
                        <span class="label-text-alt">Codice univoco per identificare la feature</span>
                    </label>
                </div>

                {{-- Feature Name --}}
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-semibold">Nome Feature <span class="text-error">*</span></span>
                    </label>
                    <input type="text" name="feature_name" value="{{ old('feature_name', $pricing->feature_name) }}"
                        class="@error('feature_name') input-error @enderror input input-bordered"
                        placeholder="es: EGI Deep Analysis" required>
                    @error('feature_name')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                {{-- Feature Description --}}
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-semibold">Descrizione</span>
                    </label>
                    <textarea name="feature_description" rows="3"
                        class="@error('feature_description') textarea-error @enderror textarea textarea-bordered"
                        placeholder="Descrizione dettagliata della feature">{{ old('feature_description', $pricing->feature_description) }}</textarea>
                    @error('feature_description')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                {{-- Feature Category --}}
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-semibold">Categoria <span class="text-error">*</span></span>
                    </label>
                    <select name="feature_category"
                        class="@error('feature_category') select-error @enderror select select-bordered" required>
                        <option value="">Seleziona categoria</option>
                        <option value="ai_services"
                            {{ old('feature_category', $pricing->feature_category) == 'ai_services' ? 'selected' : '' }}>
                            AI Services
                        </option>
                        <option value="premium_visibility"
                            {{ old('feature_category', $pricing->feature_category) == 'premium_visibility' ? 'selected' : '' }}>
                            Premium Visibility
                        </option>
                        <option value="premium_profile"
                            {{ old('feature_category', $pricing->feature_category) == 'premium_profile' ? 'selected' : '' }}>
                            Premium Profile
                        </option>
                        <option value="premium_analytics"
                            {{ old('feature_category', $pricing->feature_category) == 'premium_analytics' ? 'selected' : '' }}>
                            Premium Analytics
                        </option>
                        <option value="exclusive_access"
                            {{ old('feature_category', $pricing->feature_category) == 'exclusive_access' ? 'selected' : '' }}>
                            Exclusive Access
                        </option>
                        <option value="platform_services"
                            {{ old('feature_category', $pricing->feature_category) == 'platform_services' ? 'selected' : '' }}>
                            Platform Services
                        </option>
                    </select>
                    @error('feature_category')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                {{-- Pricing Row --}}
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    {{-- Cost Egili --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Costo Egili (Ƹ)</span>
                        </label>
                        <input type="number" name="cost_egili" value="{{ old('cost_egili', $pricing->cost_egili) }}"
                            step="1" min="0"
                            class="@error('cost_egili') input-error @enderror input input-bordered"
                            placeholder="es: 100">
                        @error('cost_egili')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>

                    {{-- Cost FIAT --}}
                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text font-semibold">Costo FIAT (EUR €)</span>
                        </label>
                        <input type="number" name="cost_fiat_eur"
                            value="{{ old('cost_fiat_eur', $pricing->cost_fiat_eur) }}" step="0.01" min="0"
                            class="@error('cost_fiat_eur') input-error @enderror input input-bordered"
                            placeholder="es: 9.99">
                        @error('cost_fiat_eur')
                            <label class="label">
                                <span class="label-text-alt text-error">{{ $message }}</span>
                            </label>
                        @enderror
                    </div>
                </div>

                {{-- Min Tier Required --}}
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text font-semibold">Tier Minimo Richiesto <span
                                class="text-error">*</span></span>
                    </label>
                    <select name="min_tier_required"
                        class="@error('min_tier_required') select-error @enderror select select-bordered" required>
                        <option value="">Seleziona tier</option>
                        <option value="free"
                            {{ old('min_tier_required', $pricing->min_tier_required) == 'free' ? 'selected' : '' }}>
                            Free</option>
                        <option value="starter"
                            {{ old('min_tier_required', $pricing->min_tier_required) == 'starter' ? 'selected' : '' }}>
                            Starter
                        </option>
                        <option value="pro"
                            {{ old('min_tier_required', $pricing->min_tier_required) == 'pro' ? 'selected' : '' }}>Pro
                        </option>
                        <option value="business"
                            {{ old('min_tier_required', $pricing->min_tier_required) == 'business' ? 'selected' : '' }}>
                            Business
                        </option>
                        <option value="enterprise"
                            {{ old('min_tier_required', $pricing->min_tier_required) == 'enterprise' ? 'selected' : '' }}>
                            Enterprise
                        </option>
                    </select>
                    @error('min_tier_required')
                        <label class="label">
                            <span class="label-text-alt text-error">{{ $message }}</span>
                        </label>
                    @enderror
                </div>

                {{-- Is Active --}}
                <div class="form-control mb-6">
                    <label class="label cursor-pointer justify-start gap-4">
                        <input type="checkbox" name="is_active" value="1"
                            {{ old('is_active', $pricing->is_active) ? 'checked' : '' }}
                            class="checkbox-primary checkbox">
                        <span class="label-text font-semibold">Feature Attiva</span>
                    </label>
                    <label class="label">
                        <span class="label-text-alt">Se deselezionata, la feature non sarà disponibile per gli
                            utenti</span>
                    </label>
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-2">
                    <a href="{{ route('superadmin.pricing.index') }}" class="btn btn-ghost">
                        Annulla
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Aggiorna Feature Pricing
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.superadmin>
