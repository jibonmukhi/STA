@props(['user'])

@php
    $userRoles = $user->roles->pluck('name')->toArray();
    $menuConfig = config('menu');
    $userMenu = [];

    // Get menu items for user's roles
    foreach ($userRoles as $role) {
        if (isset($menuConfig[$role])) {
            $userMenu = array_merge($userMenu, $menuConfig[$role]);
            break; // Use the first matching role's menu
        }
    }
@endphp

@foreach ($userMenu as $menuItem)
    @if (isset($menuItem['permission']) && $user->can($menuItem['permission']))
        @if (isset($menuItem['submenu']))
            <!-- Menu with Submenu -->
            <div class="nav-section">
                <div class="nav-item">
                    <a href="#" class="nav-link" onclick="toggleSubmenu('{{ Str::slug($menuItem['title']) }}Submenu', event)" data-tooltip="{{ $menuItem['title'] }}">
                        <div class="nav-icon">
                            <i class="{{ $menuItem['icon'] }}"></i>
                        </div>
                        <span class="nav-text">{{ $menuItem['title'] }}</span>
                        <i class="fas fa-chevron-down nav-toggle" id="{{ Str::slug($menuItem['title']) }}Toggle"></i>
                    </a>
                    <div class="nav-submenu" id="{{ Str::slug($menuItem['title']) }}Submenu">
                        @foreach ($menuItem['submenu'] as $subItem)
                            @if (isset($subItem['permission']) && $user->can($subItem['permission']))
                                <a href="{{ route($subItem['route']) }}" class="nav-link {{ request()->routeIs($subItem['route']) ? 'active' : '' }}">
                                    <div class="nav-icon">
                                        <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                    </div>
                                    <span class="nav-text">{{ $subItem['title'] }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <!-- Simple Menu Item -->
            <div class="nav-section">
                <div class="nav-item">
                    <a href="{{ route($menuItem['route']) }}" class="nav-link {{ request()->routeIs($menuItem['route']) ? 'active' : '' }}" data-tooltip="{{ $menuItem['title'] }}">
                        <div class="nav-icon">
                            <i class="{{ $menuItem['icon'] }}"></i>
                        </div>
                        <span class="nav-text">{{ $menuItem['title'] }}</span>
                    </a>
                </div>
            </div>
        @endif
    @endif
@endforeach

<!-- Profile & Logout (Available to all roles) -->
<div class="nav-section">
    <div class="nav-item">
        <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" data-tooltip="Profile Settings">
            <div class="nav-icon">
                <i class="fas fa-user-cog"></i>
            </div>
            <span class="nav-text">Profile Settings</span>
        </a>
    </div>
</div>

<div class="nav-section logout-btn">
    <div class="nav-item">
        <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
            @csrf
            <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start" data-tooltip="Logout">
                <div class="nav-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <span class="nav-text">Logout</span>
            </button>
        </form>
    </div>
</div>