<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('AWS') }}
        </h2>
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
                width: 70%;
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
</style>


    <div class="main-content" id="mainContent">   
<div class="container"
<!-- Tambahkan div untuk peta -->
<div id="map"></div>
<!-- Tambahkan script untuk memuat Leaflet dan menginisialisasi peta -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
</div>
    </div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Inisialisasi peta
        var map = L.map('map').setView([-3.654703, 128.190643], 11); // Koordinat untuk Ambon
        // Tambahkan tile layer dari OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
    </script>

</x-app-layout>
