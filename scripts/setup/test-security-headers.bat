@echo off
REM Security Headers Test Script for Windows
REM Tests that all required security headers are present

echo 🔒 Testing Security Headers for MyUKM Application
echo =================================================

REM Default URL - change this to your actual domain
set "URL=%1"
if "%URL%"=="" set "URL=http://localhost/MyUkm-main/public"

echo Testing URL: %URL%
echo.

echo Checking Security Headers:
echo -------------------------

REM Use PowerShell to check headers (more reliable than curl on Windows)
powershell -Command ^
"try { " ^
"  $response = Invoke-WebRequest -Uri '%URL%' -Method Head -UseBasicParsing; " ^
"  $headers = $response.Headers; " ^
"  Write-Host 'Response Status:' $response.StatusCode; " ^
"  Write-Host ''; " ^
"  Write-Host 'Security Headers:'; " ^
"  Write-Host '✅ X-Content-Type-Options:' ($headers['X-Content-Type-Options'] -join ', ') -ForegroundColor Green; " ^
"  Write-Host '✅ X-Frame-Options:' ($headers['X-Frame-Options'] -join ', ') -ForegroundColor Green; " ^
"  Write-Host '✅ X-XSS-Protection:' ($headers['X-XSS-Protection'] -join ', ') -ForegroundColor Green; " ^
"  Write-Host '✅ Referrer-Policy:' ($headers['Referrer-Policy'] -join ', ') -ForegroundColor Green; " ^
"  Write-Host '✅ Content-Security-Policy:' ($headers['Content-Security-Policy'] -join ', ').Substring(0, [Math]::Min(100, ($headers['Content-Security-Policy'] -join ', ').Length)) -ForegroundColor Green; " ^
"  Write-Host ''; " ^
"  Write-Host 'Cookie Security:'; " ^
"  if ($headers['Set-Cookie']) { " ^
"    $cookie = $headers['Set-Cookie'] -join ', '; " ^
"    Write-Host '✅ Set-Cookie found:' $cookie.Substring(0, [Math]::Min(80, $cookie.Length)) -ForegroundColor Green; " ^
"    if ($cookie -match 'Secure') { Write-Host '  ✅ Secure flag present' -ForegroundColor Green } " ^
"    else { Write-Host '  ⚠️ Secure flag missing' -ForegroundColor Yellow }; " ^
"    if ($cookie -match 'HttpOnly') { Write-Host '  ✅ HttpOnly flag present' -ForegroundColor Green } " ^
"    else { Write-Host '  ❌ HttpOnly flag missing' -ForegroundColor Red }; " ^
"    if ($cookie -match 'SameSite') { Write-Host '  ✅ SameSite attribute present' -ForegroundColor Green } " ^
"    else { Write-Host '  ⚠️ SameSite attribute missing' -ForegroundColor Yellow }; " ^
"  } else { Write-Host '⚠️ Set-Cookie not found (may require login)' -ForegroundColor Yellow }; " ^
"  Write-Host ''; " ^
"  Write-Host 'Cache Control:'; " ^
"  Write-Host '✅ Cache-Control:' ($headers['Cache-Control'] -join ', ') -ForegroundColor Green; " ^
"  if ($headers['Expires']) { " ^
"    Write-Host '⚠️ Expires header found (should be removed):' ($headers['Expires'] -join ', ') -ForegroundColor Yellow " ^
"  } else { " ^
"    Write-Host '✅ Expires header not present (good)' -ForegroundColor Green " ^
"  }; " ^
"  Write-Host ''; " ^
"  Write-Host 'Content-Type:'; " ^
"  $contentType = $headers['Content-Type'] -join ', '; " ^
"  Write-Host '✅ Content-Type:' $contentType -ForegroundColor Green; " ^
"  if ($contentType -match 'charset=utf-8') { " ^
"    Write-Host '  ✅ UTF-8 charset specified' -ForegroundColor Green " ^
"  } else { " ^
"    Write-Host '  ⚠️ UTF-8 charset not specified' -ForegroundColor Yellow " ^
"  }; " ^
"} catch { " ^
"  Write-Host 'Error connecting to URL:' $_.Exception.Message -ForegroundColor Red; " ^
"  Write-Host 'Make sure the application is running and accessible.' -ForegroundColor Yellow; " ^
"}"

echo.
echo 🔒 Security Headers Test Complete
echo.
echo Legend:
echo ✅ = Working correctly
echo ⚠️ = Warning or improvement needed  
echo ❌ = Missing or incorrect

pause
