**Author of this README file:** Yash Kapoor   
**Email:** YashKapoor@cmail.carleton.ca

Description:
------------
This project is a simplified recreation of the social media platform X (formerly known as Twitter). 

This project was split into three milestones:

The first milestone focuses on HTML basics such as markup, forms, and tables, enhanced with CSS styling.
The second milestone utilizes PHP to enable interactions with a SQL database, facilitating data insertion
and display. 
The third milestone extends PHP functionality to support session variables for user authentication and admin
user management.

This application is composed of five pages:

register.php: User can enter their personal information, along with their profile information.
login.php: User logs in with their credentials (email and password).
profile.php: User can edit their profile information and add additional information such as their address.
index.php: Users can make posts that are up to 280 characters long. This page shall display the last 10 posts from all users in the database.
user_list.php: Only accessible to administrators. It displays a table that consists of all the users (regular users and admins) that are 
currently registered on the platform.

Installation:
-------------
Follow the installation guide on https://www.apachefriends.org/download.html to install XAMPP on your machine.

Usage:
-------
1. Create a folder called "SYSCX" in your XAMPP's "htdocs" directory.
2. Open a terminal or command prompt, navigate to the "SYSCX" folder, and clone the repository by typing the following command:
```
git clone https://github.com/YashKapoor1102/SYSC4504-SYSCX.git
```
3. Start the XAMPP Control Panel and start the Apache and MySQL services.
4. Access phpMyAdmin from your web browser, which is usually at http://localhost/phpmyadmin.
5. Click on "SQL" tab in the top toolbar of phpMyAdmin. Open the file "yash_kapoor_a03.sql"
with a text editor, copy all its contents, and then paste them into the SQL command box in
phpMyAdmin to import your database schema.
6. Open a web browser and navigate to `http://localhost/SYSCX/register.php` to start using the application.
7. To access admin features like the user list, ensure you have admin privileges which can be set in the database.

Credits:
-------
- Yash Kapoor - Developer of this project, responsible for the implementation and documentation.
- Dr. Rami Sabouni (Course Instructor) - Designed the project guidelines.
