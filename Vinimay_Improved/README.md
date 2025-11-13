# Vinimaya - Smart Expense Tracker

A comprehensive expense tracking application designed for Indian households. Track your daily expenses, view analytics, and generate reports with ease.

## ✨ Features

- 📊 **Dashboard** with visual charts and statistics
- 💰 **Expense Management** - Add, edit, delete expenses
- 📈 **Reports & Analytics** - Category-wise breakdown and trends
- 📄 **Export Options** - PDF and Excel exports
- 🏷️ **Category Management** - 18 predefined categories including Rent
- 👥 **Multi-user Support** - Family size tracking
- 🔐 **Secure Authentication** - User registration and login

## 🚀 Quick Start

### Requirements
- PHP 7.4+ (PHP 8.0+ recommended)
- MySQL 5.7+ or MariaDB 10.3+
- Apache/Nginx with mod_rewrite enabled
- PDO MySQL extension

### Installation

1. **Clone or download the project**
   ```bash
   git clone <repository-url> Vinimay_Improved
   cd Vinimay_Improved
   ```

2. **Setup Database**
   - Create database: `vinimaya`
   - Import `sql.txt` file via phpMyAdmin or MySQL command line
   ```bash
   mysql -u root -p vinimaya < sql.txt
   ```

3. **Configure Database**
   - Edit `app/config/database.php`
   - Update database credentials (host, username, password)

4. **Setup Web Server**
   - Place project in web server directory
   - Ensure `.htaccess` file exists in `public` folder
   - Enable `mod_rewrite` (Apache)

5. **Access Application**
   - Open browser: `http://localhost/Vinimay_Improved/public/`
   - Register a new account
   - Start tracking expenses!

## 📖 Detailed Setup Guide

For detailed deployment instructions, see [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)

For quick setup checklist, see [SETUP_CHECKLIST.md](SETUP_CHECKLIST.md)

## 🗂️ Project Structure

```
Vinimay_Improved/
├── app/
│   ├── config/          # Configuration files
│   ├── controllers/     # MVC Controllers
│   ├── models/          # Data models
│   ├── utils/           # Utility classes
│   └── views/           # View templates
├── public/              # Public web directory
│   ├── css/            # Stylesheets
│   ├── js/             # JavaScript files
│   └── index.php       # Entry point
├── sql.txt             # Database schema
└── README.md           # This file
```

## 🎯 Usage

### Adding Expenses
1. Click "Add Expense" in navigation
2. Fill in amount, category, date, and description
3. Click "Save Expense"

### Viewing Expenses
1. Click "View Expenses" in navigation
2. See all your expenses in a table
3. Edit or delete expenses as needed

### Generating Reports
1. Click "Reports" in navigation
2. Select month and year
3. View category breakdown and charts
4. Export as PDF or Excel

### Categories
The application includes 18 predefined categories:
- Rent, Groceries & Ration, Vegetables & Fruits
- Milk & Dairy, Transport & Fuel, Utility Bills
- Mobile & Internet, Eating Out, Medical
- Education, Entertainment, Shopping
- Personal Care, Home Maintenance, Travel
- Gifts & Donations, Investments, Insurance, Others

## 🔧 Configuration

### Database Configuration
Edit `app/config/database.php`:
```php
private $host = "localhost";
private $db_name = "vinimaya";
private $username = "root";
private $password = "";
```

### Base URL
The application automatically detects the base URL. If you have issues with CSS/JS not loading:
- Check `BASE_URL` in `public/index.php`
- Verify file paths in `app/views/layouts/header.php` and `footer.php`

## 🐛 Troubleshooting

### Database Connection Error
- Verify MySQL service is running
- Check database credentials in `app/config/database.php`
- Ensure database `vinimaya` exists

### 404 Errors
- Verify `.htaccess` file exists in `public` folder
- Check `mod_rewrite` is enabled (Apache)
- Verify web server configuration

### CSS/JS Not Loading
- Clear browser cache
- Check browser console for errors
- Verify `BASE_URL` is set correctly

## 🔒 Security Notes

- Use strong database passwords in production
- Don't use `root` database user in production
- Enable HTTPS in production
- Set proper file permissions
- Keep PHP and MySQL updated

## 📝 License

This project is open source and available for personal and commercial use.

## 🤝 Support

For issues or questions:
1. Check [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md) for setup issues
2. Review error logs
3. Check browser console for JavaScript errors

## 🎉 Features Overview

- **Smart Dashboard**: Visual representation of expenses with charts
- **Category Tracking**: Track expenses across 18 categories
- **Monthly Reports**: Generate detailed monthly reports
- **Export Functionality**: Export reports as PDF or Excel
- **User Management**: Secure registration and login
- **Responsive Design**: Works on desktop and mobile devices

---

**Made with ❤️ for Indian households**

