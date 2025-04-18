import Alpine from 'alpinejs'
import './bootstrap';
import { animatedCounter } from './animated-counter';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.data('animatedCounter', animatedCounter);
Alpine.start();
