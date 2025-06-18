$workingDirectory = "c:\xampp\htdocs\MyUkm"
Set-Location $workingDirectory
while ($true) {
    php artisan queue:work --tries=3 --timeout=60
    Start-Sleep -Seconds 5
}
