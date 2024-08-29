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
        width: 70%;  /* 100% dari lebar halaman */
        margin: 40px auto; /* Menambahkan margin atas dan bawah otomatis untuk memusatkan */
    }
</style>

<!-- Tambahkan div untuk peta -->
<div id="map"></div>

<!-- Tambahkan script untuk memuat Leaflet dan menginisialisasi peta -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
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
