# Saron
Saron is a small application for churches to handle the contacts with their members and data on baptism and membership. 
The application builds on jtable (jtable.org) and a PHP-service layer in front of a mysql database.
- Only in Swedish yet!

## Main requirements
- Webserver (PHP enabled)
- Database mysql - Storing member and organization information
- jtable - CRUD UI http://jtable.org
- tcpdf - Dynamic creating pdf
- Wordpress - WP-OTP plugin for enabling OTP-functionality

### You need to add funtionallity for
- HTTPS/SSL

## Functions
### CRUD-functions for information about:
#### Person information
- Name (First and Last name)
- Birthday (YYYY-MM-DD)
- Address (Street, number, Zip, City, Country - Common to families)
- Contact (Phone, Mobile, Email)
- Membership (Member number, Start date, End date, Previous and Next congragation)
- Baptism (Date of baptism, baptister, congregation of baptism)
- Simple rolebased editing
#### Organization information
- Organization unit
- Organization tree
- Role
- Postion (and status)
### Reports 
- Member directory
- Baptist directory
- Birthdays
- Statistics
- Email
- Position in orgaization 
- Business log (Log all types of changes)
## Future
### Wished functionallity
- Leave the dependency to Wordpress by adding a login server.
# Installation
## Database
- Use an existing database or set up a new MySql-Server instance. Version 5 or higher.
- Use the scripts in the sql-folder to create and set up the database 
   - The user is a localhost-user. Maybee you have to change that...? 
- The script create the database 'saron', tables, index and an application user 'saron'
- The script add an 'admin' user account.  

## Apache
- Set up an Apache server
- Maybe set 'AddDefaultCharset utf-8' in httpd.conf 
   - The database use utf8mb4 by default

## Wordpress
- Install wordpress
- Install the plugin: https://sv.wordpress.org/plugins/user-role-editor/
- add two user roles: 'saron_edit' and 'saron_view'. 
- Bugfix wordpress_ Change wordpress/wp-includes/pluggable.php row 2130 to 'if ( strlen($hash) !== 0 && strlen($hash) <= 32 ) {' to avoid error messages when username isn´t in the database. 
- Easy timer editor: 
- Set session time out to one hour. Saron-gui has a 30 minutes time out 

## Application
- Add the Saron app to web-root
- Rename config_template.php to config.php. Update config.php with the database connection information
   - There a lot of paths to set.
   - You need to set up a htaccess-file adding the path to config.php
- The application use aes encrypt. Therefor you need a certificate for encryption. 
   - The path to server.key sets in config.php
### web-api
- The browser use the web-api for connection to backend.
- It is posible to use the web-api for other purpose. 
# Security
- Content-Security-Policy
   - Only javascript from saron application is alowed
- Database encryption
- Business log
   - Log all changes in the application
- Input filter in the entity layer (php)
- OTP (Two factor authentification)
- Session handling

