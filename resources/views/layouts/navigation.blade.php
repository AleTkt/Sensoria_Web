<nav x-data="{ open: false }" class="glass-surface w-full md:w-64 flex-shrink-0 flex flex-col p-4">

    <!-- Logo -->
    <div class="flex items-center justify-between mb-8 px-2">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <!-- Icono usando el verde primario de Sensoría -->
            <svg class="w-8 h-8" style="color: var(--color-primary);" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            <span class="text-xl font-bold tracking-tight" style="color: var(--color-tertiary);">SENSORÍA</span>
        </a>

        <!-- Hamburguesa Móvil -->
        <button @click="open = ! open" class="md:hidden focus:outline-none" style="color: var(--color-tertiary);">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Enlaces -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden md:flex flex-col flex-1 gap-2">

        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors"
           style="{{ request()->routeIs('dashboard') ? 'background-color: var(--color-primary); color: white;' : 'color: var(--color-tertiary);' }}">
            Panel de Control
        </a>

        <!-- Mi Empresa -->
        <a href="{{ route('company.edit') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors"
           style="{{ request()->routeIs('company.*') ? 'background-color: var(--color-primary); color: white;' : 'color: var(--color-tertiary);' }}">
            Mi Empresa
        </a>

        <!-- Mi Equipo -->
        <a href="{{ route('company.team') }}" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors"
           style="{{ request()->routeIs('company.team') ? 'background-color: var(--color-primary); color: white;' : 'color: var(--color-tertiary);' }}">
            Mi Equipo
        </a>

        <div class="mt-auto pt-6 border-t" style="border-color: var(--glass-border);">

            <!-- Distintivo Tenant -->
            @php
                $currentCompany = session()->has('current_company_id') ? Auth::user()->companies()->find(session('current_company_id')) : null;
            @endphp
            @if($currentCompany)
                <div class="mb-4 px-3 py-2 text-xs font-semibold rounded" style="background-color: rgba(88, 148, 69, 0.1); color: var(--color-secondary);">
                    {{ $currentCompany->trade_name }}
                </div>
            @endif

            <!-- Perfil / Logout -->
            <div class="flex items-center justify-between px-2">
                <div class="flex flex-col">
                    <span class="text-sm font-semibold" style="color: var(--color-tertiary);">{{ Auth::user()->name }}</span>
                    <a href="{{ route('profile.edit') }}" class="text-xs hover:underline" style="color: var(--color-secondary);">Ver Perfil</a>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="hover:opacity-75 transition-opacity" style="color: var(--color-tertiary);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
