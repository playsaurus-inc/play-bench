import Alpine from 'alpinejs'
import './bootstrap';
import { animatedCounter } from './animated-counter';

window.Alpine = Alpine;

Alpine.data('animatedCounter', animatedCounter);
Alpine.start();
