<?php
<div x-data="notifications" 
     x-init="initNotifications"
     class="notifications-dropdown">
    <div class="dropdown">
        <button class="btn btn-link nav-link dropdown-toggle" 
                type="button" 
                id="notificationsDropdown" 
                data-bs-toggle="dropdown">
            <i class="bi bi-bell"></i>
            <span x-show="unreadCount > 0" 
                  x-text="unreadCount"
                  class="badge bg-danger"></span>
        </button>
        <div class="dropdown-menu dropdown-menu-end p-0" style="width: 320px;">
            <div class="p-2 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Notifications</h6>
                <button x-show="unreadCount > 0" 
                        @click="markAllAsRead" 
                        class="btn btn-link btn-sm text-decoration-none">
                    Mark all as read
                </button>
            </div>
            <div class="notifications-list" style="max-height: 400px; overflow-y: auto;">
                <template x-if="notifications.length === 0">
                    <div class="p-3 text-center text-muted">
                        No notifications
                    </div>
                </template>
                <template x-for="notification in notifications" :key="notification.id">
                    <div :class="{'bg-light': !notification.read_at}" 
                         class="notification-item p-2 border-bottom">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="mb-1" x-text="notification.data.message"></p>
                                <small class="text-muted" x-text="formatDate(notification.created_at)"></small>
                            </div>
                            <div x-show="!notification.read_at">
                                <button @click="markAsRead(notification.id)" 
                                        class="btn btn-link btn-sm p-0 text-decoration-none">
                                    <i class="bi bi-check2"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <template x-if="notifications.length > 0">
                <div class="p-2 border-top text-center">
                    <a href="/notifications" class="text-decoration-none">View all</a>
                </div>
            </template>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('notifications', () => ({
        notifications: [],
        unreadCount: 0,

        async initNotifications() {
            await this.fetchNotifications();
            this.listenForNotifications();
        },

        async fetchNotifications() {
            const response = await fetch('/notifications/get');
            const data = await response.json();
            this.notifications = data.notifications;
            this.unreadCount = data.unreadCount;
        },

        listenForNotifications() {
            Echo.private(`App.Models.User.${userId}`)
                .notification((notification) => {
                    this.notifications.unshift(notification);
                    this.unreadCount++;
                });
        },

        async markAsRead(id) {
            await fetch(`/notifications/${id}/mark-as-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const notification = this.notifications.find(n => n.id === id);
            if (notification && !notification.read_at) {
                notification.read_at = new Date();
                this.unreadCount--;
            }
        },

        async markAllAsRead() {
            await fetch('/notifications/mark-all-as-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            this.notifications.forEach(notification => {
                notification.read_at = new Date();
            });
            this.unreadCount = 0;
        },

        formatDate(date) {
            return new Date(date).toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }));
});
</script>
@endpush

@push('styles')
<style>
.notifications-dropdown .dropdown-menu {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.notification-item:hover {
    background-color: rgba(0, 0, 0, 0.025);
}

.notification-item:last-child {
    border-bottom: none !important;
}
</style>
@endpush