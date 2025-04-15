const intFormatter = new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
});

const percentFormatter = new Intl.NumberFormat('en-US', {
    style: 'percent',
    minimumFractionDigits: 2,
});

/**
 * A simple component that animates a number from 0 to a target number.
 * The `integerValue` property returns the current number formatted as a string.
 * The `percentageValue` property returns the current number formatted as a percentage string.
 *
 * @param {number} target The target number to animate to.
 * @returns {Object} The animated counter data object.
 */
export function animatedCounter(target) {
    return {
        value: 0,
        target: target,
        init() {
            this.animate();
        },
        get integerValue() {
            return intFormatter.format(this.value);
        },
        get percentageValue() {
            return percentFormatter.format(this.value / 100);
        },
        quadOut(t) {
            return t * (2 - t);
        },
        animate() {
            const duration = 1500;
            const startTime = Date.now();

            const updateCounter = () => {
                const currentTime = Date.now();
                const t = Math.min((currentTime - startTime) / duration, 1);
                const progress = this.quadOut(t);

                this.value = progress * this.target;

                if (progress < 1) {
                    requestAnimationFrame(updateCounter);
                } else {
                    this.value = this.target;
                }
            };

            updateCounter();
        }
    };
}
