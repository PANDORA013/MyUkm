$workingDirectory = "c:\xampp\htdocs\MyUkm"
Set-Location $workingDirectory
while ($true) {
    try {
        php artisan queue:work redis --queue=default,broadcasts --tries=3 --timeout=60
    } catch {
        Write-Host "Queue worker stopped. Restarting in 5 seconds..."
    }
    Start-Sleep -Seconds 5
}
