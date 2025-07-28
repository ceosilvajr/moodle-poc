#!/bin/bash

# Bruno Testing Setup Script for Moodle API Backend
# This script helps set up the environment for testing with Bruno

set -e

echo "🚀 Bruno Testing Setup for Moodle API Backend"
echo "=============================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}✅ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

print_error() {
    echo -e "${RED}❌ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# Check if we're in the right directory
if [ -f "moodle-api-backend/artisan" ]; then
    print_status "Found Laravel project in: moodle-api-backend/"
    cd moodle-api-backend
elif [ -f "artisan" ]; then
    print_status "Found Laravel project in: $(pwd)"
elif [ -f "../moodle-api-backend/artisan" ]; then
    print_status "Found Laravel project in: ../moodle-api-backend/"
    cd ../moodle-api-backend
else
    print_error "Please run this script from the moodle-backend directory"
    echo "   Current directory: $(pwd)"
    echo "   Expected: moodle-backend directory with moodle-api-backend subdirectory"
    exit 1
fi

# Check if Laravel server is running
echo ""
print_info "Checking if Laravel server is running..."

if curl -s http://localhost:8000/test > /dev/null 2>&1; then
    print_status "Laravel server is running on http://localhost:8000"
else
    print_warning "Laravel server is not running"
    echo "   Starting Laravel server..."
    php artisan serve > /dev/null 2>&1 &
    sleep 3
    
    if curl -s http://localhost:8000/test > /dev/null 2>&1; then
        print_status "Laravel server started successfully"
    else
        print_error "Failed to start Laravel server"
        echo "   Please start it manually: php artisan serve"
        exit 1
    fi
fi

# Check if Bruno collection files exist
echo ""
print_info "Checking Bruno collection files..."

if [ -f "../api-documentation/bruno/Moodle_API_Backend.bru" ]; then
    print_status "Bruno collection file found: ../api-documentation/bruno/Moodle_API_Backend.bru"
else
    print_error "Bruno collection file not found"
    echo "   Please ensure Moodle_API_Backend.bru exists in the api-documentation/bruno directory"
    exit 1
fi

if [ -f "../api-documentation/bruno/environment.bru" ]; then
    print_status "Bruno environment file found: ../api-documentation/bruno/environment.bru"
else
    print_error "Bruno environment file not found"
    echo "   Please ensure environment.bru exists in the api-documentation/bruno directory"
    exit 1
fi

# Check if we have users in the database
echo ""
print_info "Checking database for users..."

USER_COUNT=$(php artisan tinker --execute="echo User::count();" 2>/dev/null | tail -1 || echo "0")

if [ "$USER_COUNT" -gt 0 ] 2>/dev/null; then
    print_status "Found $USER_COUNT user(s) in database"
else
    print_warning "No users found in database"
    echo "   Creating a test user..."
    
    php artisan tinker --execute="
    try {
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);
        echo 'Test user created successfully';
    } catch (Exception \$e) {
        echo 'User might already exist or error occurred';
    }
    " 2>/dev/null || print_warning "Could not create test user (might already exist)"
fi

# Generate API token
echo ""
print_info "Generating API token..."

API_TOKEN=$(php artisan tinker --execute="
try {
    \$user = User::first();
    if (\$user) {
        \$token = \$user->createToken('bruno-test-token')->plainTextToken;
        echo \$token;
    } else {
        echo 'No user found';
    }
} catch (Exception \$e) {
    echo 'Error generating token: ' . \$e->getMessage();
}
" 2>/dev/null | tail -1)

if [[ "$API_TOKEN" == *"|"* ]]; then
    print_status "API token generated successfully"
    echo "   Token: $API_TOKEN"
else
    print_error "Failed to generate API token"
    echo "   Error: $API_TOKEN"
    exit 1
fi

# Test the API token
echo ""
print_info "Testing API token..."

if curl -s -H "Authorization: Bearer $API_TOKEN" http://localhost:8000/api/user > /dev/null 2>&1; then
    print_status "API token is working correctly"
else
    print_warning "API token test failed"
    echo "   This might be normal if the user doesn't have a linked Moodle account"
fi

# Update environment file with new token
echo ""
print_info "Updating Bruno environment file..."

# Create a backup of the original environment file
cp ../api-documentation/bruno/environment.bru ../api-documentation/bruno/environment.bru.backup

# Update the token in the environment file
awk -v token="$API_TOKEN" '/api_token:/ {print "  api_token: " token; next} {print}' ../api-documentation/bruno/environment.bru > ../api-documentation/bruno/environment.bru.tmp && mv ../api-documentation/bruno/environment.bru.tmp ../api-documentation/bruno/environment.bru

if [ $? -eq 0 ]; then
    print_status "Bruno environment file updated with new API token"
else
    print_warning "Could not update environment file automatically"
    echo "   Please manually update 'api_token' in bruno/environment.bru"
    echo "   New token: $API_TOKEN"
fi

# Display setup instructions
echo ""
echo "🎉 Setup Complete!"
echo "=================="
print_status "Laravel server is running on http://localhost:8000"
print_status "API token generated and configured"
print_status "Bruno collection files are ready"

echo ""
echo "📋 Next Steps:"
echo "=============="
echo "1. 📥 Install Bruno:"
echo "   - Download from https://www.usebruno.com/"
echo "   - Install and open Bruno"
echo ""
echo "2. 📂 Import Collection:"
echo "   - Click 'Import Collection' in Bruno"
echo "   - Select 'api-documentation/bruno/Moodle_API_Backend.bru'"
echo "   - Import environment from 'api-documentation/bruno/environment.bru'"
echo ""
echo "3. 🧪 Start Testing:"
echo "   - Run 'Test API' to verify connection"
echo "   - Run 'Get User Info' to test authentication"
echo "   - Run 'Link Moodle Account' to connect to Moodle"
echo ""
echo "4. 📚 Read Documentation:"
echo "   - See 'api-documentation/bruno/README.md' for detailed instructions"
echo "   - See main 'README.md' for project overview"
echo ""

# Check if Bruno is installed
if command -v bruno &> /dev/null; then
    print_status "Bruno CLI is installed"
    echo "   You can also run: bruno run bruno/Moodle_API_Backend.bru"
else
    print_info "Bruno CLI not found"
    echo "   Install Bruno CLI for command-line testing"
fi

echo ""
print_info "Environment Variables:"
echo "   base_url: http://localhost:8000"
echo "   api_token: $API_TOKEN"
echo "   moodle_username: student"
echo "   moodle_password: moodle"
echo "   course_id: 72"

echo ""
echo "🚀 Happy Testing!" 