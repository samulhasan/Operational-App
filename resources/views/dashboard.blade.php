<x-layout>
    <x-slot:title> Dashboard </x-slot:title>
    
    <h2 style="text-align: center;">Device Status Summary (Last 24 Hours)</h2>

    <div id="charts" class="d-flex flex-wrap justify-content-center"> <!-- Center the charts -->
        @foreach ($deviceData as $deviceId => $statusCounts)
            <div class="chart-container" style="width: 250px; margin: 10px;"> <!-- Adjusted width -->
                <h3 style="text-align: center;">Device ID: {{ $deviceId }}</h3>
                <canvas id="chart-{{ $deviceId }}" width="250" height="250"></canvas> <!-- Adjusted size -->
            </div>
        @endforeach
    </div>

    <!-- Move the table below the charts -->
    <h2 style="margin-top: 20px; text-align: center;">Device Status Summary Table</h2>
    <table class="table table-bordered" style="width: 80%; margin: auto;">
        <thead>
            <tr>
                <th>Device ID</th>
                <th>Online Count</th>
                <th>Offline Count</th>
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script> <!-- Include the plugin -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const data = @json($deviceData);
            console.log(data); // Debugging line

            Object.keys(data).forEach(deviceId => {
                const ctx = document.getElementById(`chart-${deviceId}`).getContext('2d');
                const onlineCount = data[deviceId].online;
                const offlineCount = data[deviceId].offline;

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
                                    const percentage = ((value / total) * 100).toFixed(2) + '%';
                                    return percentage; // Show percentage
                                },
                                color: '#fff',
                            }
                        }
                    },
                    plugins: [ChartDataLabels] // Register the plugin
                });
            });
        });
    </script>
</x-layout>