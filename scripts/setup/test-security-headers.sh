#!/bin/bash

# Security Headers Test Script
# Tests that all required security headers are present

echo "🔒 Testing Security Headers for MyUKM Application"
echo "================================================="

# Default URL - change this to your actual domain
URL="${1:-http://localhost/MyUkm-main/public}"

echo "Testing URL: $URL"
echo ""

# Function to check header
check_header() {
    local header_name="$1"
    local expected_pattern="$2"
    
    header_value=$(curl -s -I "$URL" | grep -i "$header_name:" | head -n1)
    
    if [ -n "$header_value" ]; then
        echo "✅ $header_name: Found"
        echo "   $header_value"
        
        if [ -n "$expected_pattern" ]; then
            if echo "$header_value" | grep -qi "$expected_pattern"; then
                echo "   ✅ Value matches expected pattern"
            else
                echo "   ⚠️  Value may not match expected pattern: $expected_pattern"
            fi
        fi
    else
        echo "❌ $header_name: Not found"
    fi
    echo ""
}

echo "Checking Security Headers:"
echo "-------------------------"

check_header "X-Content-Type-Options" "nosniff"
check_header "X-Frame-Options" "DENY"
check_header "X-XSS-Protection" "1; mode=block"
check_header "Referrer-Policy" "strict-origin-when-cross-origin"
check_header "Content-Security-Policy" "default-src"

echo "Checking Cookie Security:"
echo "------------------------"

# Check Set-Cookie header for security flags
cookie_header=$(curl -s -I "$URL" | grep -i "set-cookie:" | head -n1)
if [ -n "$cookie_header" ]; then
    echo "✅ Set-Cookie: Found"
    echo "   $cookie_header"
    
    if echo "$cookie_header" | grep -qi "secure"; then
        echo "   ✅ Secure flag present"
    else
        echo "   ⚠️  Secure flag missing (expected in production)"
    fi
    
    if echo "$cookie_header" | grep -qi "httponly"; then
        echo "   ✅ HttpOnly flag present"
    else
        echo "   ❌ HttpOnly flag missing"
    fi
    
    if echo "$cookie_header" | grep -qi "samesite"; then
        echo "   ✅ SameSite attribute present"
    else
        echo "   ⚠️  SameSite attribute missing"
    fi
else
    echo "⚠️  Set-Cookie: Not found (may require login)"
fi
echo ""

echo "Checking Cache Control:"
echo "----------------------"

check_header "Cache-Control" ""

# Check for deprecated Expires header
expires_header=$(curl -s -I "$URL" | grep -i "expires:" | head -n1)
if [ -n "$expires_header" ]; then
    echo "⚠️  Expires header found (should be removed):"
    echo "   $expires_header"
else
    echo "✅ Expires header not present (good)"
fi
echo ""

echo "Checking Content-Type:"
echo "---------------------"

content_type=$(curl -s -I "$URL" | grep -i "content-type:" | head -n1)
if [ -n "$content_type" ]; then
    echo "✅ Content-Type: Found"
    echo "   $content_type"
    
    if echo "$content_type" | grep -qi "charset=utf-8"; then
        echo "   ✅ UTF-8 charset specified"
    else
        echo "   ⚠️  UTF-8 charset not specified"
    fi
else
    echo "❌ Content-Type: Not found"
fi
echo ""

echo "🔒 Security Headers Test Complete"
echo ""
echo "Legend:"
echo "✅ = Working correctly"
echo "⚠️  = Warning or improvement needed"
echo "❌ = Missing or incorrect"
