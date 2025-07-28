# Moodle API Backend

A Laravel-based REST API backend that integrates with Moodle Learning Management System, providing seamless access to Moodle courses, authentication, and certificates through a mobile-friendly API.

## 🚀 Features

- **Moodle Integration**: Connect to any Moodle instance via web services
- **User Authentication**: Link mobile users with Moodle accounts
- **Course Management**: Access enrolled and available courses
- **Certificate Management**: Retrieve user certificates
- **RESTful API**: Clean, consistent API endpoints
- **Bruno Collection**: Complete API testing suite included

## 📋 Prerequisites

- **PHP 8.2+**
- **Composer**
- **SQLite** (for local development)

## 🛠️ Installation & Setup

### 1. Clone and Install Dependencies

```bash
# Clone the repository
git clone <your-repo-url>
cd moodle-api-backend

# Install PHP dependencies
composer install
```

### 2. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Configure Moodle Settings

Edit `.env` file and set your Moodle configuration:

```env
# Moodle Configuration
MOODLE_BASE_URL=https://your-moodle-instance.com
MOODLE_MOBILE_SERVICE_SHORTNAME=moodle_mobile_app
```

### 4. Database Setup

```bash
# Create SQLite database
touch database/database.sqlite

# Run migrations
php artisan migrate
```

### 5. Start the Development Server

```bash
# Start Laravel development server
php artisan serve
```

The API will be available at: `http://localhost:8000`

## 🧪 API Testing

For API testing documentation and collections, see the root directory:
- **Bruno Collection**: `../api-documentation/bruno/`
- **Postman Collection**: `../api-documentation/postman/`
- **Main Documentation**: `../README.md`

## 📚 API Endpoints

### Authentication

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/api/v1/moodle/auth/link` | Link mobile user with Moodle account |
| `POST` | `/api/v1/moodle/auth/unlink` | Unlink Moodle account |

### Courses

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/v1/moodle/courses/enrolled` | Get user's enrolled courses |
| `GET` | `/api/v1/moodle/courses/available` | Get available courses (with fallback) |
| `POST` | `/api/v1/moodle/courses/{courseId}/enroll` | Enroll user in a course |

### Certificates

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/v1/moodle/certificates` | Get user's certificates |

### User Management

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/user` | Get authenticated user info |

## 🔧 Testing Examples

### Link Moodle Account

```bash
curl -X POST "http://localhost:8000/api/v1/moodle/auth/link" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "moodle_username": "student",
    "moodle_password": "moodle"
  }'
```

### Get Enrolled Courses

```bash
curl -X GET "http://localhost:8000/api/v1/moodle/courses/enrolled" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Get Available Courses

```bash
curl -X GET "http://localhost:8000/api/v1/moodle/courses/available" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 🗄️ Database Access

### Using Laravel Tinker

```bash
# Access the database
php artisan tinker

# View all users
User::all();

# View Moodle tokens
UserMoodleToken::all();

# Count API tokens
User::first()->tokens()->count();
```

### Direct SQLite Access

```bash
# Access SQLite database directly
sqlite3 database/database.sqlite

# View tables
.tables

# View users
SELECT * FROM users;

# View Moodle tokens
SELECT * FROM user_moodle_tokens;
```

## 🚨 Troubleshooting

### Common Issues

1. **"CSRF token mismatch"**
   - Ensure you're using the correct API endpoints (not web routes)
   - Check that your Authorization header is properly set

2. **"Moodle account not linked"**
   - First link your account using `/api/v1/moodle/auth/link`
   - Verify Moodle credentials are correct

3. **"Permission denied" for courses**
   - This is normal for demo accounts
   - The API will fallback to enrolled courses automatically

4. **Server not starting**
   ```bash
   # Check if port 8000 is in use
   lsof -i :8000
   
   # Kill existing process
   pkill -f "php artisan serve"
   
   # Start server
   php artisan serve
   ```

### Debug Mode

Enable debug mode in `.env`:
```env
APP_DEBUG=true
APP_ENV=local
```

## 📁 Project Structure

```
moodle-api-backend/
├── app/
│   ├── Http/Controllers/
│   │   ├── MoodleAuthController.php
│   │   ├── MoodleCourseController.php
│   │   └── MoodleCertificateController.php
│   ├── Models/
│   │   └── UserMoodleToken.php
│   └── Services/
│       └── MoodleApiService.php
├── database/
│   ├── migrations/
│   └── database.sqlite
├── routes/
│   └── api.php
├── .env.example
├── composer.json
└── README.md
```

## 🔄 Development Workflow

1. **Start Development**:
   ```bash
   php artisan serve
   ```

2. **Test with Bruno**:
   - Open Bruno
   - Import collection
   - Set environment variables
   - Run tests

3. **Database Changes**:
   ```bash
   php artisan make:migration create_new_table
   php artisan migrate
   ```

4. **API Changes**:
   - Modify controllers in `app/Http/Controllers/`
   - Update routes in `routes/api.php`
   - Test with Bruno collection

## 📝 API Response Format

All API responses follow this format:

```json
{
  "status": "success|error",
  "message": "Human readable message",
  "data": {}, // Optional data payload
  "error": "Error details if applicable"
}
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test with Bruno collection
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License.

## 🆘 Support

For issues and questions:
- Check the troubleshooting section
- Review the Bruno collection documentation
- Open an issue in the repository

---

**Happy Testing! 🚀**

Use the Bruno collection to explore all available endpoints and test your Moodle integration thoroughly.
