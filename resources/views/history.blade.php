<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('history') }}
        </h2>
    </x-slot>

    <div class="container mt-5" style="max-width: 1200px;"> <!-- Mengatur lebar maksimum kontainer -->
        <h2 class="mb-4"> History Display </h2>

        <!-- Filter and Sort Form -->
        <form method="GET" action="{{ route('device.logs') }}" class="mb-4">
            <div class="row">
                <div class="col-md-2">
                    <label for="device_id">Device ID</label>
                    <select name="device_id" id="device_id" class="form-control">
                        <option value="">All Devices</option>
                        @foreach ($devices as $device)
                            <option value="{{ $device->id }}" {{ request('device_id') == $device->id ? 'selected' : '' }}>
                                {{ $device->device_id }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="start_date">Start Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-2">
                    <label for="start_time">Start Time</label>
                    <input type="time" name="start_time" id="start_time" class="form-control" value="{{ request('start_time') }}">
                </div>
                
                <div class="col-md-2">
                    <label for="end_date">End Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
              
                <div class="col-md-2">
                    <label for="end_time">End Time</label>
                    <input type="time" name="end_time" id="end_time" class="form-control" value="{{ request('end_time') }}">
                </div>
                <div class="col-md-2">
                    <label for="sort">Sort By</label>
                    <select name="sort" id="sort" class="form-control">
                        <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Terbaru</option>
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Terlama</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="per_page">Rows Per Page</label>
                    <select name="per_page" id="per_page" class="form-control">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <a href="{{ route('device.logs.download', request()->query()) }}" class="btn btn-secondary">Download CSV</a>
                </div>
            </div>
        </form>

        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>Device ID</th>
                    <th>Status</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($deviceLogs as $log)
                    <tr>
                        <td>{{ $log->device->device_id }}</td> <!-- Display device_id from related device -->
                        <td>{{ $log->is_online ? 'Online' : 'Offline' }}</td> <!-- Display status instead of is_online -->
                        <td>{{ $log->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination Links -->
        <style>
            .pagination {
                height: 30px; /* Mengatur tinggi bar pagination */
                font-size: 0.8rem; /* Mengatur ukuran font */
            }
            .pagination li {
                padding: 0 5px; /* Mengatur padding item pagination */
            }
        </style>
        <div class="d-flex justify-content-center">
            {{ $deviceLogs->appends(request()->query())->links() }}
        </div>
    </div>
    
</x-app-layout>