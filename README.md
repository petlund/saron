# Saron
Saron is a small application for churches to handle the contacts with their members and data on baptism and membership. 
The application builds on jtable (jtable.org) and a PHP-service layer in front of a postgress database.
- Only in Swedish yet!

## Main requirements
- Webserver (PHP enabled)
- Database mysql - Storing member information
- jtable - CRUD UI http://jtable.org
- tcpdf - Dynamic creating pdf
- jpgraph - Dynamic creating graphs (if ERROR 25128 occur http://colekcolek.com/2012/05/16/how-to-fix-jpgraph-error-the-function-imageantialias-is-not-available/)
- Wordpress - WP-OTP plugin for enabling OTP-functionality

### You need to add funtionallity for
- HTTPS/SSL

## Functions
### CRUD-functions for information about:
- Name (First and Last name)
- Birthday (YYYY-MM-DD)
- Address (Street, number, Zip, City, Country - Common to families)
- Contact (Phone, Mobile, Email)
- Membership (Member number, Start date, End date, Previous and Next congragation)
- Baptism (Date of baptism, baptister, congregation of baptism)
- Simple rolebased editiing
### Reports 
- Member directory
- Baptist directory
- Birthdays
- Statistics
- Email

## Future
### Wished functionallity
- Multi language
# Installation
## Database
- Use an existing database or set up a new MySql-Server instance.
- Use the 'databasestructure.sql' (database - dir ) to create and set up the database 
-- The user is a localhost-user. Maybee you have to change that...? 
- The script create the database 'saron', tables, index and an application user 'saron'
- The script add an 'admin' user account.  

## Apache
- Set up an Apache server
- Maybe set 'AddDefaultCharset utf-8' in httpd.conf 
- Think about the need of SSL and HTTPS

## Wordpress
- Install wordpress
- Install the plugin: https://sv.wordpress.org/plugins/user-role-editor/
- add two user roles: 'saron_edit' and 'saron_view'. 
- Bugfix wordpress_ Change wordpress/wp-includes/pluggable.php row 2130 to 'if ( strlen($hash) !== 0 && strlen($hash) <= 32 ) {' to avoid error messages when username isn´t in the database. 
- Easy timer editor: 
- Set session time out to one hour. Saron-gui has a 30 minutes time out 

## Application
- Saron must be installed direct under web_root/wordpress/
- Rename config_template.php to config.php. Update config.php with the database connection information
- Add path to (TCPDF, /wordpress/wp-load.php)
- Session time i set to 30 min (1800000 ms) in /wordpress/saron/saron/util/util.js. In the future this property should be moved to a better place...
