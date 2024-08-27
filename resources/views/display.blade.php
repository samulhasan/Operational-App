<x-layout> 
    <x-slot:title> Device Monitoring </x-slot:title>
    <head>
        <style>
            body {
                margin: 0;
                background-color: #f9f9f9;
                color: #333;
                overflow: hidden;
            }
    
            .main-content {
                display: flex;
                justify-content: space-around;
                width: 100%;
                padding: 20px;
                opacity: 1;
                transition: opacity 1s ease-out;
                flex-wrap: wrap; /* Added to handle smaller screens */
            }
    
            .container {
                width: 45%;
                display: grid;
                grid-template-areas:
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
    
            .indicator {
                width: 20px;
                height: 20px;
                border-radius: 50%;
                position: absolute;
                top: 10px;
                right: 10px;
            }
    
            .online {
                background-color: green;
            }
    
            .offline {
                background-color: red;
            }
    
            .device-image {
                width: 100%;
                height: auto;
                border-radius: 10px;
            }
    
            .device-id {
                text-align: center;
                font-weight: bold;
                font-size: 1.5em;
                margin-bottom: 10px;
            }

            .download-button {
                position: absolute;
                bottom: 10px;
                right: 30px;
                cursor: pointer;
            }

            .download-button img {
                width: 40px;
                height: 24px;
            }

            .last-update {
                position: absolute;
                bottom: 10px;
                left: 10px;
                font-size: 0.9em;
                color: #666;
            }
        </style>
    </head>
    
    <body>
       
    <div class="main-content" id="mainContent">
    @foreach($screenshots as $deviceId => $screenshot)
        <div class="container" id="container-{{ $deviceId }}">
            <div class="device-id">{{ $deviceId }}</div>
            <div class="indicator {{ $screenshot && $screenshot['is_online'] ? 'online' : 'offline' }}"></div>
            <div class="screenshot-box">
                @if($screenshot)
                    <img src="{{ $screenshot['url'] }}" alt="Screenshot dari {{ $deviceId }}" class="device-image" id="screenshot-{{ $deviceId }}">
                    <a href="{{ $screenshot['url'] }}" download="screenshot_{{ $deviceId }}" class="download-button">
                    <img src="{{ asset('images/download_button.png') }}" alt="Download">    
                    </a>
                    <div class="last-update">Last Update: {{ $screenshot['updated_at'] }}</div>
                @else
                    <p>Tidak ada screenshot tersedia untuk {{ $deviceId }}</p>
                @endif
            </div>
        </div>
    @endforeach
    </div>

    <script>
        function updateScreenshots() {
            fetch('/api/get-latest-screenshot')
                .then(response => response.json())
                .then(data => {
                    for (const [deviceId, screenshot] of Object.entries(data.screenshots)) {
                        const container = document.getElementById(`container-${deviceId}`);
                        const imgElement = document.getElementById(`screenshot-${deviceId}`);
                        const indicator = container.querySelector('.indicator');
                        const lastUpdateElement = container.querySelector('.last-update');

                        if (imgElement) {
                            imgElement.src = screenshot.url;
                        } else {
                            container.querySelector('.screenshot-box').innerHTML = `
                                <img src="${screenshot.url}" alt="Screenshot dari ${deviceId}" class="device-image" id="screenshot-${deviceId}">
                                <a href="${screenshot.url}" download="screenshot_${deviceId}" class="download-button">
                                    <img src="{{ asset('images/download_button.png') }}" alt="Download">
                                </a>
                                <div class="last-update">Last Update: ${screenshot.updated_at}</div>
                            `;
                        }

                        if (lastUpdateElement) {
                            lastUpdateElement.textContent = `Last Update: ${screenshot.updated_at}`;
                        }

                        indicator.className = `indicator ${screenshot && screenshot.is_online ? 'online' : 'offline'}`;
                    }
                });
        }

        setInterval(updateScreenshots, 3000); // Update every 3 seconds
    </script>
    </body>
</x-layout>