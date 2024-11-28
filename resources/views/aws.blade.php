<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('AWS') }}
        </h2>
    </x-slot>


    <x-slot name="title">
            {{ __('AWS') }}
    </x-slot>



<style>
    html, body {
        height: 100%;
        margin: 0;
        padding: 0;
        width: 100%;
    }
    #map {
        height: 70vh; /* 70% dari tinggi viewport */
        width: 90%;  /* 100% dari lebar halaman */
        margin: 40px auto; /* Menambahkan margin atas dan bawah otomatis untuk memusatkan */
    }

    .main-content {
        display: flex;
        justify-content: space-around;
        width: 100%;
        padding: 50px;
        opacity: 1;
        transition: opacity 1s ease-out;
        flex-wrap: wrap; /* Added to handle smaller screens */
    }

    .container {
        width: 45%; /* Adjusted to fit two containers side by side */
        display: grid;
        grid-template-areas: ""; /* Removed undefined grid areas */
        grid-gap: 20px;
        background-color: #fff;
        padding: 45px;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        align-items: center;
        transition: box-shadow 0.3s ease;
        position: relative; /* Added for indicator positioning */
        margin-bottom: 20px; /* Added to handle spacing between containers */
    }

    .container:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    }

    .iframe-container {
        width: 100%;
        height: 400px; /* Adjusted height for smaller iframe */
        overflow: hidden; /* Ensure no scrollbars */
    }

    .iframe-container iframe {
        width: 200%; /* Adjust width for zoom effect */
        height: 200%; /* Adjust height for zoom effect */
        transform: scale(0.5); /* Scale down to 50% */
        transform-origin: 0 0; /* Set transform origin to top-left */
    }

    .form-bar {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        background-color: #f8f9fa;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .form-bar form {
        display: flex;
        align-items: center;
    }

    .form-bar input[type="url"] {
        padding: 10px;
        margin-right: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        width: 300px;
    }

    .form-bar button {
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .form-bar button:hover {
        background-color: #0056b3;
    }

    .delete-button {
        background: none;
        border: none;
        color: #ccc;
        font-size: 1.5em;
        cursor: pointer;
        position: absolute;
        top: 10px;
        right: 40px; /* Adjusted position */
    }

    .delete-button:hover {
        color: #dc3545;
    }
</style>


    <div class="form-bar">
        <form method="POST" action="{{ route('addIframe') }}">
            @csrf
            <label for="iframeUrl" style="margin-right: 10px;">Tambahkan URL AWS:</label>
            <input type="url" id="iframeUrl" name="iframeUrl" required>
            <button type="submit">Tambahkan</button>
        </form>
    </div>

    <div class="main-content" id="mainContent">
        @foreach($iframes as $iframe)
        <div class="container" id="container-{{ $iframe->id }}">
            <button class="delete-button" onclick="deleteIframe('{{ $iframe->id }}')">&times;</button>
            <button onclick="openIframeInNewTab('{{ $iframe->url }}')" style="position: absolute; top: 10px; right: 10px; background: none; border: none; cursor: pointer;">
                <i class="fas fa-external-link-alt"></i>
            </button>
            <div class="iframe-container">
                <iframe src="{{ $iframe->url }}" style="border:none;"></iframe>
            </div>
        </div>
        @endforeach
    </div>

    <script>
        function openIframeInNewTab(url) {
            window.open(url, '_blank');
        }

        function deleteIframe(iframeId) {
            if (confirm('Are you sure you want to delete this iframe?')) {
                fetch(`/api/delete-iframe/${iframeId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`container-${iframeId}`).remove();
                    } else {
                        alert('Failed to delete iframe.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting iframe.');
                });
            }
        }
    </script>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Inisialisasi peta
        var map = L.map('map').setView([-3.654703, 128.190643], 11); // Koordinat sebelumnya
        // Tambahkan tile layer dari OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Fungsi untuk membuka halaman iframe di tab baru
        function openIframeInNewTab() {
            var iframeSrc = document.getElementById('iframe').src;
            window.open(iframeSrc, '_blank');
        }
    </script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</x-app-layout>
