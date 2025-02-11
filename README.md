# Dream APK Store

**Dream APK Store** is a web-based APK repository built with PHP, MySQL, HTML, CSS, and JavaScript. Users can register, log in, upload their APKs (along with an app icon and screenshots), and download apps from the site. The project also includes an admin section for managing users and apps.

> **Live Demo:** [https://dreamapk.store](https://dreamapk.store)

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)

## Features

- **User Registration & Login:** Users can register and log in.
- **APK Upload:** Users can upload an app with an app icon, APK file, and up to 4 screenshots.
- **Download Tracking:** Downloads are counted and stored.
- **Admin Section:** Admins can manage users, assistants, and apps.
- **Responsive UI:** Built with Bootstrap and custom CSS, the project is responsive on desktops and mobile devices.
- **Progress Bar on Upload:** An AJAX-based progress bar shows upload progress.
- **Share Functionality:** Users can share the download link via the browser’s Web Share API or copy the link to the clipboard.

## Installation

### Prerequisites

- A web server with PHP (version 7.0 or higher recommended)
- MySQL or MariaDB
- Git (to clone the repository)
- Composer (optional, if you want to manage PHP dependencies)

### Steps

1. **Clone the Repository:**

   ```bash
   git clone https://github.com/basanzietech/dreamapk-store.git
   cd dreamapk-store
   ```

2. **Configure Your Environment:**

   Create a copy of your configuration file (if needed) and update it with your database credentials. For example, in `includes/config.php`, ensure you set:

   ```php
   $host = 'localhost';
   $db   = 'dream_apkstore'; // Change if your database name is different
   $user = 'root';           // Change to your database username
   $pass = '';               // Change to your database password
   $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
   ```

3. **Set Up the Database:**

   Use your preferred method (phpMyAdmin, MySQL CLI, etc.) to create a new database (if it does not exist) and execute the following SQL commands to create the necessary tables:

   ```sql
   -- Table for users
   CREATE TABLE users (
     id INT AUTO_INCREMENT PRIMARY KEY,
     username VARCHAR(100) NOT NULL,
     email VARCHAR(150) NOT NULL UNIQUE,
     password VARCHAR(255) NOT NULL,
     role ENUM('user', 'assistant', 'admin') DEFAULT 'user',
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   

--Categories Table
```sql
CREATE TABLE Categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(255) NOT NULL
);

 --Developers Table
sql
CREATE TABLE Developers (
    developer_id INT PRIMARY KEY AUTO_INCREMENT,
    company_name VARCHAR(255) NOT NULL,
    contact_email VARCHAR(255),
    website_url VARCHAR(255)
);

   -- Table for apps
   CREATE TABLE apps (
     id INT AUTO_INCREMENT PRIMARY KEY,
     user_id INT NOT NULL,
     app_name VARCHAR(150) NOT NULL,
     description TEXT NOT NULL,
     logo VARCHAR(255) NOT NULL,
     apk_file VARCHAR(255) NOT NULL,         -- Stores the APK file path
     screenshots TEXT,                       -- Stores JSON (screenshot paths)
     downloads INT DEFAULT 0,
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
     FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
   );
   ```
   

4. **Set Permissions:**

   Ensure that the `uploads/` directory exists in the root of your project and has proper write permissions so that PHP can store the uploaded files.

5. **Configure Your Web Server:**

   Point your web server’s document root to your project directory. For example, in Apache, you may set up a virtual host that points to the project folder.

6. **Install Dependencies (Optional):**

   If you plan to use Composer or other package managers for additional libraries (e.g., PHPMailer for sending emails), run:

   ```bash
   composer install
   ```

## Usage

- **Registration & Login:**  
  Users register via `register.php` and log in via `login.php`.

- **Uploading an App:**  
  Once logged in, users can access `upload_app.php` from the dashboard to upload or update an app. The form includes:
  - App Name and Description  
  - App Icon (must be no larger than 512x512px and under 500KB)  
  - APK File  
  - Up to 4 Screenshots (each no larger than 1080x1920px and under 1MB)

- **Download & Share:**  
  On the homepage (`index.php`), apps are displayed in a grid with their icon, name, description, and a Download button that triggers the download via `download.php`.

- **Admin Panel:**  
  Admin users (and assistants with appropriate permissions) can manage users and apps via the admin folder pages.

## Contributing

Contributions are welcome! To contribute:

1. **Fork the Repository:**  
   Click the "Fork" button on GitHub to create your own copy of the repository.

2. **Create a Feature Branch:**  
   ```bash
   git checkout -b feature/YourFeatureName
   ```

3. **Commit Your Changes:**  
   Follow the commit message guidelines and include clear descriptions of your changes.

4. **Push Your Changes:**  
   ```bash
   git push origin feature/YourFeatureName
   ```

5. **Open a Pull Request:**  
   Submit a pull request (PR) on GitHub with a detailed explanation of your changes.

6. **Follow the Code Guidelines:**  
   - Write clean, readable code.
   - Follow the existing coding style.
   - Test your changes thoroughly.

## Testing the Project

1. **Local Testing:**  
   - Set up your local environment using a web server (e.g., XAMPP, WAMP, or MAMP).
   - Import the database schema provided above.
   - Ensure that your `includes/config.php` has the correct database credentials.
   - Access the project via your browser (e.g., http://localhost/dreamapk-store).

2. **Live Demo:**  
   - The project is deployed at [https://dreamapk.store](https://dreamapk.store) for testing.

## License

This project is licensed under the [MIT License](LICENSE).
