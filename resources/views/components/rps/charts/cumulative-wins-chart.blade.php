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
                <span class="block w-4 h-4 rounded-full bg-red-500 mr-2"></span>
                <span class="text-sm text-gray-700">{{ $rpsMatch->player1->name }}</span>
            </div>
            <div class="flex items-center">
                <span class="block w-4 h-4 rounded-full bg-blue-500 mr-2"></span>
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
                            borderColor: 'rgb(239, 68, 68)',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 2,
                            tension: 0.1,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            pointBackgroundColor: 'rgb(239, 68, 68)',
                            fill: true
                        }, {
                            label: '{{ $chartData['player2Name'] }}',
                            data: @json($chartData['player2Data']),
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 2,
                            tension: 0.1,
                            pointRadius: 0,
                            pointHoverRadius: 4,
                            pointBackgroundColor: 'rgb(59, 130, 246)',
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
                                    text: 'Wins'
                                },
                                ticks: {
                                    precision: 0
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
