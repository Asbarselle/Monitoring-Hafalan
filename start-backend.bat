@echo off
cd /d "%~dp0backend"
start "Laravel Backend" php artisan serve --host=127.0.0.1 --port=8000
start "Open Browser" http://127.0.0.1:8000
