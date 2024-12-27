<div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 pb-4">
    <div class="flex h-16 shrink-0 items-center">
        <img class="h-8 w-auto" src="{{ asset('images/logo-white.svg') }}" alt="Logo">
    </div>
    <nav class="flex flex-1 flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-7">
            <li>
                <ul role="list" class="-mx-2 space-y-1">
                    <!-- Dashboard -->
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

                    <!-- Courses -->
                    <li>
                        <div x-data="{ open: {{ request()->routeIs('admin.courses.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" 
                                    class="{{ request()->routeIs('admin.courses.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex items-center justify-between w-full rounded-md p-2 text-sm leading-6 font-semibold">
                                <div class="flex gap-x-3">
                                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                                    </svg>
                                    Courses
                                </div>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-90': open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            
                            <div x-show="open" class="mt-1 space-y-1" style="display: none;">
                                <a href="{{ route('admin.courses.index') }}" 
                                   class="{{ request()->routeIs('admin.courses.index') ? 'bg-gray-700' : '' }} text-gray-400 hover:text-white hover:bg-gray-700 group flex gap-x-3 rounded-md p-2 pl-11 text-sm leading-6 font-semibold">
                                    All Courses
                                </a>
                                
                                <!-- <a href="{{ route('admin.courses.index') }}?view=exams" 
                                   class="{{ request()->has('view') && request('view') === 'exams' ? 'bg-gray-700' : '' }} text-gray-400 hover:text-white hover:bg-gray-700 group flex gap-x-3 rounded-md p-2 pl-11 text-sm leading-6 font-semibold">
                                    All Exams
                                </a> -->
<!--                                 
                                @if(request()->route('course'))
                                    <a href="{{ route('admin.courses.exams.index', request()->route('course')) }}" 
                                       class="{{ request()->routeIs('admin.courses.exams.*') ? 'bg-gray-700' : '' }} text-gray-400 hover:text-white hover:bg-gray-700 group flex gap-x-3 rounded-md p-2 pl-11 text-sm leading-6 font-semibold">
                                        Course Exams
                                    </a>
                                @endif -->
                            </div>
                        </div>
                    </li>

                    <!-- Products -->
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

                    <!-- Shipping -->
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

                    <!-- Users Management -->
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

                    <!-- Business -->
                    <li>
                        <a href="{{ route('admin.businesses.index') }}" 
                           class="{{ request()->routeIs('admin.businesses.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold"
                        >
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                            </svg>
                            Business
                        </a>
                    </li>

                    <!-- Professionals -->
                    <li>
                        <a href="{{ route('admin.professionals.index') }}" 
                           class="{{ request()->routeIs('admin.professionals.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold"
                        >
                            <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" />
                            </svg>
                            Professionals
                        </a>
                    </li>

                    <!-- Services -->
                    <li>
                        <div x-data="{ open: {{ request()->routeIs('admin.services.*') || request()->routeIs('admin.service-categories.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" 
                                    class="{{ request()->routeIs('admin.services.*') || request()->routeIs('admin.service-categories.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex items-center justify-between w-full rounded-md p-2 text-sm leading-6 font-semibold">
                                <div class="flex gap-x-3">
                                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z" />
                                    </svg>
                                    Services
                                </div>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-90': open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            
                            <div x-show="open" class="mt-1 space-y-1" style="display: none;">
                                <a href="{{ route('admin.services.index') }}" 
                                   class="{{ request()->routeIs('admin.services.index') ? 'bg-gray-700' : '' }} text-gray-400 hover:text-white hover:bg-gray-700 group flex gap-x-3 rounded-md p-2 pl-11 text-sm leading-6 font-semibold">
                                    All Services
                                </a>
                                <a href="{{ route('admin.service-categories.index') }}" 
                                   class="{{ request()->routeIs('admin.service-categories.*') ? 'bg-gray-700' : '' }} text-gray-400 hover:text-white hover:bg-gray-700 group flex gap-x-3 rounded-md p-2 pl-11 text-sm leading-6 font-semibold">
                                    Categories
                                </a>
                            </div>
                        </div>
                    </li>

                    <!-- Orders -->
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

                    <!-- Transactions -->
                    <li>
                        <div x-data="{ open: {{ request()->routeIs('admin.transactions.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" 
                                    class="{{ request()->routeIs('admin.transactions.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex items-center justify-between w-full rounded-md p-2 text-sm leading-6 font-semibold">
                                <div class="flex gap-x-3">
                                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                                    </svg>
                                    Transactions
                                </div>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-90': open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            
                            <div x-show="open" class="mt-1 space-y-1" style="display: none;">
                                <a href="{{ route('admin.transactions.index') }}" 
                                   class="{{ request()->routeIs('admin.transactions.index') ? 'bg-gray-700' : '' }} text-gray-400 hover:text-white hover:bg-gray-700 group flex gap-x-3 rounded-md p-2 pl-11 text-sm leading-6 font-semibold">
                                    All Transactions
                                </a>
                            </div>
                        </div>
                    </li>

                    <!-- Bank Accounts -->
                    <li>
                        <div x-data="{ open: {{ request()->routeIs('admin.bank-accounts.*') ? 'true' : 'false' }} }">
                            <button @click="open = !open" 
                                    class="{{ request()->routeIs('admin.bank-accounts.*') ? 'bg-gray-800 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }} group flex items-center justify-between w-full rounded-md p-2 text-sm leading-6 font-semibold">
                                <div class="flex gap-x-3">
                                    <svg class="h-6 w-6 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                                    </svg>
                                    Bank Accounts
                                </div>
                                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-90': open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            
                            <div x-show="open" class="mt-1 space-y-1" style="display: none;">
                                <a href="{{ route('admin.bank-accounts.index') }}" 
                                   class="{{ request()->routeIs('admin.bank-accounts.index') ? 'bg-gray-700' : '' }} text-gray-400 hover:text-white hover:bg-gray-700 group flex gap-x-3 rounded-md p-2 pl-11 text-sm leading-6 font-semibold">
                                    All Bank Accounts
                                </a>
                            </div>
                        </div>
                    </li>

                    <!-- Settings -->
                    <li>
                        <x-admin.nav-link
                    </li>
                </ul>
            </li>
            
            <!-- Logout Button -->
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