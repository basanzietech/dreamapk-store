# Dream APK Store

**Dream APK Store** ni web-based APK repository ya kisasa, iliyojengwa kwa PHP, MySQL, HTML, CSS, na JavaScript. Watumiaji wanaweza kusajili akaunti, ku-upload APK (na icon, screenshots, category, tags), na kudownload apps kwa urahisi. Admin na developers wana dashboard za kisasa zenye graph/charts na analytics.

> **Live Demo:** [https://dreamapk.store](https://dreamapk.store)

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Database Setup](#database-setup)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)

## Features

- **User Registration & Login:** Kisasa, na animation ya kuvutia.
- **APK Upload:** Upload app na icon, APK, screenshots, category, tags (modern form, validation, na feedback ya kisasa).
- **Download Tracking:** Downloads zinahesabiwa na kuhifadhiwa.
- **Filtering & Search:** Chuja apps kwa category, tag, search, na sorting (most downloaded, newest, oldest).
- **Comments:** Watumiaji wanaweza kutoa comments kwenye kila app (modern, animated, na feedback ya haraka).
- **Admin & Developer Dashboard:** Dashboard za kisasa zenye graph/charts (Chart.js) kwa analysis ya apps, downloads, users, nk.
- **Responsive UI:** Inafanya kazi vizuri kwenye desktop, tablet, na simu.
- **Modern UI/UX:** Animations (fadeInUp, zoomIn), hover effects, na muonekano wa kisasa kwenye kila sehemu (cards, tables, modals, footer, alerts).
- **Progress Bar on Upload:** AJAX-based progress bar inaonyesha upload progress.
- **Share Functionality:** Watumiaji wanaweza kushare link ya app kwa urahisi.
- **Safe APK Download:** Headers za usalama zimeboreshwa ili kupunguza alerts za "file is dangerous" kwenye browsers.

## Installation

### Prerequisites
- PHP (version 7.4 au zaidi inapendekezwa)
- MySQL au MariaDB
- Git (ku-clone repo)
- Composer (optional, kwa PHP dependencies)

### Steps

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/basanzietech/dreamapk-store.git
   cd dreamapk-store
   ```

2. **Configure Your Environment:**
   Hariri `includes/config.php` na weka database credentials zako:
   ```php
   $host = 'localhost';
   $db   = 'dream_apkstore';
   $user = 'root';
   $pass = 'root';
   $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
   ```

3. **Set Up the Database:**
   - Hakikisha MySQL server imewekwa na ina-run:
     ```bash
     sudo apt install mysql-server
     sudo systemctl start mysql
     ```
   - Login kama root na tengeneza database na tables:
     ```bash
     database file nimeweka kwenye code database.sql
     ```
   - Kumbuka kubadilisha root password kama inahitajika:
     ```sql
     ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'root';
     FLUSH PRIVILEGES;
   ```

4. **Set Permissions:**
   - Hakikisha `uploads/` directory ipo na ina write permissions:
     ```bash
     mkdir -p uploads
     chmod 777 uploads
     ```

5. **Install PHP MySQL Driver:**
   - Hakikisha extension ya PDO MySQL imewekwa:
   ```bash
     sudo apt install php-mysql
     ```

6. **Run the App Locally:**
   - Anzisha PHP built-in server:
     ```bash
     php -S localhost:8080
     ```
   - Tembelea [http://localhost:8080](http://localhost:8080)

7. **Troubleshooting:**
   - **Error: could not find driver**: Install `php-mysql` na restart server.
   - **Error: Connection refused**: Hakikisha MySQL server ina-run na config.php ina host/user/password sahihi.
   - **Error: Access denied for user 'root'@'localhost'**: Badilisha root user kwenye MySQL kutumia password (angalia hatua ya 3 juu).

## Modern Features & UX
- **Filtering:** Category, tag, search, sorting (all combined)
- **Pagination:** Orodha ya apps ina pagination bora
- **Comments:** Animated, modern, na feedback ya haraka
- **Charts:** Dashboard ya admin na developer ina Chart.js analytics
- **Animations:** Kila sehemu muhimu ina fadeInUp, zoomIn, na hover effects
- **Responsive:** Inafanya kazi vizuri kwenye devices zote

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
