# Quick Setup Checklist

Use this checklist when deploying to a new device.

## Pre-Deployment
- [ ] Copy entire project folder to new device
- [ ] Place in web server directory (htdocs/www/public_html)
- [ ] Note the folder name/path

## Database Setup
- [ ] Install MySQL/MariaDB (if not installed)
- [ ] Start MySQL service
- [ ] Open phpMyAdmin or MySQL command line
- [ ] Create database: `vinimaya`
- [ ] Import `sql.txt` file
- [ ] Verify 18 categories exist (including "Rent")

## Configuration
- [ ] Open `app/config/database.php`
- [ ] Update database host (if remote)
- [ ] Update database username
- [ ] Update database password
- [ ] Save file

## Web Server Setup
- [ ] Start Apache/Nginx
- [ ] Verify `mod_rewrite` is enabled (Apache)
- [ ] Check `.htaccess` exists in `public` folder
- [ ] Set proper file permissions (Linux/Mac)

## Testing
- [ ] Open browser: `http://localhost/[project-folder]/public/`
- [ ] See login/register page
- [ ] Register a new account
- [ ] Login successfully
- [ ] Add a test expense
- [ ] View expenses list
- [ ] Check dashboard loads
- [ ] Test PDF export
- [ ] Test Excel export
- [ ] Verify categories display correctly
- [ ] Test logout

## Common Issues
- **Database connection error**: Check credentials in `database.php`
- **404 errors**: Verify `.htaccess` and `mod_rewrite`
- **CSS/JS not loading**: Clear cache, check BASE_URL
- **Permission denied**: Check file permissions (Linux/Mac)

## Success!
If all checkboxes are complete, your application is ready to use! 🎉

