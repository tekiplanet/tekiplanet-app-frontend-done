<div class="flex grow flex-col gap-y-5 overflow-y-auto bg-gray-900 px-6 pb-4">
    <div class="flex h-16 shrink-0 items-center">
        <img class="h-8 w-auto" src="{{ asset('images/logo-white.svg') }}" alt="Logo">
    </div>
    <nav class="flex flex-1 flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-7">
            <li>
                <ul role="list" class="-mx-2 space-y-1">
                    <x-admin.nav-link 
                        :href="route('admin.dashboard')" 
                        :active="request()->routeIs('admin.dashboard')"
                        icon="home"
                    >
                        Dashboard
                    </x-admin.nav-link>

                    @can('view', App\Models\User::class)
                        <x-admin.nav-link 
                            :href="route('admin.users.index')" 
                            :active="request()->routeIs('admin.users.*')"
                            icon="users"
                        >
                            Users
                        </x-admin.nav-link>
                    @endcan

                    @can('view', App\Models\Course::class)
                        <x-admin.nav-link 
                            :href="route('admin.courses.index')" 
                            :active="request()->routeIs('admin.courses.*')"
                            icon="academic-cap"
                        >
                            Courses
                        </x-admin.nav-link>
                    @endcan

                    <!-- Add more menu items based on roles -->
                </ul>
            </li>
            
            <li class="mt-auto">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6 text-gray-400 hover:bg-gray-800 hover:text-white">
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