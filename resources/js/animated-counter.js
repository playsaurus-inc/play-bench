const formatter = new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
});

/**
 * A simple component that animates a number from 0 to a target number.
 * The `formattedCounter` property returns the current number formatted as a string.
 *
 * @param {number} target The target number to animate to.
 * @returns {Object} The animated counter data object.
 */
export function animatedCounter(target) {
    return {
        current: 0,
        target: target,
        init() {
            this.animate();
        },
        get formattedCurrent() {
            return formatter.format(this.current);
        },
        animate() {
            const duration = 1500;
            const startTime = Date.now();

            const updateCounter = () => {
                const currentTime = Date.now();
                const progress = Math.min((currentTime - startTime) / duration, 1);

                this.current = Math.floor(progress * this.target);

                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                } else {
                    this.current = this.target;
                }
            };

            updateCounter();
        }
    };
}
