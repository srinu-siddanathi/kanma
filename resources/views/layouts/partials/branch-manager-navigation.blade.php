<div class="hidden sm:flex sm:items-center sm:ml-6">
    <!-- Existing items -->
    <x-nav-link :href="route('branch.dashboard')" :active="request()->routeIs('branch.dashboard')">
        {{ __('Dashboard') }}
    </x-nav-link>

    <x-nav-link :href="route('branch.products.index')" :active="request()->routeIs('branch.products.*')">
        {{ __('Products') }}
    </x-nav-link>

    <x-nav-link :href="route('branch.chat-orders.index')" :active="request()->routeIs('branch.chat-orders.*')">
        {{ __('Chat Orders') }}
    </x-nav-link>

    <!-- New navigation items -->
    <x-nav-link :href="route('branch.delivery-boys.index')" :active="request()->routeIs('branch.delivery-boys.*')">
        {{ __('Delivery Boys') }}
    </x-nav-link>

    <x-nav-link :href="route('branch.order-assignments.index')" :active="request()->routeIs('branch.order-assignments.*')">
        {{ __('Order Assignments') }}
    </x-nav-link>

    <!-- ... other existing items ... -->
</div>

<!-- Mobile menu -->
<div class="sm:hidden">
    <!-- Existing mobile items -->
    <x-responsive-nav-link :href="route('branch.dashboard')" :active="request()->routeIs('branch.dashboard')">
        {{ __('Dashboard') }}
    </x-responsive-nav-link>

    <x-responsive-nav-link :href="route('branch.products.index')" :active="request()->routeIs('branch.products.*')">
        {{ __('Products') }}
    </x-responsive-nav-link>

    <x-responsive-nav-link :href="route('branch.chat-orders.index')" :active="request()->routeIs('branch.chat-orders.*')">
        {{ __('Chat Orders') }}
    </x-responsive-nav-link>

    <!-- New mobile navigation items -->
    <x-responsive-nav-link :href="route('branch.delivery-boys.index')" :active="request()->routeIs('branch.delivery-boys.*')">
        {{ __('Delivery Boys') }}
    </x-responsive-nav-link>

    <x-responsive-nav-link :href="route('branch.order-assignments.index')" :active="request()->routeIs('branch.order-assignments.*')">
        {{ __('Order Assignments') }}
    </x-responsive-nav-link>

    <!-- ... other existing mobile items ... -->
</div> 