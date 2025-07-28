# Bruno Collection for Moodle API Backend

This Bruno collection provides a complete testing suite for the Moodle API Backend, allowing you to test all available endpoints with ease.

## 🚀 Quick Start

### 1. Install Bruno
Download and install Bruno from [usebruno.com](https://www.usebruno.com/)

### 2. Import Collection
1. Open Bruno
2. Click "Import Collection"
3. Select `api-documentation/bruno/Moodle_API_Backend.bru`
4. Import the environment variables from `api-documentation/bruno/environment.bru`

### 3. Set Up Environment
1. Open the environment panel in Bruno
2. Update the variables as needed:
   - `base_url`: Your Laravel server URL (default: http://localhost:8000)
   - `api_token`: Your API token (see instructions below)
   - `moodle_username`: Moodle username for testing
   - `moodle_password`: Moodle password for testing
   - `course_id`: Course ID for enrollment testing

### 4. Start Testing
Run requests directly from Bruno to test your API endpoints!

## 🔑 Getting an API Token

### Method 1: Using Laravel Tinker
```bash
# Start Laravel Tinker
php artisan tinker

# Create a user (if needed)
User::create([
    'name' => 'Test User', 
    'email' => 'test@example.com', 
    'password' => Hash::make('password')
]);

# Generate API token
$user = User::first();
$token = $user->createToken('test-token')->plainTextToken;
echo $token;
```

### Method 2: Using the Setup Script
```bash
# Run the automated setup script
./api-documentation/bruno/setup-bruno-testing.sh
```

## 📋 Available Endpoints

### Authentication
- **Link Moodle Account**: `POST /api/v1/moodle/auth/link`
- **Unlink Moodle Account**: `POST /api/v1/moodle/auth/unlink`

### User Management
- **Get User Info**: `GET /api/user`

### Course Management
- **Get Enrolled Courses**: `GET /api/v1/moodle/courses/enrolled`
- **Get Available Courses**: `GET /api/v1/moodle/courses/available`
- **Enroll in Course**: `POST /api/v1/moodle/courses/{courseId}/enroll`

### Certificates
- **Get Certificates**: `GET /api/v1/moodle/certificates`

### Testing
- **Test API**: `GET /test`

## 🧪 Testing Workflow

### 1. Basic Setup Test
1. Run "Test API" to verify server is running
2. Run "Get User Info" to verify authentication

### 2. Moodle Integration Test
1. Run "Link Moodle Account" with valid credentials
2. Run "Get Enrolled Courses" to see linked courses
3. Run "Get Available Courses" to see all accessible courses

### 3. Course Management Test
1. Run "Enroll in Course" to test enrollment
2. Run "Get Certificates" to check certificate access

### 4. Cleanup Test
1. Run "Unlink Moodle Account" to test unlinking

## 🔧 Environment Variables

| Variable | Description | Default Value |
|----------|-------------|---------------|
| `base_url` | Laravel server URL | `http://localhost:8000` |
| `api_token` | Bearer token for authentication | `6|k48RXWiN274ZJPNjRkh3oXZ4XwXokDHuecPwYeOsf0cec13e` |
| `moodle_username` | Moodle username for testing | `student` |
| `moodle_password` | Moodle password for testing | `moodle` |
| `course_id` | Course ID for enrollment testing | `72` |

## 📝 Expected Responses

### Successful Authentication
```json
{
  "status": "success",
  "message": "Moodle account linked successfully."
}
```

### Enrolled Courses
```json
[
  {
    "id": 72,
    "shortname": "gremlins",
    "fullname": "Gremlins at sections",
    "status": "in-progress"
  }
]
```

### Available Courses (with fallback)
```json
{
  "courses": [...],
  "message": "Limited access: Only enrolled courses are available.",
  "permission_level": "enrolled_only"
}
```

## 🚨 Troubleshooting

### Common Issues

1. **"Unauthorized" Error**
   - Check if your API token is valid
   - Verify the token format in environment variables

2. **"Moodle account not linked"**
   - First run "Link Moodle Account" request
   - Verify Moodle credentials are correct

3. **"Connection refused"**
   - Ensure Laravel server is running (`php artisan serve`)
   - Check if the `base_url` is correct

4. **"Permission denied" for courses**
   - This is normal for demo accounts
   - The API will automatically fallback to enrolled courses

### Debug Tips

1. **Check Server Logs**
   ```bash
   # View Laravel logs
   tail -f storage/logs/laravel.log
   ```

2. **Verify Environment**
   ```bash
   # Check if environment variables are loaded
   php artisan tinker
   echo env('MOODLE_BASE_URL');
   ```

3. **Test Direct API Call**
   ```bash
   curl -X GET "http://localhost:8000/test"
   ```

## 🔄 Workflow Integration

### Development Workflow
1. Make changes to your API
2. Test with Bruno collection
3. Verify all endpoints work as expected
4. Commit your changes

### CI/CD Integration
- Bruno collections can be exported and used in CI/CD pipelines
- Environment variables can be set from CI/CD environment
- Automated testing can be performed using Bruno CLI

## 📚 Additional Resources

- [Bruno Documentation](https://www.usebruno.com/docs)
- [Laravel API Documentation](https://laravel.com/docs/api)
- [Moodle Web Services Documentation](https://docs.moodle.org/dev/Web_services)

---

**Happy Testing! 🚀**

Use this collection to thoroughly test your Moodle API Backend and ensure all endpoints work correctly. 