<x-layouts.superadmin pageTitle="Admin Panel">

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body text-center py-24">
                    <h3 class="text-2xl font-bold text-base-content/60 mb-4">
                        {{ __('admin.featured.calendar_coming_soon') }}
                    </h3>
                    <p class="text-base-content/40">
                        {{ __('admin.featured.calendar_desc') }}
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('admin.featured.pending') }}" class="btn btn-primary">
                            {{ __('admin.featured.view_pending_requests') }}
                        </a>
                    </div>
                </div>
            </div>
            

    </div>
</x-layouts.superadmin>



