# Backend Setup Instructions

## Quick Start Guide

### Step 1: Configure Database (MySQL)

1. Open the `.env` file in the backend directory
2. Update the database configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_mysql_username
DB_PASSWORD=your_mysql_password
```

**Important:** Replace:
- `your_database_name` with your MySQL database name (create it first if it doesn't exist)
- `your_mysql_username` with your MySQL username (usually `root`)
- `your_mysql_password` with your MySQL password

### Step 2: Create Database (if not exists)

If you haven't created the database yet, you can do it via MySQL command line:

```sql
CREATE DATABASE your_database_name;
```

Or use MySQL Workbench, phpMyAdmin, or any MySQL client.

### Step 3: Run Migrations

This will create all the necessary tables:

```bash
php artisan migrate
```

### Step 4: Seed Sample Accounts

This will populate the database with sample chart of accounts:

```bash
php artisan db:seed
```

Or run both migration and seeding together:

```bash
php artisan migrate --seed
```

### Step 5: Start the Server

```bash
php artisan serve
```

The backend API will be available at: **http://localhost:8000**

You should see output like:
```
INFO  Server running on [http://127.0.0.1:8000].
```

## Verify Setup

Once the server is running, you can test the API:

1. **Test Accounts Endpoint:**
   - Open browser: http://localhost:8000/api/accounts
   - Should return JSON with sample accounts

2. **Test Trial Balance:**
   - Open browser: http://localhost:8000/api/trial-balance
   - Should return JSON with trial balance data

## Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check database credentials in `.env`
- Ensure database exists
- Test connection: `mysql -u your_username -p your_database_name`

### Port Already in Use
If port 8000 is busy, use a different port:
```bash
php artisan serve --port=8001
```
Then update `frontend/src/services/api.js` to use the new port.

### Migration Errors
- Make sure database exists
- Check database user has proper permissions
- Try: `php artisan migrate:fresh --seed` (WARNING: This will delete all data)

### CORS Issues
- Ensure backend is running on port 8000
- Check `bootstrap/app.php` has CORS middleware configured
- Frontend should connect to `http://localhost:8000/api`

## Common Commands

```bash
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Reset and re-run all migrations
php artisan migrate:fresh

# Seed database
php artisan db:seed

# Clear cache
php artisan cache:clear
php artisan config:clear

# Check routes
php artisan route:list
```

