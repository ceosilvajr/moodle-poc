#!/bin/bash

# Test script for Moodle API Backend
# Make sure the Laravel server is running on http://localhost:8000

API_BASE="http://localhost:8000/api"
TOKEN="1|t58oT6exgZFTlBaQVS1NliPUaaAVVg0cfFybJbKN26d10f53"

echo "🧪 Testing Moodle API Backend"
echo "=============================="
echo ""

# Test 1: Check if the server is running
echo "1. Testing server connectivity..."
if curl -s http://localhost:8000 > /dev/null; then
    echo "✅ Server is running on http://localhost:8000"
else
    echo "❌ Server is not running. Please start with: php artisan serve"
    exit 1
fi
echo ""

# Test 2: Test user endpoint (requires authentication)
echo "2. Testing authenticated user endpoint..."
USER_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" "$API_BASE/user")
if echo "$USER_RESPONSE" | grep -q "id"; then
    echo "✅ User authentication working"
    echo "   User data: $(echo "$USER_RESPONSE" | jq -r '.name // "N/A"')"
else
    echo "❌ User authentication failed"
    echo "   Response: $USER_RESPONSE"
fi
echo ""

# Test 3: Test Moodle auth endpoints (will fail without Moodle connection, but should return proper error)
echo "3. Testing Moodle auth endpoints..."
echo "   Testing unlink endpoint (should work without Moodle connection)..."
UNLINK_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" -X POST "$API_BASE/v1/moodle/auth/unlink")
if echo "$UNLINK_RESPONSE" | grep -q "success"; then
    echo "✅ Unlink endpoint working"
else
    echo "⚠️  Unlink endpoint response: $UNLINK_RESPONSE"
fi
echo ""

# Test 4: Test course endpoints (will fail without Moodle connection, but should return proper error)
echo "4. Testing course endpoints..."
echo "   Testing enrolled courses endpoint..."
COURSES_RESPONSE=$(curl -s -H "Authorization: Bearer $TOKEN" "$API_BASE/v1/moodle/courses/enrolled")
if echo "$COURSES_RESPONSE" | grep -q "error"; then
    echo "✅ Course endpoint responding (expected error without Moodle connection)"
    echo "   Response: $(echo "$COURSES_RESPONSE" | jq -r '.error // "N/A"')"
else
    echo "⚠️  Unexpected response: $COURSES_RESPONSE"
fi
echo ""

echo "🎉 API testing completed!"
echo ""
echo "📝 Next steps:"
echo "1. Configure your Moodle instance URL in .env file"
echo "2. Set up Moodle Web Services with required functions"
echo "3. Test with real Moodle credentials"
echo ""
echo "🔗 API Documentation:"
echo "- Base URL: $API_BASE"
echo "- Test Token: $TOKEN"
echo "- Available endpoints:"
echo "  - POST /v1/moodle/auth/link"
echo "  - POST /v1/moodle/auth/unlink"
echo "  - GET  /v1/moodle/courses/enrolled"
echo "  - GET  /v1/moodle/courses/available"
echo "  - POST /v1/moodle/courses/{course_id}/enroll"
echo "  - GET  /v1/moodle/certificates" 