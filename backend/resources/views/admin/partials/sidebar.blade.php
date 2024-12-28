@php
    use App\Enums\AdminRole;
    $admin = Auth::guard('admin')->user();
@endphp

<div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 pb-4">
    <div class="flex h-16 shrink-0 items-center">
        <img class="h-8 w-auto" src="{{ asset('images/logo-white.svg') }}" alt="Logo">
    </div>
    <nav class="flex flex-1 flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-7">
            <li>
                <ul role="list" class="-mx-2 space-y-1">
                    <!-- Dashboard - visible to all -->
                    <li>
                        <a href="{{ route('admin.dashboard') }}" 
                           class="{{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold"
                        >
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>
                            Dashboard
                        </a>
                    </li>

                    <!-- Products - visible to sales -->
                    @if($admin->hasRole(AdminRole::SALES) || $admin->isSuperAdmin())
                    <li>
                        <div x-data="{ open: {{ request()->routeIs('admin.products.*') || request()->routeIs('admin.product-categories.*') || request()->routeIs('admin.brands.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" 
                                    class="{{ request()->routeIs('admin.products.*') || request()->routeIs('admin.product-categories.*') || request()->routeIs('admin.brands.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex items-center justify-between w-full rounded-md p-2 text-sm leading-6 font-semibold">
                                <div class="flex gap-x-3">
                                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                    </svg>
                                    Products
                                </div>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-90': open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            
                            <div x-show="open" class="mt-1 space-y-1" style="display: none;">
                                <a href="{{ route('admin.products.index') }}" 
                                   class="{{ request()->routeIs('admin.products.index') ? 'bg-gray-700' : '' }} text-gray-400 hover:text-white hover:bg-gray-700 group flex gap-x-3 rounded-md p-2 pl-11 text-sm leading-6 font-semibold">
                                    All Products
                                </a>
                                <a href="{{ route('admin.product-categories.index') }}" 
                                   class="{{ request()->routeIs('admin.product-categories.*') ? 'bg-gray-700' : '' }} text-gray-400 hover:text-white hover:bg-gray-700 group flex gap-x-3 rounded-md p-2 pl-11 text-sm leading-6 font-semibold">
                                    Categories
                                </a>
                                <a href="{{ route('admin.brands.index') }}" 
                                   class="{{ request()->routeIs('admin.brands.*') ? 'bg-gray-700' : '' }} text-gray-400 hover:text-white hover:bg-gray-700 group flex gap-x-3 rounded-md p-2 pl-11 text-sm leading-6 font-semibold">
                                    Brands
                                </a>
                            </div>
                        </div>
                    </li>
                    @endif

                    <!-- Shipping - visible to sales -->
                    @if($admin->hasRole(AdminRole::SALES) || $admin->isSuperAdmin())
                    <li>
                        <div x-data="{ open: {{ request()->routeIs('admin.shipping.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" 
                                    class="{{ request()->routeIs('admin.shipping.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex items-center justify-between w-full rounded-md p-2 text-sm leading-6 font-semibold">
                                <div class="flex gap-x-3">
                                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                                    </svg>
                                    Shipping
                                </div>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-90': open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            
                            <div x-show="open" class="mt-1 space-y-1" style="display: none;">
                                <a href="{{ route('admin.shipping.zones.index') }}" 
                                   class="{{ request()->routeIs('admin.shipping.zones.*') ? 'bg-gray-700' : '' }} text-gray-400 hover:text-white hover:bg-gray-700 group flex gap-x-3 rounded-md p-2 pl-11 text-sm leading-6 font-semibold">
                                    Zones
                                </a>
                                <a href="{{ route('admin.shipping.methods.index') }}" 
                                   class="{{ request()->routeIs('admin.shipping.methods.*') ? 'bg-gray-700' : '' }} text-gray-400 hover:text-white hover:bg-gray-700 group flex gap-x-3 rounded-md p-2 pl-11 text-sm leading-6 font-semibold">
                                    Methods
                                </a>
                                <a href="{{ route('admin.shipping.addresses.index') }}" 
                                   class="{{ request()->routeIs('admin.shipping.addresses.*') ? 'bg-gray-700' : '' }} text-gray-400 hover:text-white hover:bg-gray-700 group flex gap-x-3 rounded-md p-2 pl-11 text-sm leading-6 font-semibold">
                                    Addresses
                                </a>
                            </div>
                        </div>
                    </li>
                    @endif

                    <!-- Orders - visible to sales -->
                    @if($admin->hasRole(AdminRole::SALES) || $admin->isSuperAdmin())
                    <li>
                        <x-admin.nav-link 
                            href="{{ route('admin.orders.index') }}"
                            :active="request()->routeIs('admin.orders.*')"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                            <span>Orders</span>
                        </x-admin.nav-link>
                    </li>
                    @endif

                    <!-- Users Management - visible to finance -->
                    @if($admin->hasRole(AdminRole::FINANCE) || $admin->isSuperAdmin())
                    <li>
                        <a href="{{ route('admin.users.index') }}" 
                           class="{{ request()->routeIs('admin.users.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold"
                        >
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                            </svg>
                            Users
                        </a>
                    </li>
                    @endif

                    <!-- Transactions - visible to finance -->
                    @if($admin->hasRole(AdminRole::FINANCE) || $admin->isSuperAdmin())
                    <li>
                        <a href="{{ route('admin.transactions.index') }}" 
                           class="{{ request()->routeIs('admin.transactions.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold"
                        >
                                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                                    </svg>
                                    Transactions
                        </a>
                    </li>
                    @endif

                    <!-- Bank Accounts - visible to finance -->
                    @if($admin->hasRole(AdminRole::FINANCE) || $admin->isSuperAdmin())
                    <li>
                        <a href="{{ route('admin.bank-accounts.index') }}" 
                           class="{{ request()->routeIs('admin.bank-accounts.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold"
                        >
                                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                    </svg>
                                    Bank Accounts
                        </a>
                    </li>
                    @endif

                    <!-- Hide all other menu items for sales role -->
                    @unless($admin->hasRole(AdminRole::SALES))
                    <!-- All other menu items -->
                    <!-- ... keep existing menu items ... -->
                    @endunless

                </ul>
            </li>
            
            <!-- Logout Button - visible to all -->
            <li class="mt-auto">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-400 hover:bg-gray-800 hover:text-white w-full">
                        <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                        Logout
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</div> 