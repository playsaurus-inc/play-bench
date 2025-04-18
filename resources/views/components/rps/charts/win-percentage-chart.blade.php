@props(['rpsMatch'])

<x-ui.card title="Win Percentage Over Time" subtitle="Win rate progression through rounds">
    <div>
        @php
            $chartData = $rpsMatch->getWinPercentageChartData();
            $chartId = 'win-percentage-chart-' . $rpsMatch->id;
        @endphp

        <div class="h-80">
            <canvas id="{{ $chartId }}"></canvas>
        </div>

        <div class="mt-4 flex justify-center gap-4">
            <div class="flex items-center">
                <span class="block w-4 h-4 bg-rose-300 mr-2"></span>
                <span class="text-sm text-gray-700">{{ $rpsMatch->player1->name }}</span>
            </div>
            <div class="flex items-center">
                <span class="block w-4 h-4 bg-sky-300 mr-2"></span>
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
                            backgroundColor: 'rgb(253, 164, 175)', // rose-300
                            borderWidth: 0,
                            fill: true,
                            tension: 0.3,
                            pointRadius: 0,
                            pointHoverRadius: 0
                        }, {
                            label: '{{ $chartData['player2Name'] }}',
                            data: @json($chartData['player2Data']),
                            backgroundColor: 'rgb(125, 211, 252)', // sky-300
                            borderWidth: 0,
                            fill: true,
                            tension: 0.3,
                            pointRadius: 0,
                            pointHoverRadius: 0
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
                                    },
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }

                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('en-US', {
                                                style: 'percent',
                                                minimumFractionDigits: 1,
                                                maximumFractionDigits: 1
                                            }).format(context.parsed.y / 100);
                                        }

                                        return label;
                                    }
                                }
                            },
                            filler: {
                                propagate: false
                            }
                        },
                        scales: {
                            y: {
                                stacked: true,
                                min: 0,
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Win %',
                                    font: {
                                        size: 13,
                                    },
                                    color: '#64748b' // slate-500
                                },
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    },
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
