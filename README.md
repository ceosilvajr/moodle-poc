# Moodle API Backend

A comprehensive Laravel-based REST API backend that integrates with Moodle Learning Management System, providing seamless access to Moodle courses, authentication, and certificates through a mobile-friendly API.

## 🚀 Features

- **Moodle Integration**: Connect to any Moodle instance via web services
- **User Authentication**: Link mobile users with Moodle accounts
- **Course Management**: Access enrolled and available courses
- **Certificate Management**: Retrieve user certificates
- **RESTful API**: Clean, consistent API endpoints
- **Multiple Testing Options**: Bruno and Postman collections included

## 📋 Prerequisites

- **PHP 8.2+**
- **Composer**
- **SQLite** (for local development)
- **Bruno** (for API testing) - [Download Bruno](https://www.usebruno.com/)
- **Postman** (alternative testing) - [Download Postman](https://www.postman.com/)

## 🏗️ Project Structure

```
moodle-backend/
├── moodle-api-backend/           # Laravel application
│   ├── app/                     # Application code
│   ├── database/                # Database files
│   ├── routes/                  # API routes
│   └── ...
├── api-documentation/           # API testing collections
│   ├── bruno/                   # Bruno collection files
│   └── postman/                 # Postman collection files
└── README.md                    # This file
```

## 🛠️ Building and Running the Application

### 1. Clone and Setup

```bash
# Clone the repository
git clone <your-repo-url>
cd moodle-backend

# Navigate to the Laravel application
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

Edit `moodle-api-backend/.env` file and set your Moodle configuration:

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

## 🧪 Testing the API

We provide two complete testing suites for the API:

### Option 1: Bruno Collection (Recommended)

Bruno is a modern, fast API testing tool with a great developer experience.

#### Quick Start with Bruno

1. **Install Bruno**: Download from [usebruno.com](https://www.usebruno.com/)

2. **Run the automated setup**:
   ```bash
   # From the root directory
   ./api-documentation/bruno/setup-bruno-testing.sh
   ```

3. **Import the collection**:
   - Open Bruno
   - Import `api-documentation/bruno/Moodle_API_Backend.bru`
   - Import environment from `api-documentation/bruno/environment.bru`

4. **Start testing**:
   - Run "Test API" to verify connection
   - Run "Get User Info" to test authentication
   - Run "Link Moodle Account" to connect to Moodle

#### Bruno Collection Features

- ✅ **Automated setup** with token generation
- ✅ **Pre-configured environment** variables
- ✅ **All API endpoints** included
- ✅ **Detailed documentation** and troubleshooting
- ✅ **Modern interface** with great UX

### Option 2: Postman Collection

Postman is a popular API testing tool with extensive features.

#### Quick Start with Postman

1. **Install Postman**: Download from [postman.com](https://www.postman.com/)

2. **Run the automated setup**:
   ```bash
   # From the root directory
   ./api-documentation/postman/setup-postman-testing.sh
   ```

3. **Import the collection**:
   - Open Postman
   - Import `api-documentation/postman/Moodle_API_Backend.postman_collection.json`
   - Set up environment variables as prompted

4. **Start testing**:
   - Begin with "Test API Connection"
   - Test "Get Authenticated User"
   - Continue with other endpoints

#### Postman Collection Features

- ✅ **Complete API collection** with all endpoints
- ✅ **Automated setup script** for quick configuration
- ✅ **Comprehensive documentation** and examples
- ✅ **Environment variables** management
- ✅ **Request/response examples**

## 📚 API Endpoints

### Authentication

| Method | Endpoint | Description |
|--------|----------|-------------|
| `POST` | `/api/v1/moodle/auth/link` | Link mobile user with Moodle account |
| `POST` | `/api/v1/moodle/auth/unlink` | Unlink Moodle account |

### User Management

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/user` | Get authenticated user info |

### Course Management

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/v1/moodle/courses/enrolled` | Get user's enrolled courses |
| `GET` | `/api/v1/moodle/courses/available` | Get available courses (with fallback) |
| `POST` | `/api/v1/moodle/courses/{courseId}/enroll` | Enroll user in a course |

### Certificates

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/api/v1/moodle/certificates` | Get user's certificates |

### Testing

| Method | Endpoint | Description |
|--------|----------|-------------|
| `GET` | `/test` | Test API connection |

## 🔧 Getting an API Token

### Method 1: Using Setup Scripts (Recommended)

Both Bruno and Postman setup scripts automatically generate API tokens for you.

### Method 2: Manual Token Generation

```bash
# Navigate to the Laravel application
cd moodle-api-backend

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

## 🗄️ Database Access

### Using Laravel Tinker

```bash
cd moodle-api-backend
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
cd moodle-api-backend
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
   cd moodle-api-backend && php artisan serve
   ```

### Debug Mode

Enable debug mode in `moodle-api-backend/.env`:
```env
APP_DEBUG=true
APP_ENV=local
```

## 📁 Documentation Files

### API Testing Collections

| Collection | Location | Description |
|------------|----------|-------------|
| **Bruno** | `api-documentation/bruno/` | Modern API testing tool |
| **Postman** | `api-documentation/postman/` | Popular API testing tool |

### Documentation Files

| File | Purpose | Description |
|------|---------|-------------|
| `README.md` | Main documentation | This file - project overview |
| `moodle-api-backend/README.md` | Laravel app docs | Application-specific documentation |
| `api-documentation/bruno/README.md` | Bruno docs | Bruno-specific testing guide |
| `api-documentation/postman/README.md` | Postman docs | Postman-specific testing guide |

## 🔄 Development Workflow

1. **Start Development**:
   ```bash
   cd moodle-api-backend
   php artisan serve
   ```

2. **Test with Collections**:
   - Use Bruno: `./api-documentation/bruno/setup-bruno-testing.sh`
   - Use Postman: `./api-documentation/postman/setup-postman-testing.sh`

3. **Database Changes**:
   ```bash
   cd moodle-api-backend
   php artisan make:migration create_new_table
   php artisan migrate
   ```

4. **API Changes**:
   - Modify controllers in `moodle-api-backend/app/Http/Controllers/`
   - Update routes in `moodle-api-backend/routes/api.php`
   - Test with collections

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
4. Test with Bruno or Postman collections
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License.

## 🆘 Support

For issues and questions:
- Check the troubleshooting section
- Review the collection documentation
- Open an issue in the repository

---

## 🚀 Quick Start Summary

1. **Build & Run**:
   ```bash
   cd moodle-api-backend
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   php artisan serve
   ```

2. **Test with Bruno**:
   ```bash
   ./api-documentation/bruno/setup-bruno-testing.sh
   ```

3. **Test with Postman**:
   ```bash
   ./api-documentation/postman/setup-postman-testing.sh
   ```

**Happy Testing! 🎉**

Choose your preferred testing tool and start exploring the Moodle API Backend! 