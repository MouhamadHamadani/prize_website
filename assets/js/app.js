// Main JavaScript Application for Prize Website

class PrizeApp {
    constructor() {
        this.apiBase = '/api';
        this.isSpinning = false;
        this.prizes = [];
        this.init();
    }

    init() {
        this.bindEvents();
        this.loadPrizes();
    }

    bindEvents() {
        // Bind common events
        $(document).on('click', '.spin-wheel-btn', () => this.spinWheel());
        $(document).on('click', '.open-box-btn', () => this.openBox());
        $(document).on('click', '.close-modal', () => this.closeModal());
    }

    async loadPrizes(method = 'wheel') {
        try {
            const response = await $.get(`${this.apiBase}/prizes.php?method=${method}`);
            if (response.success) {
                this.prizes = response.data.prizes;
                return this.prizes;
            }
        } catch (error) {
            console.error('Failed to load prizes:', error);
        }
        return [];
    }

    async spinWheel() {
        if (this.isSpinning) return;
        
        this.isSpinning = true;
        const $button = $('.spin-wheel-btn');
        
        // Disable button and show loading
        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Spinning...');
        
        try {
            // Make API call to get prize first
            const response = await $.post(`${this.apiBase}/spin.php`, { method: 'wheel' });
            
            if (response.success) {
                const wonPrize = response.data.prize;
                
                // Find the index of the won prize in the prizes array
                const prizeIndex = this.prizes.findIndex(prize => prize.id === wonPrize.id);
                
                if (prizeIndex !== -1) {
                    // Calculate the target angle for the canvas wheel
                    const segmentAngle = 360 / this.prizes.length;
                    const targetAngle = prizeIndex * segmentAngle + (segmentAngle / 2);
                    
                    // Animate the canvas wheel to the correct position
                    this.animateCanvasWheel(targetAngle, () => {
                        this.showPrizeResult(wonPrize, 'wheel');
                        this.resetSpinButton();
                    });
                } else {
                    // Fallback: random rotation if prize not found
                    console.warn('Prize not found in wheel segments, using random rotation');
                    const randomAngle = Math.random() * 360;
                    this.animateCanvasWheel(randomAngle, () => {
                        this.showPrizeResult(wonPrize, 'wheel');
                        this.resetSpinButton();
                    });
                }
            } else {
                this.showError(response.message || 'Failed to spin wheel');
                this.resetSpinButton();
            }
        } catch (error) {
            this.showError('Network error. Please try again.');
            this.resetSpinButton();
        }
    }

    animateCanvasWheel(targetAngle, callback) {
        const canvas = document.getElementById('wheelCanvas');
        if (!canvas) return;
        
        // Add multiple rotations for visual effect
        const fullRotations = 5 + Math.random() * 3;
        const totalRotation = (fullRotations * 360) + (360 - targetAngle); // Subtract because we want the segment to align with pointer at top
        
        const startTime = Date.now();
        const duration = 3000; // 3 seconds
        const startRotation = this.currentRotation || 0;
        
        const animate = () => {
            const elapsed = Date.now() - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function for smooth deceleration
            const easeOut = 1 - Math.pow(1 - progress, 3);
            
            const currentRotation = startRotation + (totalRotation * easeOut);
            this.currentRotation = currentRotation;
            
            // Redraw the wheel at the current rotation
            this.drawWheel(currentRotation);
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                // Animation complete
                if (callback) callback();
            }
        };
        
        animate();
    }

    drawWheel(rotation = 0) {
        const canvas = document.getElementById('wheelCanvas');
        if (!canvas || !this.prizes || this.prizes.length === 0) return;
        
        const ctx = canvas.getContext('2d');
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        const radius = Math.min(centerX, centerY) - 10;
        
        // Clear canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // Save context for rotation
        ctx.save();
        ctx.translate(centerX, centerY);
        ctx.rotate((rotation * Math.PI) / 180);
        
        const segmentAngle = (2 * Math.PI) / this.prizes.length;
        const colors = [
            '#ef4444', '#f97316', '#eab308', '#22c55e', 
            '#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899'
        ];
        
        // Draw segments
        this.prizes.forEach((prize, index) => {
            const startAngle = index * segmentAngle;
            const endAngle = startAngle + segmentAngle;
            const color = colors[index % colors.length];
            
            // Draw segment
            ctx.beginPath();
            ctx.moveTo(0, 0);
            ctx.arc(0, 0, radius, startAngle, endAngle);
            ctx.closePath();
            ctx.fillStyle = color;
            ctx.fill();
            ctx.strokeStyle = '#ffffff';
            ctx.lineWidth = 2;
            ctx.stroke();
            
            // Draw text
            ctx.save();
            ctx.rotate(startAngle + segmentAngle / 2);
            ctx.textAlign = 'center';
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 14px Arial';
            ctx.shadowColor = 'rgba(0, 0, 0, 0.7)';
            ctx.shadowBlur = 2;
            ctx.shadowOffsetX = 1;
            ctx.shadowOffsetY = 1;
            
            // Prize name
            const prizeName = prize.name.length > 12 ? prize.name.substring(0, 10) + '...' : prize.name;
            ctx.fillText(prizeName, radius * 0.7, -5);
            
            // Prize price
            ctx.font = 'bold 12px Arial';
            ctx.fillText(prize.formatted_price, radius * 0.7, 10);
            
            ctx.restore();
        });
        
        // Draw center circle
        ctx.beginPath();
        ctx.arc(0, 0, 20, 0, 2 * Math.PI);
        ctx.fillStyle = '#fbbf24';
        ctx.fill();
        ctx.strokeStyle = '#ffffff';
        ctx.lineWidth = 4;
        ctx.stroke();
        
        ctx.restore();
    }

    async openBox() {
        if (this.isSpinning) return;
        
        this.isSpinning = true;
        const $button = $('.open-box-btn');
        const $box = $('.mystery-box');
        
        // Disable button and show loading
        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Opening...');
        
        try {
            // Animate box opening
            $box.addClass('opening');
            
            // Make API call to get prize
            const response = await $.post(`${this.apiBase}/spin.php`, { method: 'box' });
            
            if (response.success) {
                // Show result after animation
                setTimeout(() => {
                    this.showPrizeResult(response.data.prize, 'box');
                    this.resetBoxButton();
                    $box.removeClass('opening');
                }, 1000);
            } else {
                this.showError(response.message || 'Failed to open box');
                this.resetBoxButton();
                $box.removeClass('opening');
            }
        } catch (error) {
            this.showError('Network error. Please try again.');
            this.resetBoxButton();
            $box.removeClass('opening');
        }
    }

    showPrizeResult(prize, method) {
        const methodIcon = method === 'wheel' ? '🎡' : '📦';
        const methodName = method === 'wheel' ? 'Wheel of Fortune' : 'Mystery Box';
        
        const modalHtml = `
            <div class="prize-reveal show">
                <div class="prize-content">
                    <div class="text-6xl mb-4">${methodIcon}</div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Congratulations!</h2>
                    <p class="text-gray-600 mb-4">You won from ${methodName}:</p>
                    <div class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white p-4 rounded-lg mb-6">
                        <h3 class="text-2xl font-bold">${prize.name}</h3>
                        <p class="text-xl">${prize.formatted_price}</p>
                    </div>
                    <button class="close-modal bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-300">
                        <i class="fas fa-check mr-2"></i>Awesome!
                    </button>
                </div>
            </div>
        `;
        
        $('body').append(modalHtml);
        this.createConfetti();
        
        // Auto-close after 10 seconds
        setTimeout(() => {
            this.closeModal();
        }, 10000);
    }

    showError(message) {
        const modalHtml = `
            <div class="prize-reveal show">
                <div class="prize-content">
                    <div class="text-6xl mb-4">😞</div>
                    <h2 class="text-2xl font-bold text-red-600 mb-4">Oops!</h2>
                    <p class="text-gray-600 mb-6">${message}</p>
                    <button class="close-modal bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg transition duration-300">
                        <i class="fas fa-times mr-2"></i>Try Again
                    </button>
                </div>
            </div>
        `;
        
        $('body').append(modalHtml);
    }

    closeModal() {
        $('.prize-reveal').removeClass('show');
        setTimeout(() => {
            $('.prize-reveal').remove();
            $('.confetti').remove();
        }, 300);
    }

    resetSpinButton() {
        this.isSpinning = false;
        $('.spin-wheel-btn').prop('disabled', false).html('<i class="fas fa-play mr-2"></i>Spin Wheel!');
    }

    resetBoxButton() {
        this.isSpinning = false;
        $('.open-box-btn').prop('disabled', false).html('<i class="fas fa-box-open mr-2"></i>Open Box!');
    }

    createConfetti() {
        const confettiContainer = $('<div class="confetti"></div>');
        $('body').append(confettiContainer);
        
        const colors = ['#fbbf24', '#ef4444', '#10b981', '#3b82f6', '#8b5cf6', '#f59e0b'];
        
        for (let i = 0; i < 50; i++) {
            const confettiPiece = $('<div class="confetti-piece"></div>');
            confettiPiece.css({
                left: Math.random() * 100 + '%',
                backgroundColor: colors[Math.floor(Math.random() * colors.length)],
                animationDelay: Math.random() * 3 + 's',
                animationDuration: (Math.random() * 3 + 2) + 's'
            });
            confettiContainer.append(confettiPiece);
        }
        
        // Remove confetti after animation
        setTimeout(() => {
            confettiContainer.remove();
        }, 5000);
    }

    generateWheelSegments(prizes) {
        if (!prizes || prizes.length === 0) {
            const canvas = document.getElementById('wheelCanvas');
            if (canvas) {
                const ctx = canvas.getContext('2d');
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.fillStyle = '#666666';
                ctx.font = '20px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('No prizes available', canvas.width / 2, canvas.height / 2);
            }
            return;
        }
        
        // Store prizes and draw initial wheel
        this.prizes = prizes;
        this.currentRotation = 0;
        this.drawWheel(0);
    }

    // Utility functions
    formatPrice(price) {
        return '$' + parseFloat(price).toFixed(2);
    }

    showLoading(element) {
        $(element).html('<div class="spinner"></div>');
    }

    hideLoading(element, originalContent) {
        $(element).html(originalContent);
    }
}

// Initialize app when document is ready
$(document).ready(function() {
    window.prizeApp = new PrizeApp();
    
    // Add some global event handlers
    $(document).on('click', '[data-toggle="modal"]', function() {
        const target = $(this).data('target');
        $(target).addClass('show');
    });
    
    $(document).on('click', '[data-dismiss="modal"]', function() {
        $(this).closest('.modal').removeClass('show');
    });
    
    // Handle escape key for modals
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            $('.prize-reveal.show').removeClass('show');
            setTimeout(() => {
                $('.prize-reveal').remove();
            }, 300);
        }
    });
});

// Export for use in other files
window.PrizeApp = PrizeApp;

