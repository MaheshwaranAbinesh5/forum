# Discussion Forum (PHP & MySQL)

A simple web-based discussion forum where students can register, log in, browse categories, start threads and reply to each other.

## Features

- User registration, login and logout (session-based)
- Discussion categories: Course Discussion, General Discussion, Help & Support
- Create new threads inside a category
- Post replies on any thread
- Character counter on thread and reply forms
- Login required for creating threads and replying

## Tech Stack

- **Backend:** PHP
- **Database:** MySQL (accessed with `mysqli` and prepared statements)
- **Frontend:** HTML, CSS, JavaScript, Bootstrap
- **Local server:** XAMPP (Apache + MySQL)

## Database Design

Four related tables in `forum_db`: `users`, `categories`, `threads`, `replies`, connected with foreign keys (a thread belongs to a user and a category, a reply belongs to a user and a thread).

## Project Structure

```
forum/
├── index.php           # Home page - list of categories
├── category.php        # Threads in a category
├── thread.php          # A thread and its replies
├── create_thread.php   # Start a new thread
├── post_reply.php      # Reply to a thread
├── register.php        # Sign up
├── login.php           # Log in
├── logout.php          # Log out
├── database.sql        # Database and tables setup
├── config/database.php # Database connection
├── includes/           # Header, footer, helper functions
├── css/style.css       # Styling
└── js/script.js        # Small front-end scripts
```

## How to Run Locally

1. Install [XAMPP](https://www.apachefriends.org/) and start **Apache** and **MySQL**.
2. Copy this project folder to `C:\xampp\htdocs\forum`.
3. Open `http://localhost/phpmyadmin`, go to the **Import** tab and import `C:\xampp\htdocs\forum\database.sql`. This creates the `forum_db` database and the default categories.
4. Check the database settings in `C:\xampp\htdocs\forum\config\database.php` (default is user `root` with no password).
5. Open `http://localhost/forum/` in your browser and register a new account.

## Screenshots

Add your screenshots here, for example:

- Home page with categories
- Thread page with replies
- Register / login page

## Author

Maheshwaran Abinesh - [GitHub](https://github.com/MaheshwaranAbinesh5) | [LinkedIn](https://www.linkedin.com/in/maheshwaran-abinesh-456a21322/)
