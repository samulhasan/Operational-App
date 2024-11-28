<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <x-slot name="title">
        {{ __('Dashboard') }}
    </x-slot>

    <head>
        <style>
            .main-content {
                display: flex;
                justify-content: space-around;
                width: 100%;
                padding: 50px;
                opacity: 1;
                transition: opacity 1s ease-out;
                flex-wrap: wrap;
            }

            .container {
                width: 70%;
                display: grid;
                grid-gap: 20px;
                background-color: #fff;
                padding: 45px;
                border-radius: 15px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                align-items: center;
                transition: box-shadow 0.3s ease;
                position: relative;
                margin-bottom: 20px;
            }

            .container:hover {
                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
            }
        </style>
    </head>

    <body>
        <div class="main-content" id="mainContent">   
            <!-- 24-Hour Chart Section -->
            <div class="container">
                <h2 style="text-align: center; padding-top: 20px;">Device Status Summary (Last 24 Hours)</h2>
                <div id="charts" class="d-flex flex-wrap justify-content-center">
                    @foreach ($deviceData as $deviceId => $statusCounts)
                        <div class="chart-container" style="width: 250px; margin: 10px;">
                            <h3 style="text-align: center;">{{ $deviceId }}</h3>
                            <canvas id="chart-24h-{{ $deviceId }}" width="250" height="250"></canvas>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 30-Day Chart Section -->
            <div class="container">
                <h2 style="text-align: center; padding-top: 20px;">Device Status Summary (Last 30 Days)</h2>
                <div id="charts-30d" class="d-flex flex-wrap justify-content-center">
                    @foreach ($deviceDataLast30Days as $deviceId => $statusCounts)
                        <div class="chart-container" style="width: 250px; margin: 10px;">
                            <h3 style="text-align: center;">{{ $deviceId }}</h3>
                            <canvas id="chart-30d-{{ $deviceId }}" width="250" height="250"></canvas>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 24-Hour Summary Table -->
            <div class="container">
                <h2 style="margin-top: 20px; text-align: center;">Rangkuman Status Display per Menit (24 Jam)</h2>
                <table class="table table-bordered" style="width: 60%; margin: auto;">
                    <thead>
                        <tr>
                            <th>Device ID</th>
                            <th>Online Hit</th>
                            <th>Offline Hit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($deviceData as $deviceId => $statusCounts)
                            <tr>
                                <td>{{ $deviceId }}</td>
                                <td>{{ $statusCounts['online'] }}</td>
                                <td>{{ $statusCounts['offline'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- 30-Day Summary Table -->
            <div class="container">
                <h2 style="margin-top: 20px; text-align: center;">Rangkuman Status Display per Menit (30 Hari)</h2>
                <table class="table table-bordered" style="width: 60%; margin: auto;">
                    <thead>
                        <tr>
                            <th>Device ID</th>
                            <th>Online Hit</th>
                            <th>Offline Hit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($deviceDataLast30Days as $deviceId => $statusCounts)
                            <tr>
                                <td>{{ $deviceId }}</td>
                                <td>{{ $statusCounts['online'] }}</td>
                                <td>{{ $statusCounts['offline'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const data24h = @json($deviceData);
                const data30d = @json($deviceDataLast30Days);

                // Render 24-hour charts
                Object.keys(data24h).forEach(deviceId => {
                    const ctx = document.getElementById(`chart-24h-${deviceId}`).getContext('2d');
                    const onlineCount = data24h[deviceId].online;
                    const offlineCount = data24h[deviceId].offline;

                    new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ['Online', 'Offline'],
                            datasets: [{
                                data: [onlineCount, offlineCount],
                                backgroundColor: ['rgba(54, 162, 235, 0.2)', 'rgba(255, 99, 132, 0.2)'],
                                borderColor: ['rgba(54, 162, 235, 1)', 'rgba(255, 99, 132, 1)'],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: true,
                                    text: `Device ID: ${deviceId} Status in the Last 24 Hours`
                                },
                                datalabels: {
                                    formatter: (value, context) => {
                                        const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        return ((value / total) * 100).toFixed(2) + '%';
                                    },
                                    color: '#fff',
                                }
                            }
                        },
                        plugins: [ChartDataLabels]
                    });
                });

                // Render 30-day charts
                Object.keys(data30d).forEach(deviceId => {
                    const ctx = document.getElementById(`chart-30d-${deviceId}`).getContext('2d');
                    const onlineCount = data30d[deviceId].online;
                    const offlineCount = data30d[deviceId].offline;

                    new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: ['Online', 'Offline'],
                            datasets: [{
                                data: [onlineCount, offlineCount],
                                backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(255, 99, 132, 0.2)'],
                                borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 99, 132, 1)'],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: true,
                                    text: `Device ID: ${deviceId} Status in the Last 30 Days`
                                },
                                datalabels: {
                                    formatter: (value, context) => {
                                        const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        return ((value / total) * 100).toFixed(2) + '%';
                                    },
                                    color: '#fff',
                                }
                            }
                        },
                        plugins: [ChartDataLabels]
                    });
                });
            });
        </script>
    </body>
</x-app-layout>
