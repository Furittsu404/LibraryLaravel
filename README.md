<p align="center">
  <img src="public/LISO_LogoColored.png" alt="CLSU-LISO Logo" width="300">
</p>

<h1 align="center">CLSU-LISO AMS</h1>

<p align="center">
  <strong>CLSU Library and Information Services Office Attendance Management System</strong>
</p>

<p align="center">
A modern Laravel-based attendance management system for the Central Luzon State University Library and Information Services Office. This system is a complete upgrade from the legacy plain PHP system, migrated to Laravel while preserving all historical data.
</p>

---

## 📋 Table of Contents

- [About](#about)
- [Features](#features)
- [Technology Stack](#technology-stack)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [Migration Notes](#migration-notes)
- [Contributing](#contributing)
- [License](#license)

---

## 🎯 About

The CLSU-LISO AMS is an attendance tracking and user management system designed specifically for the CLSU Library and Information Services Office. It provides comprehensive tools for managing library visitors, students, faculty, and staff attendance records while offering powerful analytics and reporting capabilities.

This project represents a complete modernization effort, migrating from a legacy plain PHP codebase to a robust Laravel framework while ensuring complete data preservation from the previous system.

---

## ✨ Features

### 📊 Dashboard & Analytics

- **Real-time Statistics**: Live attendance tracking with user status monitoring
- **Interactive Charts**:
    - Course distribution pie chart
    - Gender demographics visualization
    - Timeline attendance graph
- **Dark Mode Support**: Full dark/light theme toggle
- **Responsive Design**: Mobile-friendly interface using Tailwind CSS

### 👥 Account Management

- **User CRUD Operations**: Create, read, update, and delete user accounts
- **Bulk Import**: CSV/Excel import functionality for mass user registration
- **User Types**: Support for students, visitors, faculty, and staff
- **Account Status Control**: Active/inactive account management
- **Expiration Date Management**:
    - Individual expiration date setting per user
    - Bulk expiration date update for all accounts
- **Barcode/ID Integration**: Quick user identification via barcode scanning

### 📦 Archive System

- **User Archiving**: Soft-delete functionality preserving user history
- **Archive Management**: View, edit, restore, or permanently delete archived users
- **Bulk Operations**: Mass archive deletion capabilities
- **Auto-Activation**: Automatic account activation when restoring from archive

### 📈 Reports & Export

- **Customizable Date Ranges**: Filter reports by specific time periods
- **Export Formats**:
    - PDF generation for printing
    - Excel/CSV export for data analysis
- **Comprehensive Data**: Includes login/logout times, user demographics, and attendance patterns

### 📜 Login History

- **Detailed Tracking**: Complete login/logout history with timestamps
- **User Status Monitoring**: Track inside/outside library status
- **Search & Filter**: Advanced filtering by date, user, or status

### 🔐 Authentication & Security

- **Admin Authentication**: Secure admin login system
- **Session Management**: Proper session handling with CSRF protection
- **Role-based Access**: Admin-only routes with middleware protection

---

## 🛠 Technology Stack

### Backend

- **Laravel 12.x**: Modern PHP framework
- **PHP 8.4**: Latest PHP features and performance
- **MySQL/MariaDB**: Relational database management

### Frontend

- **Livewire 4.x**: Full-stack reactive components
- **Alpine.js**: Lightweight JavaScript framework for interactivity
- **Tailwind CSS**: Utility-first CSS framework
- **Chart.js**: Interactive data visualizations

### Development Tools

- **Composer**: PHP dependency management
- **NPM**: JavaScript package management
- **Vite**: Modern frontend build tool

---

## 📥 Installation

### Prerequisites

- PHP >= 8.4
- Composer
- Node.js & NPM
- MySQL/MariaDB
- Web server (Apache/Nginx)

### Step 1: Clone the Repository

```bash
git clone https://github.com/Furittsu404/LibraryLaravel.git
cd LibraryLaravel
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies
npm install
```

### Step 3: Environment Configuration

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Configure Database

Edit your `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=library_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 5: Build Frontend Assets

```bash
# Development
npm run dev

# Production
npm run build
```

---

## 🗄 Database Setup

```bash
php artisan migrate
php artisan db:seed
```

### Creating an Admin Account

```bash
# Visit this route to create the first admin account
# http://your-domain/create-admin

# Or use Laravel tinker
php artisan tinker
>>> \App\Models\Admin::create([
    'username' => 'admin',
    'password' => bcrypt('your_password')
]);
```

---

## 🚀 Usage

### Starting the Development Server

```bash
# Start Laravel development server
php artisan serve

# In another terminal, start Vite for hot module replacement
npm run dev
```

Visit `http://localhost:8000` in your browser.

### Default Login

```
Username: admin
Password: (set during admin creation)
```

### Main Routes

- `/login` - Admin login page
- `/dashboard` - Main dashboard with analytics
- `/accounts` - User account management
- `/archive` - Archived users management
- `/reports` - Generate and export reports
- `/login-history` - View attendance history

---

## 📁 Project Structure

```
LibraryLaravel/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Livewire/
│   │           ├── AccountsPage/      # Account management
│   │           ├── ArchivePage/       # Archive functionality
│   │           ├── DashboardPage/     # Dashboard & analytics
│   │           ├── LoginHistoryPage/  # Login tracking
│   │           └── ReportsPage/       # Report generation
│   └── Models/
│       ├── User.php                   # Active users
│       ├── Archive.php                # Archived users
│       └── Admin.php                  # Admin accounts
├── database/
│   ├── library_main.sql              # Full database with data
│   └── library_schema_clean.sql      # Clean schema only
├── resources/
│   ├── views/
│   │   └── components/
│   │       ├── accountsPage/         # Account UI components
│   │       ├── archivePage/          # Archive UI components
│   │       └── dashboardPage/        # Dashboard UI components
│   ├── js/
│   │   └── dashboard.js              # Chart.js configurations
│   └── css/
│       └── app.css                   # Tailwind styles
└── routes/
    └── web.php                       # Application routes
```

---

## 🔄 Migration Notes

This system was migrated from a legacy plain PHP application to Laravel with the following considerations:

### Data Preservation

- ✅ All historical user data preserved
- ✅ Login/logout records maintained
- ✅ User barcode IDs retained
- ✅ Account statuses and user types preserved

### Architecture Changes

- **From**: Procedural PHP with manual routing
- **To**: Laravel MVC with Livewire components
- **Benefits**:
    - Better code organization
    - Built-in security features
    - Modern UI/UX with reactive components
    - Improved performance and scalability

### Database Changes

- Added `expiration_date` field for account expiration tracking
- Improved indexing for better query performance
- Normalized table structures
- Added proper foreign key relationships

---

## 🤝 Contributing

Contributions are welcome! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

This project is proprietary software developed for Central Luzon State University Library and Information Services Office.

---

## 📞 Support

For issues, questions, or contributions, please contact the CLSU-LISO IT team or open an issue in the repository.

---

**Built with ❤️ for CLSU Library and Information Services Office**
