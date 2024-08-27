import time
import requests
import pyautogui
from datetime import datetime
import os

DEVICE_ID = 'device_1'  # Ganti dengan ID atau nama perangkat

def send_screenshot():
    screenshot = pyautogui.screenshot()
    screenshot_path = f'screenshot_{DEVICE_ID}.png'
    screenshot.save(screenshot_path)

    url = 'http://192.168.1.34/upload-screenshot'
    with open(screenshot_path, 'rb') as screenshot_file:
        files = {'screenshot': screenshot_file}
        timestamp = datetime.utcnow().isoformat()  # Get current timestamp in ISO format
        data = {'device_id': DEVICE_ID, 'timestamp': timestamp}  # Send device ID and timestamp
        response = requests.post(url, files=files, data=data)
    
    print("Status Code:", response.status_code)
    print("Response Text:", response.text)

    # Optional: Remove the screenshot file after sending
    os.remove(screenshot_path)

while True:
    send_screenshot()
    time.sleep(1)  # Tunggu 1 detik sebelum mengambil screenshot lagi