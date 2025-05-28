@echo off
chcp 65001 >nul
cd /d %~dp0
SETLOCAL

:: Konfiguracja bazy danych
SET DB_USER=root
SET DB_NAME=pit_calculator

echo [1/8] Tworzenie bazy danych (jesli jeszcze nie istnieje)
"C:\xampp\mysql\bin\mysql.exe" -u %DB_USER% -e "CREATE DATABASE IF NOT EXISTS %DB_NAME% CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
IF %ERRORLEVEL% NEQ 0 (
    echo Błąd: Nie mozna utworzyc bazy danych.
    pause
    EXIT /B
)

echo [2/8] Instalacja zaleznosci Node.js i PHP...
call npm install || goto error
call composer install || goto error

echo [3/8] Migracje i seedy...
call php artisan migrate:fresh --seed || goto error

echo [4/8] Tworzenie linku storage...
call php artisan storage:link || goto error

echo [5/8] Uruchamianie frontend (npm run dev)...
start "Vite Dev" cmd /k npm run dev

echo [6/8] Uruchamianie queue:work...
start "Queue Worker" cmd /k php artisan queue:work

echo [7/8] Uruchamianie schedule:work...
start "Schedule Worker" cmd /k php artisan schedule:work

echo [8/8] Uruchamianie serwera Laravel...
start "Laravel Server" cmd /k php artisan serve

echo Gotowe!
goto end

:error
echo Wystapil blad podczas uruchamiania.
pause
exit /B

:end
pause
