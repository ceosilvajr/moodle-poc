# Database Access Guide - Moodle API Backend

This guide shows you how to access the database and manage users in your Laravel project.

## 📊 **Current Database Setup**

- **Database Type**: SQLite
- **Database File**: `database/database.sqlite`
- **Current Users**: 2 users in the system

## 🔍 **Method 1: Laravel Tinker (Recommended)**

### Quick Commands

#### View All Users
```bash
php artisan tinker --execute="use App\Models\User; User::all(['id', 'name', 'email'])->each(function(\$user) { echo 'ID: ' . \$user->id . ' | Name: ' . \$user->name . ' | Email: ' . \$user->email . PHP_EOL; });"
```

#### Get User Count
```bash
php artisan tinker --execute="use App\Models\User; echo 'Total users: ' . User::count();"
```

#### Find Specific User
```bash
php artisan tinker --execute="use App\Models\User; \$user = User::find(1); echo 'User: ' . \$user->name . ' (' . \$user->email . ')';"
```

### Interactive Tinker Session
```bash
php artisan tinker
```

Then use these commands in the interactive session:

```php
use App\Models\User;

// Get all users
User::all();

// Get specific user
User::find(1);

// Get user count
User::count();

// Search users by email
User::where('email', 'like', '%@example.com')->get();

// Get users with specific fields
User::select('id', 'name', 'email')->get();

// Get latest users
User::latest()->take(5)->get();
```

## 🗄️ **Method 2: Direct SQLite Access**

### Access SQLite Database
```bash
sqlite3 database/database.sqlite
```

### Useful SQLite Commands

#### List All Tables
```sql
.tables
```

#### Show Table Schema
```sql
.schema users
```

#### View All Users
```sql
SELECT id, name, email, created_at FROM users;
```

#### View User with Moodle Tokens
```sql
SELECT u.id, u.name, u.email, umt.moodle_token 
FROM users u 
LEFT JOIN user_moodle_tokens umt ON u.id = umt.user_id;
```

#### Count Users
```sql
SELECT COUNT(*) as user_count FROM users;
```

#### Search Users
```sql
SELECT * FROM users WHERE email LIKE '%@example.com';
```

## 👤 **User Management Commands**

### Create New User

#### Via Tinker
```bash
php artisan tinker --execute="use App\Models\User; User::create(['name' => 'New User', 'email' => 'newuser@example.com', 'password' => bcrypt('password')]); echo 'User created!';"
```

#### Via SQLite
```sql
INSERT INTO users (name, email, password, created_at, updated_at) 
VALUES ('New User', 'newuser@example.com', 'hashed_password', datetime('now'), datetime('now'));
```

### Update User

#### Via Tinker
```bash
php artisan tinker --execute="use App\Models\User; \$user = User::find(1); \$user->update(['name' => 'Updated Name']); echo 'User updated!';"
```

#### Via SQLite
```sql
UPDATE users SET name = 'Updated Name', updated_at = datetime('now') WHERE id = 1;
```

### Delete User

#### Via Tinker
```bash
php artisan tinker --execute="use App\Models\User; User::find(1)->delete(); echo 'User deleted!';"
```

#### Via SQLite
```sql
DELETE FROM users WHERE id = 1;
```

## 🔑 **API Token Management**

### Generate New Token for User
```bash
php artisan tinker --execute="use App\Models\User; \$user = User::find(1); \$token = \$user->createToken('api-token')->plainTextToken; echo 'Token: ' . \$token;"
```

### List All Tokens
```bash
php artisan tinker --execute="use App\Models\User; User::with('tokens')->get()->each(function(\$user) { echo 'User: ' . \$user->name . PHP_EOL; \$user->tokens->each(function(\$token) { echo '  - Token: ' . \$token->name . ' (created: ' . \$token->created_at . ')' . PHP_EOL; }); });"
```

### Delete User Tokens
```bash
php artisan tinker --execute="use App\Models\User; \$user = User::find(1); \$user->tokens()->delete(); echo 'All tokens deleted for user!';"
```

## 📋 **Useful Database Queries**

### Check User Moodle Token Status
```bash
php artisan tinker --execute="use App\Models\User; use App\Models\UserMoodleToken; User::with('moodleToken')->get()->each(function(\$user) { echo 'User: ' . \$user->name . ' | Moodle Linked: ' . (\$user->moodleToken ? 'Yes' : 'No') . PHP_EOL; });"
```

### Get Users with Moodle Tokens
```bash
php artisan tinker --execute="use App\Models\User; use App\Models\UserMoodleToken; User::whereHas('moodleToken')->get()->each(function(\$user) { echo 'User with Moodle: ' . \$user->name . ' (' . \$user->email . ')' . PHP_EOL; });"
```

### Get Users without Moodle Tokens
```bash
php artisan tinker --execute="use App\Models\User; use App\Models\UserMoodleToken; User::whereDoesntHave('moodleToken')->get()->each(function(\$user) { echo 'User without Moodle: ' . \$user->name . ' (' . \$user->email . ')' . PHP_EOL; });"
```

## 🛠️ **Database Maintenance**

### Reset Database
```bash
php artisan migrate:fresh
```

### Seed Database with Test Data
```bash
php artisan db:seed
```

### Backup Database
```bash
cp database/database.sqlite database/database_backup_$(date +%Y%m%d_%H%M%S).sqlite
```

### Restore Database
```bash
cp database/database_backup_YYYYMMDD_HHMMSS.sqlite database/database.sqlite
```

## 📊 **Current Database Tables**

| Table | Description |
|-------|-------------|
| `users` | User accounts |
| `user_moodle_tokens` | Moodle authentication tokens |
| `personal_access_tokens` | API tokens |
| `migrations` | Database migration history |
| `sessions` | User sessions |
| `cache` | Application cache |
| `jobs` | Queue jobs |
| `failed_jobs` | Failed queue jobs |

## 🔍 **Quick Reference Commands**

### View Current Users
```bash
php artisan tinker --execute="use App\Models\User; echo 'Current users:' . PHP_EOL; User::all(['id', 'name', 'email'])->each(function(\$user) { echo \$user->id . ': ' . \$user->name . ' (' . \$user->email . ')' . PHP_EOL; });"
```

### Generate API Token
```bash
php artisan tinker --execute="use App\Models\User; \$user = User::first(); \$token = \$user->createToken('postman-test')->plainTextToken; echo 'API Token: ' . \$token;"
```

### Check Database Status
```bash
php artisan tinker --execute="echo 'Users: ' . App\Models\User::count() . PHP_EOL; echo 'Moodle Tokens: ' . App\Models\UserMoodleToken::count() . PHP_EOL; echo 'API Tokens: ' . App\Models\PersonalAccessToken::count() . PHP_EOL;"
```

## 🚨 **Important Notes**

1. **Password Hashing**: Always use `bcrypt()` when creating users via SQLite
2. **Timestamps**: Laravel automatically manages `created_at` and `updated_at`
3. **Relationships**: Use Eloquent relationships for complex queries
4. **Backup**: Always backup before making changes
5. **Environment**: Make sure you're working with the correct database

## 📞 **Troubleshooting**

### Database Connection Issues
```bash
# Check database configuration
php artisan config:show database

# Test database connection
php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Database connected!'; } catch(Exception \$e) { echo 'Database connection failed: ' . \$e->getMessage(); }"
```

### Permission Issues
```bash
# Fix database file permissions
chmod 664 database/database.sqlite
chmod 775 database/
```

---

**Happy Database Management! 🎉** 