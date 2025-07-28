#!/bin/bash

# Postman Testing Setup Script for Moodle API Backend
# This script helps you set up and start testing the API

echo "🚀 Moodle API Backend - Postman Testing Setup"
echo "=============================================="
echo ""

# Check if we're in the right directory (either in postman folder or root)
if [ -f "artisan" ]; then
    echo "✅ Found Laravel project in: $(pwd)"
elif [ -f "../artisan" ]; then
    echo "✅ Found Laravel project in: $(dirname $(pwd))"
    cd ..
else
    echo "❌ Error: Please run this script from the moodle-api-backend directory or postman subdirectory"
    echo "   Current directory: $(pwd)"
    echo "   Expected: moodle-api-backend directory with artisan file"
    exit 1
fi
echo ""

# Check if server is running
echo "🔍 Checking if Laravel server is running..."
if curl -s http://localhost:8000/api/test > /dev/null 2>&1; then
    echo "✅ Laravel server is running on http://localhost:8000"
else
    echo "⚠️  Laravel server is not running"
    echo ""
    echo "📋 To start the server, run:"
    echo "   php artisan serve"
    echo ""
    read -p "Would you like to start the server now? (y/n): " -n 1 -r
    echo ""
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo "🚀 Starting Laravel server..."
        php artisan serve > /dev/null 2>&1 &
        SERVER_PID=$!
        echo "✅ Server started with PID: $SERVER_PID"
        echo "   You can stop it later with: kill $SERVER_PID"
        sleep 3
    else
        echo "❌ Please start the server manually before testing"
        exit 1
    fi
fi

echo ""

# Test API connection
echo "🧪 Testing API connection..."
API_RESPONSE=$(curl -s http://localhost:8000/api/test)
if echo "$API_RESPONSE" | grep -q "API routes are working"; then
    echo "✅ API is responding correctly"
    echo "   Response: $API_RESPONSE"
else
    echo "❌ API is not responding correctly"
    echo "   Response: $API_RESPONSE"
    exit 1
fi

echo ""

# Check if test user exists
echo "👤 Checking test user..."
USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null)
if [ "$USER_COUNT" -gt 0 ]; then
    echo "✅ Test user exists in database"
    
    # Generate new token
    echo "🔑 Generating new API token..."
    NEW_TOKEN=$(php artisan tinker --execute="use App\Models\User; \$user = User::first(); \$token = \$user->createToken('postman-test')->plainTextToken; echo \$token;" 2>/dev/null)
    
    if [ ! -z "$NEW_TOKEN" ]; then
        echo "✅ New API token generated:"
        echo "   $NEW_TOKEN"
        echo ""
        echo "📝 Update your Postman environment variable 'api_token' with this value"
    else
        echo "⚠️  Could not generate new token, using existing one"
    fi
else
    echo "❌ No users found in database"
    echo ""
    echo "📋 To create a test user, run:"
    echo "   php artisan tinker --execute=\"use App\Models\User; User::create(['name' => 'Test User', 'email' => 'test@example.com', 'password' => bcrypt('password')]);\""
    exit 1
fi

echo ""

# Display Postman setup instructions
echo "📋 Postman Setup Instructions"
echo "============================"
echo ""
echo "1. 📥 Import the Postman collection:"
echo "   - Open Postman"
echo "   - Click 'Import'"
echo "   - Select 'postman/Moodle_API_Backend.postman_collection.json'"
echo ""
echo "2. 🔧 Set up environment variables:"
echo "   - Click the Environment dropdown (top right)"
echo "   - Select 'New Environment'"
echo "   - Add these variables:"
echo ""
echo "   Variable: base_url"
echo "   Value: http://localhost:8000"
echo ""
echo "   Variable: api_token"
echo "   Value: $NEW_TOKEN"
echo ""
echo "   Variable: moodle_username"
echo "   Value: your_moodle_username"
echo ""
echo "   Variable: moodle_password"
echo "   Value: your_moodle_password"
echo ""
echo "   Variable: course_id"
echo "   Value: 1"
echo ""
echo "3. 🧪 Start testing:"
echo "   - Begin with 'Test API Connection' (no auth required)"
echo "   - Then test 'Get Authenticated User'"
echo "   - Continue with other endpoints"
echo ""

# Check if Postman collection file exists
if [ -f "postman/Moodle_API_Backend.postman_collection.json" ]; then
    echo "✅ Postman collection file found: postman/Moodle_API_Backend.postman_collection.json"
elif [ -f "Moodle_API_Backend.postman_collection.json" ]; then
    echo "✅ Postman collection file found: Moodle_API_Backend.postman_collection.json"
else
    echo "❌ Postman collection file not found"
    echo "   Please ensure Moodle_API_Backend.postman_collection.json exists in the postman directory"
fi

echo ""

# Display available endpoints
echo "🔗 Available API Endpoints"
echo "=========================="
echo ""
echo "🔐 Authentication:"
echo "   GET  /api/test                    - Test API connection"
echo "   GET  /api/user                    - Get authenticated user"
echo ""
echo "🔗 Moodle Account Management:"
echo "   POST /api/v1/moodle/auth/link     - Link Moodle account"
echo "   POST /api/v1/moodle/auth/unlink   - Unlink Moodle account"
echo ""
echo "📚 Course Management:"
echo "   GET  /api/v1/moodle/courses/enrolled   - Get enrolled courses"
echo "   GET  /api/v1/moodle/courses/available  - Get available courses"
echo "   POST /api/v1/moodle/courses/{id}/enroll - Enroll in course"
echo ""
echo "🏆 Certificates:"
echo "   GET  /api/v1/moodle/certificates  - Get user certificates"
echo ""

echo "🎉 Setup complete! You're ready to test the API with Postman."
echo ""
echo "📖 For detailed documentation, see: POSTMAN_DOCUMENTATION.md"
echo "🛠️  For troubleshooting, see the documentation or run: ./test-api.sh" 