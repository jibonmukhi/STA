<!-- Notification Dropdown Component -->
<div class="dropdown me-3" id="notificationDropdown">
    <button class="btn btn-link p-2 position-relative" type="button" data-bs-toggle="dropdown" id="notificationButton">
        <i class="fas fa-bell"></i>
        <span class="badge bg-danger badge-sm position-absolute hide" id="notificationBadge">0</span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end notification-dropdown-menu" id="notificationMenu">
        <li class="notification-header">
            <div class="d-flex justify-content-between align-items-center px-3 py-2">
                <h6 class="mb-0"><i class="fas fa-bell me-2"></i>{{ __('notifications.title') }}</h6>
                <button class="btn btn-sm btn-link text-primary p-0" id="markAllReadBtn" style="font-size: 0.8rem; display: none;">
                    {{ __('notifications.mark_all_read') }}
                </button>
            </div>
        </li>
        <div class="notification-list-scroll" id="notificationListContainer">
            <div id="notificationList">
                <li>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-bell-slash fa-2x mb-2"></i>
                        <p class="mb-0">{{ __('notifications.no_notifications') }}</p>
                    </div>
                </li>
            </div>
        </div>
        <li class="notification-footer">
            <hr class="dropdown-divider my-0">
            <a class="dropdown-item text-center" href="{{ route('notifications.index') }}" tabindex="-1">
                <i class="fas fa-eye me-2"></i>{{ __('notifications.view_all') }}
            </a>
        </li>
    </ul>
</div>

<style>
    /* Notification dropdown container */
    .notification-dropdown-menu {
        min-width: 350px;
        max-height: 500px;
        padding: 0 !important;
        overflow: hidden !important;
    }

    /* Only apply flex when dropdown is shown */
    .notification-dropdown-menu.show {
        display: flex !important;
        flex-direction: column;
    }

    /* Fixed header */
    .notification-header {
        flex-shrink: 0;
        border-bottom: 1px solid #f0f0f0;
        background: white;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    /* Scrollable notification list */
    .notification-list-scroll {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
        max-height: 380px;
        scroll-behavior: smooth;
    }

    /* Fixed footer */
    .notification-footer {
        flex-shrink: 0;
        background: white;
        position: sticky;
        bottom: 0;
        z-index: 10;
    }

    .notification-footer .dropdown-item {
        border-top: 1px solid #f0f0f0;
    }

    /* Notification-specific styles */
    #notificationMenu .dropdown-item {
        white-space: normal;
        padding: 0.75rem 1rem;
    }

    #notificationMenu .notification-item {
        display: flex;
        align-items: start;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #f8f9fa;
        transition: all 0.2s ease;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
    }

    #notificationMenu .notification-item:hover {
        background: linear-gradient(135deg, #4f46e5, #3730a3);
        color: white;
        transform: translateX(5px);
    }

    #notificationMenu .notification-item.unread {
        background-color: #f0f4ff;
    }

    #notificationMenu .notification-icon {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.9rem;
    }

    #notificationMenu .notification-icon.bg-warning {
        background-color: #fef3c7 !important;
        color: #f59e0b;
    }

    #notificationMenu .notification-icon.bg-success {
        background-color: #d1fae5 !important;
        color: #10b981;
    }

    #notificationMenu .notification-icon.bg-danger {
        background-color: #fee2e2 !important;
        color: #ef4444;
    }

    #notificationMenu .notification-icon.bg-info {
        background-color: #dbeafe !important;
        color: #0ea5e9;
    }

    #notificationMenu .notification-icon.bg-primary {
        background-color: #e0e7ff !important;
        color: #4f46e5;
    }

    #notificationMenu .notification-content {
        flex: 1;
        min-width: 0;
    }

    #notificationMenu .notification-title {
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
        line-height: 1.3;
    }

    #notificationMenu .notification-message {
        font-size: 0.8rem;
        color: #6b7280;
        margin-bottom: 0.25rem;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    #notificationMenu .notification-item:hover .notification-message {
        color: rgba(255, 255, 255, 0.9);
    }

    #notificationMenu .notification-time {
        font-size: 0.75rem;
        color: #9ca3af;
    }

    #notificationMenu .notification-item:hover .notification-time {
        color: rgba(255, 255, 255, 0.8);
    }

    /* Badge styling */
    #notificationBadge {
        top: 0;
        right: 0;
        font-size: 0.65rem;
        min-width: 18px;
        height: 18px;
        padding: 0.2rem 0.4rem;
        border-radius: 10px;
        line-height: 1;
    }

    /* Badge animation - only when visible */
    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
        100% {
            transform: scale(1);
        }
    }

    #notificationBadge.show {
        display: block !important;
        animation: pulse 2s infinite;
    }

    #notificationBadge.hide {
        display: none !important;
    }

    /* Scrollbar styling for notification list */
    .notification-list-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .notification-list-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .notification-list-scroll::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .notification-list-scroll::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Prevent focus scroll on footer link */
    .notification-footer a:focus {
        outline: 2px solid #4f46e5;
        outline-offset: -2px;
    }
</style>

<script>
    // Notification management
    let notificationCheckInterval;

    document.addEventListener('DOMContentLoaded', function() {
        // Load notifications immediately
        loadNotifications();

        // Refresh notifications every 30 seconds
        notificationCheckInterval = setInterval(loadNotifications, 30000);

        // Mark all as read button
        document.getElementById('markAllReadBtn').addEventListener('click', function(e) {
            e.preventDefault();
            markAllAsRead();
        });

        // Prevent auto-scroll when dropdown opens
        const notificationMenu = document.getElementById('notificationMenu');
        const notificationButton = document.getElementById('notificationButton');
        const notificationListContainer = document.getElementById('notificationListContainer');

        notificationButton.addEventListener('click', function() {
            // Reset scroll position to top when opening dropdown
            setTimeout(function() {
                if (notificationListContainer) {
                    notificationListContainer.scrollTop = 0;
                }
            }, 0);
        });

        // Prevent scroll when dropdown is shown
        if (notificationMenu) {
            notificationMenu.addEventListener('shown.bs.dropdown', function() {
                if (notificationListContainer) {
                    notificationListContainer.scrollTop = 0;
                }
            });
        }
    });

    function loadNotifications() {
        fetch('{{ route('notifications.recent') }}', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            updateNotificationBadge(data.unread_count);
            renderNotifications(data.notifications);
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
        });
    }

    function updateNotificationBadge(count) {
        const badge = document.getElementById('notificationBadge');
        const markAllBtn = document.getElementById('markAllReadBtn');

        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : count;
            badge.classList.remove('hide');
            badge.classList.add('show');
            markAllBtn.style.display = 'block';
        } else {
            badge.classList.remove('show');
            badge.classList.add('hide');
            markAllBtn.style.display = 'none';
        }
    }

    function renderNotifications(notifications) {
        const list = document.getElementById('notificationList');

        if (notifications.length === 0) {
            list.innerHTML = `
                <li>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-bell-slash fa-2x mb-2"></i>
                        <p class="mb-0">{{ __('notifications.no_new_notifications') }}</p>
                    </div>
                </li>
            `;
            return;
        }

        list.innerHTML = notifications.map(notification => `
            <li>
                <a href="${notification.action_url}"
                   class="notification-item unread"
                   data-notification-id="${notification.id}"
                   onclick="markAsReadAndRedirect(event, '${notification.id}', '${notification.action_url}')">
                    <div class="notification-icon bg-${notification.color}">
                        <i class="${notification.icon}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${notification.title}</div>
                        <div class="notification-message">${notification.message}</div>
                        <div class="notification-time">${notification.created_at}</div>
                    </div>
                </a>
            </li>
        `).join('');
    }

    function markAsReadAndRedirect(event, notificationId, actionUrl) {
        event.preventDefault();

        fetch(`{{ route('notifications.mark-as-read', ':id') }}`.replace(':id', notificationId), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload notifications
                loadNotifications();

                // Redirect to action URL
                if (actionUrl && actionUrl !== '#') {
                    window.location.href = actionUrl;
                }
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
            // Still redirect even if marking fails
            if (actionUrl && actionUrl !== '#') {
                window.location.href = actionUrl;
            }
        });
    }

    function markAllAsRead() {
        fetch('{{ route('notifications.mark-all-read') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications();
            }
        })
        .catch(error => {
            console.error('Error marking all notifications as read:', error);
        });
    }

    // Cleanup interval when page is unloaded
    window.addEventListener('beforeunload', function() {
        if (notificationCheckInterval) {
            clearInterval(notificationCheckInterval);
        }
    });
</script>
