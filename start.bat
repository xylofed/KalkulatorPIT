@echo off
setlocal

echo === STARTUJEMY PROJEKT KALKULATORA PIT ===

:: 1. Kopiowanie pliku .env jeśli nie istnieje
IF NOT EXIST ".env" (
    echo Tworzenie pliku .env...
    copy .env.example .env
) ELSE (
    echo Plik .env już istnieje.
)

:: 2. Instalacja zależności Composer (jeśli jeszcze nie ma vendor)
IF NOT EXIST "vendor" (
    echo Instalacja zależności Composer...
    composer install
ELSE (
    echo Katalog vendor już istnieje – pomijam composer install.
)
)

:: 3. Generowanie klucza aplikacji
echo Generowanie klucza aplikacji...
php artisan key:generate

:: 4. Migracje + seed
echo Uruchamianie migracji i seederów...
php artisan migrate:fresh --seed

:: 5. Otwieranie aplikacji w przeglądarce
start http://localhost:8000

:: 6. Uruchamianie lokalnego serwera
echo Uruchamianie serwera Laravel...
php artisan serve

pause
