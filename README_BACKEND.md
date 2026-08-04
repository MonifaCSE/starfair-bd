# STAR FAIR - Local Development & Setup Guide

This project has been migrated from Firebase to a standard **Core PHP 8.x + MySQL** backend. It is optimized to run on standard shared hosting environments (such as Hostinger) and standard local PHP setups (such as XAMPP, MAMP, or the PHP built-in server).

---

## 1. Database Configuration & Setup

### Step A: Create MySQL Database
1. Open your local database management tool (e.g., **phpMyAdmin** on XAMPP).
2. Create a new database named `starfair_db` with `utf8mb4_unicode_ci` collation:
   ```sql
   CREATE DATABASE `starfair_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

### Step B: Import Schema
1. Import the provided schema file [database.sql](database.sql) into the database. This will create three tables:
   - `admins`: Stores dashboard login credentials.
   - `registrations`: Stores submitted student applications.
   - `magazines`: Stores magazine edition titles, PDFs, and cover paths.

### Step C: Set Configuration Credentials
1. Open [backend/config.php](backend/config.php) in your editor.
2. Edit the database connection constants to match your database server details:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'starfair_db');
   define('DB_USER', 'root');      // Your MySQL username
   define('DB_PASSWORD', '');      // Your MySQL password
   ```

---

## 2. Running a Local Server

You can run this project locally using XAMPP or the native PHP built-in development server.

### Option A: Using native PHP built-in server (Recommended for testing)
Open PowerShell / Terminal in the project root directory (`d:\Self\starfair-bd`) and execute:
```bash
php -S localhost:8000
```
Then navigate to `http://localhost:8000` in your web browser.

### Option B: Using XAMPP / MAMP
1. Copy or link the project folder into your server's public root (e.g. `C:\xampp\htdocs\starfair-bd`).
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Access it at `http://localhost/starfair-bd`.

---

## 3. Creating the First Administrator Account

For safety, **no default admin accounts or plain passwords** are seeded in the database.

1. Once the local server is running, navigate to the setup script page:
   `http://localhost:8000/backend/admin/create-admin.php`
2. Enter your desired **Admin Email** and a **Secure Password** (minimum 8 characters).
3. Click **Create Admin Account**.
4. The system will hash the password securely using `password_hash()` and write it to the database.
5. **Self-Destruct security**: Upon successful database entry, the setup script will automatically attempt to delete itself (`unlink()`) from your disk to prevent unauthorized reuse in production.

---

## 4. Verification & Testing Instructions

### A. Testing Registration Submissions
1. Go to the registration page: `http://localhost:8000/registration.html`.
2. Fill out the application form (enter invalid inputs first to check local client-side validation).
3. Select training programmes and events checkboxes.
4. Upload files:
   - Student Photo (JPG/PNG/WEBP only, Max 5MB).
   - NID / Birth Certificate (JPG/PNG/WEBP/PDF only, Max 5MB).
5. Click **Submit Admission Form**.
6. **Expectations**: The form will submit asynchronously. On success, a Bootstrap Modal pops up saying the registration is saved. Check your local directory `/uploads/registrations/` to ensure a new subfolder with generated safe file names was created.

### B. Testing File Upload Validation & Security
1. Try uploading an oversized file (> 5MB) or a file with a forbidden extension (e.g. a `.php` file or `.js` file).
2. **Expectation**: The upload must fail server-side with an error response, and no file must be written to the disk.
3. Direct file URL protection: Copy the path of an uploaded NID file (e.g., `http://localhost:8000/uploads/registrations/{subdir}/nid_bc.pdf`). Attempt to open it in a separate browser tab.
4. **Expectation**: The server must return **403 Forbidden** due to the `.htaccess` configuration inside the registrations folder.

### C. Testing Admin Dashboard Login
1. Go to `http://localhost:8000/admin/index.php`.
2. Try logging in with incorrect credentials to verify the error response.
3. Login using the email and password you created in Step 3.
4. **Expectation**: On success, the session is created and you are redirected to `dashboard.php`.

### D. Testing Admin Dashboard Features
1. **Student registrations**: The table should load all submitted registrations dynamically.
2. **Secure Downloads**: Click **View** on a registration record. A detailed modal opens showing the data. Click the **View / Download NID** or **Download Photo** buttons.
   - **Expectation**: The documents must open or download successfully because your admin session is active.
3. **Delete registration**: Click **Delete** on a record.
   - **Expectation**: The record is removed from the database and the folder containing files inside `/uploads/registrations/{subdir}/` is completely wiped from disk.
4. **Magazine Manager**: Open the "Magazine Manager" tab.
   - Fill out the form (Title, Description, Date Text, Order).
   - Choose a cover image and select a PDF file.
   - Click **Publish Magazine Issue**.
   - **Expectation**: The magazine will be uploaded and appear in the "Published Issues" list immediately. Check `/uploads/magazines/` on your disk to verify the files are present with generated safe filenames.

### E. Testing Magazine Frontend Flipbook
1. Navigate to `http://localhost:8000/magazine.html`.
2. **Expectation**: The magazine page loads and queries `backend/magazine/list.php`. It should render cards for the issues you created in the Admin Panel.
3. Click **Read Now** on any issue.
4. **Expectation**: The interactive PDF flipbook must initialize and allow flipping pages using StPageFlip and PDF.js.

---

## 5. Deployment Checklist for Hostinger

When moving this project from local development to your active **Hostinger shared hosting**:

1. Upload all project files to Hostinger's `public_html` directory.
2. Log in to your Hostinger hPanel and go to **Databases → MySQL Databases**. Create a new database and database user.
3. Open **phpMyAdmin** on Hostinger, choose your new database, and import your local `database.sql` file.
4. Edit the database constants in `/backend/config.php` to match the Hostinger DB Host, DB Name, DB User, and DB Password.
5. Create your initial admin account by running the setup script on your live domain:
   `https://yourdomain.com/backend/admin/create-admin.php`
6. Verify that `/backend/admin/create-admin.php` has successfully deleted itself after you finish setup. If it hasn't (due to filesystem permissions on Hostinger), delete it manually.
7. Set folders permissions on Hostinger:
   - `/uploads/registrations/` must be writable (usually `0755` permissions).
   - `/uploads/magazines/` must be writable (usually `0755` permissions).
