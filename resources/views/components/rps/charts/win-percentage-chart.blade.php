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
                <span class="block w-4 h-4 rounded-full bg-red-500 mr-2"></span>
                <span class="text-sm text-gray-700">{{ $rpsMatch->player1->name }}</span>
            </div>
            <div class="flex items-center">
                <span class="block w-4 h-4 rounded-full bg-blue-500 mr-2"></span>
                <span class="text-sm text-gray-700">{{ $rpsMatch->player2->name }}</span>
            </div>
            <div class="flex items-center">
                <span class="block w-4 h-4 border border-gray-300 mr-2"></span>
                <span class="text-sm text-gray-700">50% line</span>
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
                            borderColor: 'rgb(239, 68, 68)',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 2,
                            tension: 0.1,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            pointBackgroundColor: 'rgb(239, 68, 68)'
                        }, {
                            label: '{{ $chartData['player2Name'] }}',
                            data: @json($chartData['player2Data']),
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            tension: 0.1,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            pointBackgroundColor: 'rgb(59, 130, 246)'
                        }, {
                            label: '50% Line',
                            data: Array({{ count($chartData['labels']) }}).fill(50),
                            borderColor: 'rgba(156, 163, 175, 0.5)',
                            borderWidth: 1,
                            borderDash: [5, 5],
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
                                callbacks: {
                                    title: function(context) {
                                        return 'Round ' + context[0].label;
                                    },
                                    label: function(context) {
                                        // Don't show the 50% line in the tooltip
                                        if (context.datasetIndex === 2) {
                                            return null;
                                        }

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
                            }
                        },
                        scales: {
                            y: {
                                min: 0,
                                max: 100,
                                title: {
                                    display: true,
                                    text: 'Win %'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Round'
                                },
                                ticks: {
                                    maxTicksLimit: 10
                                }
                            }
                        }
                    }
                });
            });
        </script>
    </div>
</x-ui.card>
