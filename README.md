# My-blog


My Blog is a responsive PHP and MySQL–powered blogging platform featuring user and admin roles, post management, comments, likes, search, and a light/dark theme toggle.

## Table of Contents

- [Features](#features)  
- [Tech Stack](#tech-stack)  
- [Prerequisites](#prerequisites)  
- [Installation](#installation)  
- [Configuration](#configuration)  
- [Database Migrations](#database-migrations)  
- [Usage](#usage)   

## Features

- User registration, login/logout and secure password hashing  
- Role-based access control: **User** vs. **Admin**  
- CRUD operations for posts (publish/draft) with WYSIWYG‐style editor support  
- Commenting system with threaded replies  
- Like/unlike posts with real-time count  
- Search posts by title or author  
- Responsive UI built on Bootstrap 4  
- Light/dark mode toggle with CSS variables and persisted in `localStorage`  
- Animation effects (fade-in, hover transforms, logo pulse)

## Tech Stack

- PHP 7.x  
- MySQL / MariaDB  
- Bootstrap 4  
- Vanilla JavaScript & CSS variables  
- jQuery & Popper.js (for Bootstrap JS)  

## Prerequisites

- XAMPP (or equivalent AMP stack)  
- PHP 7.x or higher  
- MySQL or MariaDB  
- Composer (optional, if you extend with dependencies)  

## Installation

1. Clone or download this repository into your web server’s document root (e.g., `C:\xampp\htdocs\myblog`).  
2. Ensure your server is running Apache and MySQL.  
3. Create a database named `myblog` in phpMyAdmin or via the MySQL CLI.  
4. Import the migration files in order:
   - `migrations/001_create_users_table.sql`  
   - `migrations/002_create_posts_table.sql`  
   - `migrations/003_create_likes_table.sql`  
   *(Optionally add a `comments` table if you implement comment persistence separately.)*  
5. Configure your database connection and base URL in:  
   - `config/db.php`  
   - `config/config.php`  
6. Point your browser to `http://localhost/myblog/index.php` and enjoy!

## Configuration

- **config/db.php**: set `$host`, `$user`, `$pass`, `$db`.  
- **config/config.php**: update `define('BASE_URL', '/myblog/');` if your project path differs.  
- Theme preference is stored in `localStorage` under `theme`.  

## Database Migrations

All SQL migrations are in the `migrations/` folder and can be imported via phpMyAdmin or the MySQL CLI:

```bash
mysql -u root -p myblog < [001_create_users_table.sql](http://_vscodecontentref_/0)
mysql -u root -p myblog < [002_create_posts_table.sql](http://_vscodecontentref_/1)
mysql -u root -p myblog < [003_create_likes_table.sql](http://_vscodecontentref_/2)
```

##Usage
Register a new account at /auth/register.php.
Log in and navigate to Create Post or My Posts in the dashboard.
Publish or save drafts, then view them on the homepage or via search.
Comment on any published post and like/unlike using the buttons.
Toggle between Light and Dark mode via the navbar button—your choice will persist on reload.
As an admin, access /admin/index.php to manage all users and posts.

