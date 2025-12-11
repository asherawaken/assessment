# Mini Accounting Module (GL Journal System)

A professional accounting module with separate Laravel backend and Vue.js frontend, implementing a General Ledger Journal System with double-entry accounting.

## Features

- ✅ **Journal Entries**: Create and manage journal entries with multiple debit and credit lines
- ✅ **Double-Entry Validation**: Ensures total debits equal total credits before saving
- ✅ **Trial Balance Report**: View trial balance grouped by account with debit/credit totals
- ✅ **Account Management**: Pre-seeded chart of accounts (Assets, Liabilities, Equity, Revenue, Expenses)
- ✅ **RESTful API**: Clean Laravel API with proper validation and error handling
- ✅ **Modern Frontend**: Vue.js 3 with Router for a responsive user interface

## Project Structure

```
assessment/
├── backend/          # Laravel 12 API
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   └── Models/
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   └── routes/
│       └── api.php
└── frontend/         # Vue.js 3 Frontend
    ├── src/
    │   ├── components/
    │   ├── views/
    │   ├── services/
    │   └── router/
    └── package.json
```

## Prerequisites

- PHP >= 8.2
- Composer
- Node.js >= 20.19.0 or >= 22.12.0
- MySQL Server (already running as per your setup)
- npm or yarn

## Backend Setup (Laravel)

1. **Navigate to backend directory:**
   ```bash
   cd backend
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Configure environment:**
   - Copy `.env.example` to `.env` (if not exists)
   - Update database credentials in `.env`:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=your_database_name
     DB_USERNAME=your_username
     DB_PASSWORD=your_password
     ```

4. **Run migrations and seeders:**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```
   This will create the necessary tables and seed sample accounts.

5. **Start Laravel development server:**
   ```bash
   php artisan serve
   ```
   The API will be available at `http://localhost:8000`

## Frontend Setup (Vue.js)

1. **Navigate to frontend directory:**
   ```bash
   cd frontend
   ```

2. **Install dependencies:**
   ```bash
   npm install
   ```

3. **Start development server:**
   ```bash
   npm run dev
   ```
   The frontend will be available at `http://localhost:5173` (or the port Vite assigns)

## API Endpoints

### Accounts
- `GET /api/accounts` - List all accounts
- `POST /api/accounts` - Create a new account
- `GET /api/accounts/{id}` - Get account details
- `PUT /api/accounts/{id}` - Update account
- `DELETE /api/accounts/{id}` - Delete account

### Journal Entries
- `GET /api/journal-entries` - List all journal entries
- `POST /api/journal-entries` - Create a new journal entry
- `GET /api/journal-entries/{id}` - Get journal entry details
- `PUT /api/journal-entries/{id}` - Update journal entry
- `DELETE /api/journal-entries/{id}` - Delete journal entry

### Trial Balance
- `GET /api/trial-balance` - Get trial balance report

## Database Schema

### accounts
- `id` - Primary key
- `code` - Unique account code
- `name` - Account name
- `type` - Account type (asset, liability, equity, revenue, expense)
- `description` - Optional description
- `timestamps`

### journal_entries
- `id` - Primary key
- `entry_date` - Date of the entry
- `description` - Entry description
- `reference_number` - Optional reference number
- `timestamps`

### journal_entry_lines
- `id` - Primary key
- `journal_entry_id` - Foreign key to journal_entries
- `account_id` - Foreign key to accounts
- `type` - 'debit' or 'credit'
- `amount` - Decimal amount
- `description` - Optional line description
- `timestamps`

## Usage

1. **Access the application:**
   - Open your browser and navigate to the frontend URL (typically `http://localhost:5173`)

2. **Create a Journal Entry:**
   - Navigate to "Journal Entries" from the menu
   - Click "Add New Entry"
   - Fill in the entry date, description, and reference number (optional)
   - Add at least 2 lines (one debit, one credit)
   - Ensure total debits equal total credits
   - Click "Create Entry"

3. **View Trial Balance:**
   - Navigate to "Trial Balance" from the menu
   - View the report showing all accounts with their debit/credit totals and balances

## Validation Rules

- Journal entries must have at least 2 lines
- Total debits must equal total credits (within 0.01 tolerance)
- Each line must have a valid account, type (debit/credit), and amount > 0
- Entry date and description are required

## Sample Accounts

The seeder creates the following sample accounts:

**Assets:** Cash, Accounts Receivable, Inventory, Prepaid Expenses, Equipment
**Liabilities:** Accounts Payable, Accrued Expenses, Short-term Loans
**Equity:** Capital, Retained Earnings
**Revenue:** Sales Revenue, Service Revenue, Other Income
**Expenses:** Cost of Goods Sold, Salaries Expense, Rent Expense, Utilities Expense, Marketing Expense

## Technologies Used

### Backend
- Laravel 12
- MySQL
- PHP 8.2+

### Frontend
- Vue.js 3
- Vue Router
- Vite
- Fetch API

## Notes

- CORS is configured to allow requests from the frontend
- The backend uses Laravel's built-in validation
- All amounts are stored as decimals with 2 decimal places
- The trial balance calculates balances as: Total Debits - Total Credits

## Troubleshooting

1. **CORS errors:** Ensure the backend is running on port 8000 and frontend can access it
2. **Database connection:** Verify MySQL credentials in `.env`
3. **Port conflicts:** If port 8000 is in use, Laravel will automatically use the next available port
4. **Frontend not connecting:** Check that the API_BASE_URL in `frontend/src/services/api.js` matches your backend URL
