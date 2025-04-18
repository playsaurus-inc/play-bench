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
                <span class="block w-4 h-4 rounded-full bg-rose-300 mr-2"></span>
                <span class="text-sm text-gray-700">{{ $rpsMatch->player1->name }}</span>
            </div>
            <div class="flex items-center">
                <span class="block w-4 h-4 rounded-full bg-sky-300 mr-2"></span>
                <span class="text-sm text-gray-700">{{ $rpsMatch->player2->name }}</span>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('{{ $chartId }}');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($chartData['labels']),
                        datasets: [{
                            label: '{{ $chartData['player1Name'] }}',
                            data: @json($chartData['player1Data']),
                            borderColor: 'rgb(253, 164, 175)', // rose-300
                            backgroundColor: 'rgba(253, 164, 175, 0.15)',
                            borderWidth: 2,
                            tension: 0.3,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            pointBackgroundColor: 'rgb(253, 164, 175)',
                            fill: true
                        }, {
                            label: '{{ $chartData['player2Name'] }}',
                            data: @json($chartData['player2Data']),
                            borderColor: 'rgb(125, 211, 252)', // sky-300
                            backgroundColor: 'rgba(125, 211, 252, 0.15)',
                            borderWidth: 2,
                            tension: 0.3,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            pointBackgroundColor: 'rgb(125, 211, 252)',
                            fill: true
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
