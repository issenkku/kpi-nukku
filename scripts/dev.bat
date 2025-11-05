@echo off
setlocal enableextensions
cd /d %~dp0..

rem Start the Laravel scheduler in a new Command Prompt window
start "Laravel Scheduler" cmd /c "php artisan schedule:work"

rem Start the Laravel dev server in this window
php artisan serve

endlocal

