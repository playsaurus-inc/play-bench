import Alpine from 'alpinejs'
import './bootstrap';
import { animatedCounter } from './animated-counter';
import Chart from 'chart.js/auto';
import hljs from 'highlight.js/lib/core';
import xml from 'highlight.js/lib/languages/xml';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.data('animatedCounter', animatedCounter);
Alpine.start();

hljs.registerLanguage('xml', xml);
hljs.highlightAll();
