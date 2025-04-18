@props(['rpsMatch'])

<x-ui.card title="Cumulative Wins" subtitle="Win progress throughout the match">
    <div>
        @php
            $chartData = $rpsMatch->getCumulativeWinChartData();
            $chartId = 'cumulative-wins-chart-' . $rpsMatch->id;
        @endphp

        <div class="h-80">
            <canvas id="{{ $chartId }}"></canvas>
        </div>

        <div class="mt-4 flex justify-center gap-4">
            <div class="flex items-center">
                <span class="block w-4 h-4 bg-red-400 rounded mr-2"></span>
                <span class="text-xs font-bold text-gray-500">{{ ucfirst($rpsMatch->player1->name) }}</span>
            </div>
            <div class="flex items-center">
                <span class="block w-4 h-4 bg-blue-400 rounded mr-2"></span>
                <span class="text-xs font-bold text-gray-500">{{ ucfirst($rpsMatch->player2->name) }}</span>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const el = document.getElementById('{{ $chartId }}');

                new Chart(el, {
                    type: 'line',
                    data: {
                        labels: @json($chartData['labels']),
                        datasets: [{
                            label: '{{ $chartData['player1Name'] }}',
                            data: @json($chartData['player1Data']),
                            borderColor: '#ef4444', // red-500
                            pointBackgroundColor: '#ef4444', // red-500
                            //backgroundColor: 'rgba(248, 113, 113, 0.2)', // red-400/20,
                            backgroundColor: 'transparent',
                            borderWidth: 2.5,
                            tension: 0.3,
                            pointRadius: 0,
                            pointHoverRadius: 5,
                            fill: 'stack',
                        }, {
                            label: '{{ $chartData['player2Name'] }}',
                            data: @json($chartData['player2Data']),
                            borderColor: '#3b82f6', // blue-500
                            pointBackgroundColor: '#3b82f6', // blue-500
                            //backgroundColor: 'rgba(96, 165, 250, 0.2)', // blue-400/20,
                            backgroundColor: 'transparent',
                            borderWidth: 2.5,
                            tension: 0.3,
                            pointRadius: 0,
                            pointHoverRadius: 5,
                            fill: 'stack'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            intersect: false,
                            mode: 'index',
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                titleColor: '#334155', // slate-700
                                bodyColor: '#334155', // slate-700
                                borderColor: '#e2e8f0', // slate-200
                                borderWidth: 1,
                                padding: 10,
                                cornerRadius: 8,
                                callbacks: {
                                    title: function(context) {
                                        return 'Round ' + context[0].label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Wins',
                                    font: {
                                        size: 13,
                                    },
                                    color: '#64748b' // slate-500
                                },
                                ticks: {
                                    precision: 0,
                                    color: '#94a3b8' // slate-400
                                },
                                grid: {
                                    color: 'rgba(226, 232, 240, 0.6)' // slate-200
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Round',
                                    font: {
                                        size: 13,
                                    },
                                    color: '#64748b' // slate-500
                                },
                                ticks: {
                                    maxTicksLimit: 10,
                                    color: '#94a3b8' // slate-400
                                },
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            });
        </script>
    </div>
</x-ui.card>
