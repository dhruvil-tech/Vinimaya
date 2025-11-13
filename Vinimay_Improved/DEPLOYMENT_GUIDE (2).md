# Vinimaya Expense Tracker - Deployment Guide

This guide will help you deploy the Vinimaya Expense Tracker application on another device or server.

## 📋 Requirements

Before deploying, ensure the target device has:

- **PHP 7.4 or higher** (PHP 8.0+ recommended)
- **MySQL 5.7+ or MariaDB 10.3+**
- **Web Server**: Apache, Nginx, or IIS (with mod_rewrite enabled for Apache)
- **PHP Extensions**:
  - PDO
  - PDO_MySQL
  - mbstring
  - json

## 📦 Step 1: Transfer Files

### Option A: Using USB/External Drive
1. Copy the entire `Vinimay_Improved` folder to the target device
2. Place it in the web server's document root:
   - **Windows (XAMPP)**: `C:\xampp\htdocs\`
   - **Windows (WAMP)**: `C:\wamp64\www\`
   - **Linux (Apache)**: `/var/www/html/`
   - **macOS (MAMP)**: `/Applications/MAMP/htdocs/`

### Option B: Using Git
```bash
git clone <repository-url> Vinimay_Improved
cd Vinimay_Improved
```

### Option C: Using ZIP File
1. Zip the project folder
2. Transfer the ZIP file to the target device
3. Extract it to the web server directory

## 🗄️ Step 2: Database Setup

### 2.1 Create Database
1. Open phpMyAdmin or MySQL command line
2. Create a new database:
```sql
CREATE DATABASE vinimaya CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2.2 Import Database Schema
1. Open the `sql.txt` file
2. Copy all SQL commands
3. Execute them in phpMyAdmin SQL tab or MySQL command line
4. Or use command line:
```bash
mysql -u root -p vinimaya < sql.txt
```

### 2.3 Verify Categories
Ensure all categories are inserted (should be 18 categories including "Rent").

## ⚙️ Step 3: Configure Database Connection

Edit `app/config/database.php` and update the database credentials:

```php
<?php
class Database {
    private $host = "localhost";        // Change if database is on remote server
    private $db_name = "vinimaya";      // Database name
    private $username = "root";         // MySQL username
    private $password = "";             // MySQL password (set if required)
    // ... rest of the code
}
```

**For remote database:**
- Change `$host` to your database server IP/domain
- Update `$username` and `$password` with your credentials

## 🌐 Step 4: Configure Web Server

### Apache Configuration

#### Option A: Using .htaccess (Recommended)
1. Create `.htaccess` file in the `public` folder (if it doesn't exist):
```apache
RewriteEngine On

# Redirect all requests to index.php unless the file exists
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

2. Ensure `mod_rewrite` is enabled:
```bash
# Linux
sudo a2enmod rewrite
sudo systemctl restart apache2

# Windows (XAMPP)
# Check httpd.conf: LoadModule rewrite_module modules/mod_rewrite.so
```

#### Option B: Virtual Host (Production)
Create a virtual host configuration:
```apache
<VirtualHost *:80>
    ServerName vinimaya.local
    DocumentRoot "C:/xampp/htdocs/Vinimay_Improved/public"
    
    <Directory "C:/xampp/htdocs/Vinimay_Improved/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Nginx Configuration
```nginx
server {
    listen 80;
    server_name vinimaya.local;
    root /var/www/html/Vinimay_Improved/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

## 🔧 Step 5: Set Permissions (Linux/macOS)

```bash
# Set proper permissions
sudo chown -R www-data:www-data /var/www/html/Vinimay_Improved
sudo chmod -R 755 /var/www/html/Vinimay_Improved
sudo chmod -R 775 /var/www/html/Vinimay_Improved/app/storage  # If you create storage folder
```

## 🧪 Step 6: Test the Installation

1. **Start your web server** (Apache/Nginx)
2. **Start MySQL service**
3. **Open browser** and navigate to:
   - Local: `http://localhost/Vinimay_Improved/public/`
   - Or: `http://localhost/Vinimay_Improved/public/index.php`
   - Virtual host: `http://vinimaya.local/`

4. **Expected Result**: You should see the login/register page

5. **Create a test account**:
   - Click "Register"
   - Fill in the registration form
   - Log in with your credentials

## 🔍 Troubleshooting

### Issue: "Database connection failed"
**Solution:**
- Check database credentials in `app/config/database.php`
- Verify MySQL service is running
- Ensure database `vinimaya` exists
- Check user has proper permissions

### Issue: "404 Not Found" or "Page not loading"
**Solution:**
- Verify `.htaccess` file exists in `public` folder
- Check `mod_rewrite` is enabled (Apache)
- Verify web server document root points to `public` folder
- Check file permissions

### Issue: "CSS/JS files not loading"
**Solution:**
- Clear browser cache
- Check browser console for 404 errors
- Verify `BASE_URL` is correctly set in `public/index.php`
- Check file paths in `app/views/layouts/header.php` and `footer.php`

### Issue: "Session errors"
**Solution:**
- Check PHP `session.save_path` is writable
- Verify `session_start()` is called before any output
- Check PHP `session` extension is enabled

### Issue: "Class not found" errors
**Solution:**
- Verify autoloader in `public/index.php` is working
- Check file paths match class names
- Ensure PHP version is 7.4+

## 📝 Step 7: Production Checklist

Before deploying to production:

- [ ] Change database credentials
- [ ] Set strong database password
- [ ] Enable HTTPS/SSL
- [ ] Update `error_reporting` to hide errors in production
- [ ] Set proper file permissions
- [ ] Configure backup strategy
- [ ] Set up error logging
- [ ] Configure PHP `upload_max_filesize` if needed
- [ ] Test all features (login, register, add expense, reports, exports)

## 🔒 Security Recommendations

1. **Database Security**:
   - Use strong passwords
   - Don't use `root` user in production
   - Create dedicated database user with limited privileges

2. **File Permissions**:
   - Restrict access to config files
   - Set proper directory permissions

3. **PHP Security**:
   - Hide PHP version in headers
   - Disable dangerous PHP functions
   - Use prepared statements (already implemented)

4. **HTTPS**:
   - Enable SSL certificate
   - Force HTTPS redirect

## 📱 Accessing from Other Devices on Network

### Local Network Access:
1. Find your server's IP address:
   - Windows: `ipconfig`
   - Linux/Mac: `ifconfig` or `ip addr`

2. Access from other devices:
   - `http://[SERVER_IP]/Vinimay_Improved/public/`
   - Example: `http://192.168.1.100/Vinimay_Improved/public/`

3. **Firewall**: Ensure port 80 (HTTP) or 443 (HTTPS) is open

## 🌍 Deploying to Web Hosting

### Shared Hosting:
1. Upload files via FTP/SFTP to `public_html` folder
2. Adjust paths if needed
3. Create database via hosting control panel
4. Update `app/config/database.php` with hosting credentials
5. Import `sql.txt` via phpMyAdmin

### Cloud Hosting (AWS, DigitalOcean, etc.):
1. Follow server setup steps above
2. Configure domain name
3. Set up SSL certificate (Let's Encrypt)
4. Configure firewall rules
5. Set up automated backups

## 📞 Support

If you encounter issues:
1. Check error logs:
   - Apache: `/var/log/apache2/error.log`
   - PHP: Check `php.ini` for `error_log` location
2. Enable PHP error display (development only):
   ```php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```
3. Check browser console for JavaScript errors

## ✅ Verification Steps

After deployment, verify:
- [ ] Can access login page
- [ ] Can register new user
- [ ] Can login
- [ ] Can add expenses
- [ ] Can view expenses list
- [ ] Can edit expenses
- [ ] Can delete expenses
- [ ] Can view dashboard with charts
- [ ] Can generate reports
- [ ] Can export PDF
- [ ] Can export Excel
- [ ] Categories display correctly
- [ ] Navigation works
- [ ] Logout works

---

**Congratulations!** Your Vinimaya Expense Tracker is now deployed and ready to use! 🎉

