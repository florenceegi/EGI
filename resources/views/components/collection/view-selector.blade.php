{{-- resources/views/components/collection/view-selector.blade.php --}}
@props(['totalItems' => 0, 'totalHolders' => 0])

<div class="flex items-center p-1 bg-gray-800 rounded-lg">
    {{-- Items (Grid) --}}
    <button class="flex items-center px-4 py-2 space-x-2 text-sm font-medium text-gray-400 transition-all duration-200 rounded-md view-selector-btn hover:text-white"
            data-view="items">
        <span class="text-base material-symbols-outlined">grid_view</span>
        <span class="hidden sm:inline">{{ __('collection.view_selector.items') }}</span>
        <span class="text-xs bg-gray-600 text-gray-300 px-2 py-0.5 rounded-full ml-1">{{ $totalItems }}</span>
    </button>

    {{-- List --}}
    <button class="flex items-center px-4 py-2 space-x-2 text-sm font-medium transition-all duration-200 rounded-md view-selector-btn active"
            data-view="list">
        <span class="text-base material-symbols-outlined">view_list</span>
        <span class="hidden sm:inline">{{ __('collection.view_selector.list') }}</span>
    </button>

    {{-- Holders --}}
    <button class="flex items-center px-4 py-2 space-x-2 text-sm font-medium text-gray-400 transition-all duration-200 rounded-md view-selector-btn hover:text-white"
            data-view="holders">
        <span class="text-base material-symbols-outlined">group</span>
        <span class="hidden sm:inline">{{ __('collection.view_selector.holders') }}</span>
        <span class="text-xs bg-gray-600 text-gray-300 px-2 py-0.5 rounded-full ml-1">{{ $totalHolders }}</span>
    </button>

    {{-- Traits --}}
    <button class="flex items-center px-4 py-2 space-x-2 text-sm font-medium text-gray-400 transition-all duration-200 rounded-md view-selector-btn hover:text-white"
            data-view="traits">
        <span class="text-base material-symbols-outlined">category</span>
        <span class="hidden sm:inline">{{ __('collection.view_selector.traits') }}</span>
    </button>
</div>

<style>
.view-selector-btn.active {
    @apply bg-indigo-600 text-white;
}

.view-selector-btn:not(.active):hover {
    @apply bg-gray-700 text-white;
}

/* Mobile responsive - stack vertically on very small screens */
@media (max-width: 480px) {
    .view-selector-btn {
        @apply px-2 py-2;
    }

    .view-selector-btn span:not(.material-symbols-outlined) {
        @apply hidden;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // View selector functionality
    const viewButtons = document.querySelectorAll('.view-selector-btn');
    const egisContainer = document.getElementById('egis-container');
    const holdersContainer = document.getElementById('holders-container');
    const traitsContainer = document.getElementById('traits-container');

    // Initialize with list view as default
    showListView();

    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const view = this.dataset.view;

            // Update active state
            viewButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            // Show/hide containers
            switch(view) {
                case 'items':
                    showGridView();
                    break;
                case 'list':
                    showListView();
                    break;
                case 'holders':
                    showHoldersView();
                    break;
                case 'traits':
                    showTraitsView();
                    break;
            }
        });
    });

    function showGridView() {
        if (egisContainer) egisContainer.style.display = 'grid';
        if (holdersContainer) holdersContainer.style.display = 'none';
        if (traitsContainer) traitsContainer.style.display = 'none';

        // Show grid items, hide list items
        document.querySelectorAll('.egi-item.grid-view').forEach(item => {
            item.style.display = 'block';
        });
        document.querySelectorAll('.egi-item.list-view').forEach(item => {
            item.style.display = 'none';
        });

        // Update grid class
        if (egisContainer) egisContainer.className = 'egi-grid';
    }

    function showListView() {
        if (egisContainer) egisContainer.style.display = 'block';
        if (holdersContainer) holdersContainer.style.display = 'none';
        if (traitsContainer) traitsContainer.style.display = 'none';

        // Show list items, hide grid items
        document.querySelectorAll('.egi-item.grid-view').forEach(item => {
            item.style.display = 'none';
        });
        document.querySelectorAll('.egi-item.list-view').forEach(item => {
            item.style.display = 'block';
        });

        // Update container class for list layout
        if (egisContainer) egisContainer.className = 'space-y-4';
    }

    function showHoldersView() {
        if (egisContainer) egisContainer.style.display = 'none';
        if (holdersContainer) holdersContainer.style.display = 'block';
        if (traitsContainer) traitsContainer.style.display = 'none';
    }

    function showTraitsView() {
        if (egisContainer) egisContainer.style.display = 'none';
        if (holdersContainer) holdersContainer.style.display = 'none';
        if (traitsContainer) traitsContainer.style.display = 'block';
    }
});
</script>
