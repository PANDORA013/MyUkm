# Test Middleware Fix - PowerShell version
Write-Host "=== Testing Middleware Configuration ===" -ForegroundColor Green

Write-Host "`n1. Checking CSRF middleware exceptions..." -ForegroundColor Yellow
if (Test-Path "app/Http/Middleware/VerifyCsrfToken.php") {
    $csrfContent = Get-Content "app/Http/Middleware/VerifyCsrfToken.php" -Raw
    if ($csrfContent -match "broadcasting/auth") {
        Write-Host "[OK] CSRF exception for /broadcasting/auth found" -ForegroundColor Green
    } else {
        Write-Host "[ERROR] CSRF exception for /broadcasting/auth NOT found" -ForegroundColor Red
    }
} else {
    Write-Host "[ERROR] CSRF middleware file not found" -ForegroundColor Red
}

Write-Host "`n2. Checking Security Headers middleware..." -ForegroundColor Yellow
if (Test-Path "app/Http/Middleware/SecurityHeaders.php") {
    $securityContent = Get-Content "app/Http/Middleware/SecurityHeaders.php" -Raw
    if ($securityContent -match "ws:" -and $securityContent -match "wss:" -and $securityContent -match "pusher") {
        Write-Host "[OK] WebSocket CSP configuration found" -ForegroundColor Green
    } else {
        Write-Host "[INFO] WebSocket CSP configuration may need checking" -ForegroundColor Yellow
    }
} else {
    Write-Host "[INFO] Security Headers middleware not found (may not exist)" -ForegroundColor Gray
}

Write-Host "`n3. Checking Broadcasting Service Provider..." -ForegroundColor Yellow
if (Test-Path "app/Providers/BroadcastServiceProvider.php") {
    $broadcastContent = Get-Content "app/Providers/BroadcastServiceProvider.php" -Raw
    if ($broadcastContent -match "Broadcast::routes") {
        Write-Host "[OK] Broadcasting routes registration found" -ForegroundColor Green
    } else {
        Write-Host "[ERROR] Broadcasting routes registration NOT found" -ForegroundColor Red
    }
} else {
    Write-Host "[ERROR] Broadcasting Service Provider not found" -ForegroundColor Red
}

Write-Host "`n4. Checking channel authentication..." -ForegroundColor Yellow
if (Test-Path "routes/channels.php") {
    $channelsContent = Get-Content "routes/channels.php" -Raw
    if ($channelsContent -match "group\.") {
        Write-Host "[OK] Group channel authentication found" -ForegroundColor Green
    } else {
        Write-Host "[ERROR] Group channel authentication NOT found" -ForegroundColor Red
    }
} else {
    Write-Host "[ERROR] channels.php file not found" -ForegroundColor Red
}

Write-Host "`n=== Middleware Test Complete ===" -ForegroundColor Green
Write-Host "If all items show [OK], middleware is properly configured for real-time chat." -ForegroundColor Cyan
Write-Host "Run .\launch-myukm.bat to start the application." -ForegroundColor Cyan
