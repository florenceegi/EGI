#!/bin/bash
# Test AI Traits API directly

EGI_ID=$1
if [ -z "$EGI_ID" ]; then
    echo "Usage: ./test-ai-traits-api.sh <egi_id>"
    exit 1
fi

echo "Testing AI Traits API for EGI #$EGI_ID"
echo "========================================"

# Get CSRF token (simulate browser)
echo "1. Checking route..."
php artisan route:list | grep "traits.generate"

echo ""
echo "2. Checking EGI exists..."
php artisan tinker --execute="echo 'EGI #$EGI_ID: '; \$egi = App\Models\Egi::find($EGI_ID); echo \$egi ? 'EXISTS' : 'NOT FOUND'; echo PHP_EOL; echo 'token_EGI: '.(\$egi->token_EGI ?? 'NULL'); echo PHP_EOL; echo 'user_id: '.\$egi->user_id; echo PHP_EOL; echo 'main_image_url: '.(\$egi->main_image_url ?? 'NULL');"

echo ""
echo "3. Ready to test!"
echo "   URL: POST /egi/$EGI_ID/dual-arch/traits/generate"
echo "   Body: {\"requested_count\": 5}"
