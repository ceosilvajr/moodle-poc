# Postman Documentation - Moodle API Backend

This document provides comprehensive instructions for testing the Moodle API Backend using the provided Postman collection.

## 📋 Table of Contents

1. [Setup Instructions](#setup-instructions)
2. [Importing the Collection](#importing-the-collection)
3. [Environment Variables](#environment-variables)
4. [Testing Scenarios](#testing-scenarios)
5. [API Endpoints Reference](#api-endpoints-reference)
6. [Example Responses](#example-responses)
7. [Troubleshooting](#troubleshooting)

## 🚀 Setup Instructions

### Prerequisites

1. **Postman Desktop App**: Download and install from [postman.com](https://www.postman.com/downloads/)
2. **Laravel Server Running**: Ensure your Laravel server is running on `http://localhost:8000`
3. **Test User Created**: The API should have a test user with the provided token

### Quick Start

1. Start your Laravel server:
   ```bash
   cd moodle-api-backend
   php artisan serve
   ```

2. Import the Postman collection (see instructions below)
3. Set up environment variables
4. Start testing!

## 📥 Importing the Collection

### Method 1: Import from File

1. Open Postman
2. Click **Import** button
3. Select **Upload Files**
4. Choose `postman/Moodle_API_Backend.postman_collection.json`
5. Click **Import**

### Method 2: Import from Raw Text

1. Open Postman
2. Click **Import** button
3. Select **Raw text**
4. Copy and paste the content of `postman/Moodle_API_Backend.postman_collection.json`
5. Click **Continue** and then **Import**

## 🔧 Environment Variables

The collection uses the following environment variables:

| Variable | Default Value | Description |
|----------|---------------|-------------|
| `base_url` | `http://localhost:8000` | Base URL of your Laravel API |
| `api_token` | `1\|t58oT6exgZFTlBaQVS1NliPUaaAVVg0cfFybJbKN26d10f53` | Bearer token for authentication |
| `moodle_username` | `your_moodle_username` | Moodle username for testing |
| `moodle_password` | `your_moodle_password` | Moodle password for testing |
| `course_id` | `1` | Course ID for enrollment testing |

### Setting Environment Variables

1. In Postman, click on the **Environment** dropdown (top right)
2. Select **New Environment**
3. Add the variables above with appropriate values
4. Save the environment

## 🧪 Testing Scenarios

### Scenario 1: Basic API Testing

1. **Test API Connection**
   - Endpoint: `GET {{base_url}}/api/test`
   - Expected: `{"message":"API routes are working!"}`
   - No authentication required

2. **Get Authenticated User**
   - Endpoint: `GET {{base_url}}/api/user`
   - Expected: User information in JSON format
   - Requires valid Bearer token

### Scenario 2: Moodle Account Management

1. **Unlink Moodle Account** (Safe to test)
   - Endpoint: `POST {{base_url}}/api/v1/moodle/auth/unlink`
   - Expected: `{"status":"success","message":"Moodle account unlinked successfully."}`
   - This will work even without a linked Moodle account

2. **Link Moodle Account** (Requires real Moodle credentials)
   - Endpoint: `POST {{base_url}}/api/v1/moodle/auth/link`
   - Body: `{"moodle_username":"your_username","moodle_password":"your_password"}`
   - Expected: `{"status":"success","message":"Moodle account linked successfully."}`

### Scenario 3: Course Management

1. **Get Enrolled Courses**
   - Endpoint: `GET {{base_url}}/api/v1/moodle/courses/enrolled`
   - Expected: Array of enrolled courses or error if no Moodle account linked

2. **Get Available Courses**
   - Endpoint: `GET {{base_url}}/api/v1/moodle/courses/available`
   - Expected: Array of available courses or error if no Moodle account linked

3. **Enroll in Course**
   - Endpoint: `POST {{base_url}}/api/v1/moodle/courses/{{course_id}}/enroll`
   - Expected: Success message or error if enrollment fails

### Scenario 4: Certificate Retrieval

1. **Get User Certificates**
   - Endpoint: `GET {{base_url}}/api/v1/moodle/certificates`
   - Expected: Array of certificates or empty array if none available

## 📚 API Endpoints Reference

### Authentication Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/user` | Get authenticated user info | ✅ |
| GET | `/api/test` | Test API connection | ❌ |

### Moodle Account Management

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/v1/moodle/auth/link` | Link Moodle account | ✅ |
| POST | `/api/v1/moodle/auth/unlink` | Unlink Moodle account | ✅ |

### Course Management

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/v1/moodle/courses/enrolled` | Get enrolled courses | ✅ |
| GET | `/api/v1/moodle/courses/available` | Get available courses | ✅ |
| POST | `/api/v1/moodle/courses/{id}/enroll` | Enroll in course | ✅ |

### Certificates

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/v1/moodle/certificates` | Get user certificates | ✅ |

## 📄 Example Responses

### Successful Authentication Response

```json
{
    "id": 1,
    "name": "Test User",
    "email": "test@example.com",
    "email_verified_at": null,
    "created_at": "2025-07-28T18:30:00.000000Z",
    "updated_at": "2025-07-28T18:30:00.000000Z"
}
```

### Successful Moodle Unlink Response

```json
{
    "status": "success",
    "message": "Moodle account unlinked successfully."
}
```

### Successful Moodle Link Response

```json
{
    "status": "success",
    "message": "Moodle account linked successfully."
}
```

### Error Response (No Moodle Account Linked)

```json
{
    "error": "Moodle account not linked for this user."
}
```

### Course Data Response

```json
[
    {
        "id": 1,
        "shortname": "MATH101",
        "fullname": "Introduction to Mathematics",
        "displayname": "Introduction to Mathematics",
        "summary": "Basic mathematics course",
        "status": "in-progress"
    }
]
```

### Certificate Data Response

```json
[
    {
        "id": 1,
        "name": "Course Completion Certificate",
        "course_id": 1,
        "course_name": "Introduction to Mathematics",
        "status": "completed",
        "issue_date": "2025-07-28",
        "download_url": "https://moodle.example.com/certificate.pdf?token=abc123"
    }
]
```

## 🔍 Testing Checklist

### ✅ Pre-Testing Checklist

- [ ] Laravel server is running on `http://localhost:8000`
- [ ] Postman collection is imported
- [ ] Environment variables are set
- [ ] Test user exists in database
- [ ] API token is valid

### ✅ Basic Functionality Tests

- [ ] Test API connection (`GET /api/test`)
- [ ] Test authentication (`GET /api/user`)
- [ ] Test Moodle unlink (`POST /api/v1/moodle/auth/unlink`)

### ✅ Moodle Integration Tests (Requires Real Moodle)

- [ ] Test Moodle account linking
- [ ] Test getting enrolled courses
- [ ] Test getting available courses
- [ ] Test course enrollment
- [ ] Test certificate retrieval

## 🛠️ Troubleshooting

### Common Issues

#### 1. "Could not open input file: artisan"

**Problem**: You're not in the correct directory.

**Solution**: 
```bash
cd moodle-api-backend
php artisan serve
```

#### 2. "401 Unauthorized" Error

**Problem**: Invalid or missing API token.

**Solution**:
- Verify the `api_token` variable is set correctly
- Generate a new token if needed:
  ```bash
  php artisan tinker --execute="use App\Models\User; \$user = User::find(1); \$token = \$user->createToken('test-token')->plainTextToken; echo 'API Token: ' . \$token;"
  ```

#### 3. "404 Not Found" Error

**Problem**: API routes not working.

**Solution**:
- Ensure the server is running
- Check if routes are properly loaded
- Verify the base URL is correct

#### 4. "Moodle account not linked" Error

**Problem**: No Moodle account is linked to the user.

**Solution**:
- First link a Moodle account using the link endpoint
- Or test with the unlink endpoint which works without a linked account

#### 5. "Failed to obtain Moodle token" Error

**Problem**: Moodle credentials are incorrect or Moodle is not accessible.

**Solution**:
- Verify Moodle credentials
- Check if Moodle server is accessible
- Ensure Moodle Web Services are enabled

### Debugging Tips

1. **Check Server Logs**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Test with curl**:
   ```bash
   curl -H "Authorization: Bearer YOUR_TOKEN" http://localhost:8000/api/user
   ```

3. **Verify Database**:
   ```bash
   php artisan tinker --execute="use App\Models\User; echo User::count();"
   ```

## 📞 Support

If you encounter issues:

1. Check the troubleshooting section above
2. Verify your Laravel server is running correctly
3. Check the Laravel logs for detailed error messages
4. Ensure all environment variables are set correctly

## 🔄 Updating the Collection

To update the collection with new endpoints or changes:

1. Export the updated collection from Postman
2. Replace the existing `postman/Moodle_API_Backend.postman_collection.json` file
3. Re-import the collection

---

**Happy Testing! 🎉**

This documentation should help you effectively test all aspects of the Moodle API Backend using Postman. 