# Moodle API Backend

A Laravel-based API backend that provides integration with Moodle Learning Management System. This backend allows mobile applications to interact with Moodle through a RESTful API, handling authentication, course management, and certificate retrieval.

## Features

- **Moodle Account Linking**: Link mobile app users to their Moodle accounts
- **Course Management**: Fetch enrolled courses, available courses, and enroll in new courses
- **Certificate Retrieval**: Get user certificates from completed courses
- **Docker Support**: Complete Docker environment with PHP-FPM, Nginx, and MySQL
- **API Authentication**: Laravel Sanctum for secure API access

## Prerequisites

- Docker and Docker Compose
- PHP 8.2+ (for local development)
- Composer (for local development)
- Postman (for API testing)

## Quick Start with Docker

1. **Clone and navigate to the project**:
   ```bash
   cd moodle-api-backend
   ```

2. **Configure environment variables**:
   ```bash
   cp .env.example .env
   ```
   
   Update the `.env` file with your Moodle configuration:
   ```env
   MOODLE_BASE_URL=https://your-moodle-instance.com
   MOODLE_MOBILE_SERVICE_SHORTNAME=moodle_mobile_app
   DB_DATABASE=moodle_api_db
   DB_USERNAME=moodle_user
   DB_PASSWORD=moodle_password
   DB_ROOT_PASSWORD=moodle_root_password
   ```

3. **Start the Docker containers**:
   ```bash
   docker-compose up -d
   ```

4. **Install dependencies and run migrations**:
   ```bash
   docker-compose exec app composer install
   docker-compose exec app php artisan migrate
   ```

5. **Generate application key**:
   ```bash
   docker-compose exec app php artisan key:generate
   ```

6. **Access the API**:
   - API Base URL: `http://localhost:8000/api`
   - Database: `localhost:3307` (MySQL)

## API Endpoints

### Authentication Required
All endpoints require authentication via Laravel Sanctum. Include the Bearer token in the Authorization header.

### Moodle Account Management

#### Link Moodle Account
```http
POST /api/v1/moodle/auth/link
Content-Type: application/json
Authorization: Bearer {token}

{
    "moodle_username": "user@example.com",
    "moodle_password": "password"
}
```

#### Unlink Moodle Account
```http
POST /api/v1/moodle/auth/unlink
Authorization: Bearer {token}
```

### Course Management

#### Get Enrolled Courses
```http
GET /api/v1/moodle/courses/enrolled
Authorization: Bearer {token}
```

#### Get Available Courses
```http
GET /api/v1/moodle/courses/available
Authorization: Bearer {token}
```

#### Enroll in Course
```http
POST /api/v1/moodle/courses/{course_id}/enroll
Authorization: Bearer {token}
```

### Certificates

#### Get User Certificates
```http
GET /api/v1/moodle/certificates
Authorization: Bearer {token}
```

## Local Development Setup

1. **Install PHP dependencies**:
   ```bash
   composer install
   ```

2. **Configure environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Set up database**:
   ```bash
   php artisan migrate
   ```

4. **Start development server**:
   ```bash
   php artisan serve
   ```

## Docker Services

- **app**: PHP-FPM 8.2 with Laravel application
- **nginx**: Nginx web server (port 8000)
- **db**: MySQL 8.0 database (port 3307)

## Configuration

### Moodle Setup

1. **Enable Web Services** in your Moodle instance
2. **Create a Mobile Service** with shortname `moodle_mobile_app`
3. **Enable required Web Service functions**:
   - `core_webservice_get_site_info`
   - `core_enrol_get_users_courses`
   - `core_course_get_courses`
   - `core_course_get_contents`
   - `core_completion_get_course_completion_status`
   - `core_completion_get_activities_completion_status`
   - `enrol_self_enrol_user`

### Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `MOODLE_BASE_URL` | Your Moodle instance URL | `https://qa.moodledemo.net` |
| `MOODLE_MOBILE_SERVICE_SHORTNAME` | Moodle mobile service shortname | `moodle_mobile_app` |
| `DB_DATABASE` | Database name | `moodle_api_db` |
| `DB_USERNAME` | Database username | `moodle_user` |
| `DB_PASSWORD` | Database password | `moodle_password` |

## API Testing with Postman

We provide a complete Postman testing suite to help you test all API endpoints.

### Quick Start for Testing

1. **Start the Laravel server**:
   ```bash
   php artisan serve
   ```

2. **Run the automated setup script**:
   ```bash
   ./postman/setup-postman-testing.sh
   ```

3. **Import the Postman collection**:
   - Open Postman
   - Import `postman/Moodle_API_Backend.postman_collection.json`
   - Set up environment variables as prompted

### Postman Files

| File | Description |
|------|-------------|
| `postman/Moodle_API_Backend.postman_collection.json` | Complete API collection |
| `postman/POSTMAN_DOCUMENTATION.md` | Comprehensive testing guide |
| `postman/setup-postman-testing.sh` | Automated setup script |
| `postman/README.md` | Postman folder overview |

For detailed testing instructions, see `postman/POSTMAN_DOCUMENTATION.md`.

## Project Structure

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
├── database/migrations/
│   └── create_user_moodle_tokens_table.php
├── postman/
│   ├── Moodle_API_Backend.postman_collection.json
│   ├── POSTMAN_DOCUMENTATION.md
│   ├── setup-postman-testing.sh
│   ├── POSTMAN_FILES.md
│   └── README.md
├── routes/
│   └── api.php
├── docker-compose.yml
├── Dockerfile
└── nginx/
    └── default.conf
```

## Troubleshooting

### Common Issues

1. **Moodle API Connection Failed**:
   - Verify `MOODLE_BASE_URL` is correct
   - Check if Moodle Web Services are enabled
   - Ensure the mobile service is properly configured

2. **Database Connection Issues**:
   - Verify Docker containers are running
   - Check database credentials in `.env`
   - Ensure database port 3307 is available

3. **Permission Issues**:
   - Make sure storage and bootstrap/cache directories are writable
   - Run `chmod -R 775 storage bootstrap/cache` if needed

### Logs

View application logs:
```bash
docker-compose logs app
```

View Nginx logs:
```bash
docker-compose logs nginx
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
