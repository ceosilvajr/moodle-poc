# Bruno Collection Files Overview

This directory contains all Bruno-related files for testing the Moodle API Backend.

## 📁 Files

| File | Purpose | Description |
|------|---------|-------------|
| `Moodle_API_Backend.bru` | Bruno Collection | Complete API collection with all endpoints |
| `environment.bru` | Environment Variables | Environment variables for testing |
| `README.md` | Documentation | Comprehensive testing guide and troubleshooting |
| `setup-bruno-testing.sh` | Setup Script | Automated setup script for Bruno testing |
| `BRUNO_FILES.md` | Overview | This file - overview of Bruno files |

## 🚀 Quick Start

### Option 1: Automated Setup
```bash
./api-documentation/bruno/setup-bruno-testing.sh
```

### Option 2: Manual Setup
1. Import `api-documentation/bruno/Moodle_API_Backend.bru` into Bruno
2. Import `api-documentation/bruno/environment.bru` for environment variables
3. Follow instructions in `api-documentation/bruno/README.md`

## 📋 Collection Contents

The Bruno collection includes these endpoints:

### Authentication
- `POST /api/v1/moodle/auth/link` - Link Moodle account
- `POST /api/v1/moodle/auth/unlink` - Unlink Moodle account

### User Management
- `GET /api/user` - Get user info

### Course Management
- `GET /api/v1/moodle/courses/enrolled` - Get enrolled courses
- `GET /api/v1/moodle/courses/available` - Get available courses
- `POST /api/v1/moodle/courses/{courseId}/enroll` - Enroll in course

### Certificates
- `GET /api/v1/moodle/certificates` - Get certificates

### Testing
- `GET /test` - Test API connection

## 🔧 Environment Variables

The environment file includes:
- `base_url`: Laravel server URL
- `api_token`: Bearer token for authentication
- `moodle_username`: Moodle username for testing
- `moodle_password`: Moodle password for testing
- `course_id`: Course ID for enrollment testing

## 📚 Documentation

- **Main README**: See `../../README.md` for project overview
- **Bruno README**: See `README.md` for detailed testing instructions
- **Setup Script**: See `setup-bruno-testing.sh` for automated setup

---

**Ready to test! 🚀**

Import the collection into Bruno and start testing your Moodle API Backend. 