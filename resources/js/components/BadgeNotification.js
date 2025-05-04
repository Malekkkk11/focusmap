export default class BadgeNotification {
    constructor() {
        this.bindEvents();
    }

    bindEvents() {
        window.Echo.private(`App.Models.User.${window.userId}`)
            .notification((notification) => {
                if (notification.type === 'App\\Notifications\\BadgeEarned') {
                    this.showBadgeNotification(notification.badge);
                }
            });
    }

    showBadgeNotification(badge) {
        const html = `
            <div class="toast-header">
                <i class="bi bi-${badge.icon} text-primary me-2"></i>
                <strong class="me-auto">New Badge Earned!</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                <h6 class="mb-1">${badge.name}</h6>
                <p class="mb-0 text-muted small">${badge.description}</p>
            </div>
        `;

        const toastElement = document.createElement('div');
        toastElement.className = 'toast show';
        toastElement.setAttribute('role', 'alert');
        toastElement.innerHTML = html;

        const container = document.getElementById('toast-container');
        if (!container) {
            const newContainer = document.createElement('div');
            newContainer.id = 'toast-container';
            newContainer.className = 'position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(newContainer);
        }

        document.getElementById('toast-container').appendChild(toastElement);

        // Remove the toast after 5 seconds
        setTimeout(() => {
            toastElement.remove();
        }, 5000);

        // Play a success sound
        const audio = new Audio('/audio/achievement.mp3');
        audio.play().catch(() => {
            // Handle browsers that block autoplay
            console.log('Audio playback was prevented');
        });
    }
}