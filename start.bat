@echo off
setlocal

echo === STARTUJEMY PROJEKT KALKULATORA PIT ===

:: 1. Plik .env
IF NOT EXIST ".env" (
    echo Tworzenie pliku .env...
    copy .env.example .env
) ELSE (
    echo Plik .env juz istnieje.
)

:: 2. Composer install
IF NOT EXIST "vendor" (
    echo Instalacja zależności Composer...
    composer install
    echo Composer install zakonczony.

    echo.
    echo *** Restartujemy skrypt po composer install... ***
    timeout /t 2 >nul
    call "%~f0"
    exit /b
) ELSE (
    echo Katalog vendor juz istnieje - pomijam composer install.
)

:: 3. Generowanie klucza
echo Generowanie klucza aplikacji...
php artisan key:generate

:: 4. Migracje + seed
echo Uruchamianie migracji i seederow...
php artisan migrate:fresh --seed

:: 5. Przeglądarka
start http://localhost:8000

:: 6. Serwer Laravel w nowym oknie
echo Uruchamianie serwera Laravel...
start cmd /k "php artisan serve"

