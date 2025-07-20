@echo off
echo ================================
echo   Flarum Development Helper
echo ================================
echo.

set FLARUM_PATH=C:\xampp\htdocs\my-flarum-test
set EXTENSION_PATH=F:\Projects\Post-Pin-Flarum

echo 1. Build extension assets
cd /d "%EXTENSION_PATH%"
call npm run build
if errorlevel 1 (
    echo Error building assets!
    pause
    exit /b 1
)

echo.
echo 2. Update extension in Flarum
cd /d "%FLARUM_PATH%"
call composer update nippytime/post-pin-flarum

echo.
echo 3. Clear Flarum cache
call php flarum cache:clear

echo.
echo 4. Migrate database (if needed)
call php flarum migrate

echo.
echo ================================
echo   Development update complete!
echo   Visit: http://localhost/my-flarum-test
echo ================================
pause