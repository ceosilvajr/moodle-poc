# Postman Testing Files - Moodle API Backend

This document lists all the Postman-related files created for testing the Moodle API Backend.

## 📁 Files Overview

| File | Purpose | Description |
|------|---------|-------------|
| `api-documentation/postman/Moodle_API_Backend.postman_collection.json` | Postman Collection | Complete API collection with all endpoints |
| `api-documentation/postman/POSTMAN_DOCUMENTATION.md` | Documentation | Comprehensive testing guide and troubleshooting |
| `api-documentation/postman/setup-postman-testing.sh` | Setup Script | Automated setup script for Postman testing |
| `moodle-api-backend/test-api.sh` | Testing Script | Basic API testing script (created earlier) |

## 🚀 Quick Start

### Option 1: Automated Setup
```bash
./api-documentation/postman/setup-postman-testing.sh
```

### Option 2: Manual Setup
1. Import `api-documentation/postman/Moodle_API_Backend.postman_collection.json` into Postman
2. Follow instructions in `api-documentation/postman/POSTMAN_DOCUMENTATION.md`
3. Set up environment variables as described

## 📋 File Details

### 1. Moodle_API_Backend.postman_collection.json
- **Type**: Postman Collection
- **Purpose**: Import into Postman for API testing
- **Contains**: All API endpoints organized by category
- **Usage**: Import directly into Postman

### 2. POSTMAN_DOCUMENTATION.md
- **Type**: Documentation
- **Purpose**: Complete testing guide
- **Contains**: 
  - Setup instructions
  - Environment variables
  - Testing scenarios
  - Example responses
  - Troubleshooting guide

### 3. setup-postman-testing.sh
- **Type**: Bash Script
- **Purpose**: Automated setup and testing
- **Features**:
  - Checks Laravel server status
  - Generates new API tokens
  - Provides setup instructions
  - Validates API functionality

### 4. test-api.sh
- **Type**: Bash Script
- **Purpose**: Basic API testing
- **Features**:
  - Tests server connectivity
  - Tests authentication
  - Tests Moodle endpoints
  - Provides detailed output

## 🔧 Environment Variables

The collection uses these variables:

| Variable | Default Value | Description |
|----------|---------------|-------------|
| `base_url` | `http://localhost:8000` | API base URL |
| `api_token` | `1\|t58oT6exgZFTlBaQVS1NliPUaaAVVg0cfFybJbKN26d10f53` | Bearer token |
| `moodle_username` | `your_moodle_username` | Moodle username |
| `moodle_password` | `your_moodle_password` | Moodle password |
| `course_id` | `1` | Course ID for testing |

## 🧪 Testing Workflow

1. **Start Laravel Server**:
   ```bash
   php artisan serve
   ```

2. **Run Setup Script**:
   ```bash
   ./postman/setup-postman-testing.sh
   ```

3. **Import Collection**:
   - Open Postman
   - Import `postman/Moodle_API_Backend.postman_collection.json`

4. **Set Environment Variables**:
   - Use the values provided by the setup script

5. **Start Testing**:
   - Begin with "Test API Connection"
   - Continue with authenticated endpoints

## 📚 Available Endpoints

### Authentication
- `GET /api/test` - Test API connection
- `GET /api/user` - Get authenticated user

### Moodle Account Management
- `POST /api/v1/moodle/auth/link` - Link Moodle account
- `POST /api/v1/moodle/auth/unlink` - Unlink Moodle account

### Course Management
- `GET /api/v1/moodle/courses/enrolled` - Get enrolled courses
- `GET /api/v1/moodle/courses/available` - Get available courses
- `POST /api/v1/moodle/courses/{id}/enroll` - Enroll in course

### Certificates
- `GET /api/v1/moodle/certificates` - Get user certificates

## 🛠️ Troubleshooting

### Common Issues
1. **Server not running**: Run `php artisan serve`
2. **Invalid token**: Run setup script to generate new token
3. **404 errors**: Check if routes are loaded correctly
4. **Moodle errors**: Verify Moodle credentials and server accessibility

### Debug Commands
```bash
# Check server status
curl http://localhost:8000/api/test

# Generate new token
php artisan tinker --execute="use App\Models\User; \$user = User::first(); \$token = \$user->createToken('test')->plainTextToken; echo \$token;"

# Check logs
tail -f storage/logs/laravel.log
```

## 📞 Support

- **Documentation**: See `postman/POSTMAN_DOCUMENTATION.md`
- **Setup Help**: Run `./postman/setup-postman-testing.sh`
- **Basic Testing**: Run `./test-api.sh`
- **Laravel Logs**: Check `storage/logs/laravel.log`

---

**Happy Testing! 🎉** 