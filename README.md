## Podeo Podcast

Steps to Run the application:

- Create Database Schema and name it "podeo"
- Create Database Username "u_podeo" with the following password "Podeo@123"
- Run the following commands :
    ~~~~
      php artisan migrate
      php artisan db:seed
    ~~~~


Login Credentials:
<br>
Username: mohammadzahwy2017@gmail.com
<br>
Password: admin@123

## Deploy Application on Apache Server using Ubuntu

- Move all application files to directory /var/www/podeo/
- Run the following commands 
    ~~~~
  cd /etc/apache2/sites-available
  sudo cp /etc/apache2/sites-available/000-default.conf etc/apache2/sites-available/podeo.com.conf
    ~~~~
- Add the following Lines to the copied file
    ~~~~
    <VirtualHost *:3250>
    ServerName podeo.com
    ServerAdmin webmaster@podeo.com
    DocumentRoot /var/www/html/podeo/public_html
    
    <Directory /var/www/podeo/public_html>
    AllowOverride All
    </Directory>
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
    </VirtualHost>
    ~~~~
- To run the application on the port mentioned above(3250) add the following line to the file /etc/apache2/ports.conf
    ~~~~
    Listen 3250
    ~~~~
- Run the following commands 
    ~~~~
    sudo a2ensite podeo.com
    sudo systemctl reload apache2
    sudo a2enmod rewrite
    sudo systemctl restart apache2
  ~~~~

## Test Application Using Postman
Import the following file "Podeo.postman_collection.json" to the postman application to test the api routes, it includes the below routes:

- Login
- Logout
- Revoke All Tokens
- Revoke All Tokens Except Current Token
- CRUD Routes For Podcast
- CRUD Routes For Episode


