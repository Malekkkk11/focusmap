import './bootstrap';
import BadgeNotification from './components/BadgeNotification';

// Initialize badge notifications
if (document.querySelector('meta[name="user-id"]')) {
    window.userId = document.querySelector('meta[name="user-id"]').content;
    new BadgeNotification();
}

// Initialize tooltip and popover functionality
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function(popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
});

// Step reordering functionality
let dragSrcEl = null;

function handleDragStart(e) {
    dragSrcEl = this;
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/html', this.innerHTML);
    this.classList.add('dragging');
}

function handleDragOver(e) {
    if (e.preventDefault) {
        e.preventDefault();
    }
    e.dataTransfer.dropEffect = 'move';
    return false;
}

function handleDragEnter(e) {
    this.classList.add('drag-over');
}

function handleDragLeave(e) {
    this.classList.remove('drag-over');
}

function handleDrop(e) {
    if (e.stopPropagation) {
        e.stopPropagation();
    }

    if (dragSrcEl != this) {
        // Swap the order values
        const srcOrder = dragSrcEl.dataset.order;
        const destOrder = this.dataset.order;
        
        // Update the UI
        dragSrcEl.dataset.order = destOrder;
        this.dataset.order = srcOrder;

        // Send AJAX request to update orders
        updateStepOrders([
            { id: dragSrcEl.dataset.stepId, order: destOrder },
            { id: this.dataset.stepId, order: srcOrder }
        ]);
    }
    return false;
}

function handleDragEnd(e) {
    this.classList.remove('dragging');
    document.querySelectorAll('.step-item').forEach(item => {
        item.classList.remove('drag-over');
    });
}

// Initialize drag and drop for step items
document.querySelectorAll('.step-item').forEach(item => {
    item.addEventListener('dragstart', handleDragStart);
    item.addEventListener('dragenter', handleDragEnter);
    item.addEventListener('dragover', handleDragOver);
    item.addEventListener('dragleave', handleDragLeave);
    item.addEventListener('drop', handleDrop);
    item.addEventListener('dragend', handleDragEnd);
});

// Function to update step orders via AJAX
function updateStepOrders(steps) {
    fetch('/steps/reorder', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ steps })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Optionally show success message
            console.log('Steps reordered successfully');
        }
    })
    .catch(error => {
        console.error('Error reordering steps:', error);
    });
}

// Handle goal completion celebrations
function celebrateGoalCompletion() {
    const confetti = document.createElement('canvas');
    confetti.style.position = 'fixed';
    confetti.style.top = '0';
    confetti.style.left = '0';
    confetti.style.width = '100vw';
    confetti.style.height = '100vh';
    confetti.style.pointerEvents = 'none';
    confetti.style.zIndex = '9999';
    document.body.appendChild(confetti);

    // Simple confetti animation
    const ctx = confetti.getContext('2d');
    const particles = [];
    const colors = ['#ff6b6b', '#4ecdc4', '#45b7d1', '#96ceb4', '#ffeead'];

    for (let i = 0; i < 100; i++) {
        particles.push({
            x: Math.random() * confetti.width,
            y: 0,
            speed: 2 + Math.random() * 3,
            radius: 3 + Math.random() * 2,
            color: colors[Math.floor(Math.random() * colors.length)]
        });
    }

    function animate() {
        ctx.clearRect(0, 0, confetti.width, confetti.height);

        let complete = true;
        particles.forEach(particle => {
            ctx.beginPath();
            ctx.arc(particle.x, particle.y, particle.radius, 0, Math.PI * 2);
            ctx.fillStyle = particle.color;
            ctx.fill();

            particle.y += particle.speed;

            if (particle.y < confetti.height) {
                complete = false;
            }
        });

        if (complete) {
            document.body.removeChild(confetti);
        } else {
            requestAnimationFrame(animate);
        }
    }

    animate();
}

// Check for goal completion notification
document.addEventListener('DOMContentLoaded', function() {
    const goalCompletedAlert = document.querySelector('.goal-completed-alert');
    if (goalCompletedAlert) {
        celebrateGoalCompletion();
    }
});
