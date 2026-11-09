✔ To get CINEVERSE up and running, you essentially need a local web server (like XAMPP or Laragon) to handle the PHP and MySQL database.

✔ Short Summary
CINEVERSE is a dual-portal cinema management system.

Managers use the Admin portal to manage the movie library, schedules, and theaters.
Audience members use the main portal to browse movies and book digital tickets.

Tech: PHP, MySQL

✔ How to Install
1. Prerequisites
Download and install XAMPP (or any WAMP/MAMP stack) to provide the Apache server and MySQL database.
2. Setup the Files
Open your XAMPP installation folder (usually C:\xampp\htdocs).
Create a new folder (e.g. Cineverse).
Place all your .php files (index, signup, dash, dbcon, etc.) inside that folder.
3. Configure the Database
Open your browser and go to http://localhost/phpmyadmin.
Click New on the left sidebar and create a database named cinema_db.
Click on the SQL tab at the top.
Copy the content of your cinema_db.sql file, paste it into the box, and click Go.
This will automatically create your tables and the default admin account.
4. Connect PHP to MySQL
5. Run the Project
Open the XAMPP Control Panel and click Start for both Apache and MySQL.
In your browser, go to: http://localhost/Cineverse/index.php
(replace "Cineverse" with whatever folder name you used in step 2)

Default Credentials (from your SQL)
Admin/Manager Login: * Username: admin

Password: 123

Audience Login: Register a new account via signup.php to begin.

✔ Troubleshooting
If you see an error like "Unknown column 'show_date' in 'field list'", your
live database table is missing a column that the PHP code expects. This
happens if cinema_db.sql was updated after you first imported it. Fix it by
re-running the CREATE TABLE statements, or by running an ALTER TABLE
statement to add just the missing column(s) without losing existing data.
When in doubt, open phpMyAdmin -> cinema_db -> the affected table -> Structure
tab, and compare its columns against cinema_db.sql.