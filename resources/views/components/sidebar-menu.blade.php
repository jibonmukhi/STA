@props(['user'])

@php
    $userRoles = $user->roles->pluck('name')->toArray();
    $menuConfig = config('menu');
    $userMenu = [];
    $addedItems = []; // Track added items to avoid duplicates

    // Determine primary role for menu selection
    $primaryRole = null;
    $rolePriority = ['sta_manager', 'company_manager', 'teacher', 'end_user'];

    foreach ($rolePriority as $role) {
        if (in_array($role, $userRoles)) {
            $primaryRole = $role;
            break;
        }
    }

    // Use menu from primary role only to avoid conflicts
    if ($primaryRole && isset($menuConfig[$primaryRole])) {
        foreach ($menuConfig[$primaryRole] as $menuItem) {
            // Create unique identifier including submenu structure
            $identifier = isset($menuItem['route']) ? $menuItem['route'] : $menuItem['title'];
            if (isset($menuItem['submenu'])) {
                $identifier .= '_with_submenu';
            }

            // Only add if not already added and user has permission
            if (!in_array($identifier, $addedItems) &&
                (!isset($menuItem['permission']) || $user->can($menuItem['permission']))) {
                $userMenu[] = $menuItem;
                $addedItems[] = $identifier;
            }
        }
    }
@endphp

@foreach ($userMenu as $menuItem)
    @if (isset($menuItem['submenu']))
            <!-- Menu with Submenu -->
            <div class="nav-section">
                <div class="nav-item">
                    @php
                        $parentRouteUrl = '#';
                        try {
                            if (isset($menuItem['route'])) {
                                $parentRouteUrl = route($menuItem['route']);
                            }
                        } catch (Exception $e) {
                            // Route doesn't exist, keep # as fallback
                        }
                    @endphp
                    <a href="{{ $parentRouteUrl }}" class="nav-link" onclick="toggleSubmenu('{{ Str::slug($menuItem['title']) }}Submenu', event)" data-tooltip="{{ is_array(__($menuItem['title'])) ? (is_string($menuItem['title']) ? $menuItem['title'] : 'Menu Item') : __($menuItem['title']) }}">
                        <div class="nav-icon">
                            <i class="{{ $menuItem['icon'] }}"></i>
                        </div>
                        <span class="nav-text">
                            {{ is_array(__($menuItem['title'])) ? (is_string($menuItem['title']) ? $menuItem['title'] : 'Menu Item') : __($menuItem['title']) }}
                        </span>
                        <i class="fas fa-chevron-down nav-toggle" id="{{ Str::slug($menuItem['title']) }}Toggle"></i>
                    </a>
                    <div class="nav-submenu" id="{{ Str::slug($menuItem['title']) }}Submenu">
                        @foreach ($menuItem['submenu'] as $subItem)
                            @if (!isset($subItem['permission']) || $user->can($subItem['permission']))
                                @php
                                    $routeUrl = '#';
                                    $isActive = false;
                                    try {
                                        if (isset($subItem['route'])) {
                                            $routeUrl = route($subItem['route']);
                                            // Check exact route match or routes that start with the same prefix
                                            $isActive = request()->routeIs($subItem['route']) || request()->routeIs($subItem['route'] . '.*');
                                        }
                                    } catch (Exception $e) {
                                        // Route doesn't exist, use # as fallback
                                    }
                                @endphp
                                <a href="{{ $routeUrl }}" class="nav-link {{ $isActive ? 'active' : '' }}">
                                    <div class="nav-icon">
                                        <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                    </div>
                                    <span class="nav-text">
                                        {{ is_array(__($subItem['title'])) ? (is_string($subItem['title']) ? $subItem['title'] : 'Menu Item') : __($subItem['title']) }}
                                    </span>
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
                @php
                    $mainRouteUrl = '#';
                    $mainIsActive = false;
                    try {
                        if (isset($menuItem['route'])) {
                            $mainRouteUrl = route($menuItem['route']);
                            // Check exact route match or routes that start with the same prefix
                            $mainIsActive = request()->routeIs($menuItem['route']) || request()->routeIs($menuItem['route'] . '.*');
                        }
                    } catch (Exception $e) {
                        // Route doesn't exist, use # as fallback
                    }
                @endphp
                <a href="{{ $mainRouteUrl }}" class="nav-link {{ $mainIsActive ? 'active' : '' }}" data-tooltip="{{ is_array(__($menuItem['title'])) ? (is_string($menuItem['title']) ? $menuItem['title'] : 'Menu Item') : __($menuItem['title']) }}">
                    <div class="nav-icon">
                        <i class="{{ $menuItem['icon'] }}"></i>
                    </div>
                    <span class="nav-text">
                        {{ is_array(__($menuItem['title'])) ? (is_string($menuItem['title']) ? $menuItem['title'] : 'Menu Item') : __($menuItem['title']) }}
                    </span>
                </a>
            </div>
        </div>
    @endif
@endforeach

<!-- Profile & Logout (Available to all roles) -->
<div class="nav-section">
    <div class="nav-item">
        <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" data-tooltip="{{ is_array(__('navigation.profile')) ? 'Profile' : __('navigation.profile') }}">
            <div class="nav-icon">
                <i class="fas fa-user-cog"></i>
            </div>
            <span class="nav-text">{{ is_array(__('navigation.profile')) ? 'Profile' : __('navigation.profile') }}</span>
        </a>
    </div>
</div>

<div class="nav-section logout-btn">
    <div class="nav-item">
        <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
            @csrf
            <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start" data-tooltip="@php $tooltipLogout = __('navigation.logout'); echo is_array($tooltipLogout) ? 'Logout' : $tooltipLogout; @endphp">
                <div class="nav-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <span class="nav-text">
                    @php
                        $translatedLogout = __('navigation.logout');
                        echo is_array($translatedLogout) ? 'Logout' : $translatedLogout;
                    @endphp
                </span>
            </button>
        </form>
    </div>
</div>