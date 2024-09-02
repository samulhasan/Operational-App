import requests
import time
from PIL import ImageGrab
import io

DEVICE_ID = 'KSOP '  # Ganti dengan ID perangkat Anda
API_URL = 'http://36.95.90.12/api/update-screenshot'  # Ganti dengan URL API Laravel Anda

def send_screenshot():
    # Ambil screenshot
    screenshot = ImageGrab.grab()
    
    # Simpan screenshot ke dalam buffer
    buffer = io.BytesIO()
    screenshot.save(buffer, format='PNG')
    buffer.seek(0)
    
    # Kirim screenshot ke server
    response = requests.post(API_URL, files={'screenshot': buffer}, data={'device_id': DEVICE_ID})
    print(response.text)  # Cetak respons mentah dari server
    try:
        print(response.json())
    except requests.exceptions.JSONDecodeError as e:
        print("Error decoding JSON:", e)

while True:
    send_screenshot()
    time.sleep(10)