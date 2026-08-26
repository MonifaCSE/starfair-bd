# STAR FAIR - Hostinger Business Shared Hosting Deployment Guide

This guide explains how to deploy the STAR FAIR Fashion and Cultural Training Institute website to a standard Hostinger Business Shared Hosting environment using Hostinger hPanel.

---

## Prerequisites
- A registered domain name pointing to your Hostinger nameservers.
- Access to your **Hostinger hPanel**.

---

## Step-by-Step Deployment Instructions

### Step 1: Upload the Production ZIP File
1. Log in to your **Hostinger hPanel**.
2. Go to **Websites** and click **Manage** next to your domain.
3. Search for and open **File Manager**.
4. Navigate to the **`public_html`** directory.
5. Delete any default placeholder files (like `default.php` or `index.php` created by Hostinger) from `public_html`.
6. Click the **Upload** icon at the top right, select `STAR-FAIR-HOSTINGER-PRODUCTION.zip`, and upload it.

### Step 2: Extract the Package
1. Right-click on the uploaded `STAR-FAIR-HOSTINGER-PRODUCTION.zip` file.
2. Select **Extract**.
3. Choose **`public_html`** as the destination path (enter `.` or leave blank as prompted by File Manager) so that files like `index.html` are placed directly under `public_html/`.
4. Click **Extract**.
5. Once extracted, you can delete the `STAR-FAIR-HOSTINGER-PRODUCTION.zip` file to clean up your space.

### Step 3: Create the MySQL Database
1. Go back to your hPanel home and search for **MySQL Databases** (located under **Databases**).
2. Enter a database name (e.g. `db`), user name (e.g. `admin`), and generate a strong password.
3. Click **Create**.
4. Note down the following generated values:
   - **MySQL Database** (e.g., `u123456789_db`)
   - **MySQL User** (e.g., `u123456789_admin`)
   - **MySQL Host** (Hostinger uses `localhost`)
   - **MySQL Password** (the password you entered)

### Step 4: Import database.sql
1. In the **MySQL Databases** section, scroll down to **List of Current MySQL Databases And Users**.
2. Locate your new database and click **Enter phpMyAdmin**.
3. Select your database from the left-hand menu.
4. Click on the **Import** tab at the top.
5. Click **Choose File** and select **`database.sql`** (which you extracted in the root of your `public_html`).
6. Click **Go** or **Import** at the bottom to execute the schema. You should see success messages indicating the tables `admins`, `registrations`, and `magazines` were created.

### Step 5: Update config.php
1. Go back to the hPanel **File Manager** inside `public_html/backend/`.
2. Right-click **`config.php`** and select **Edit**.
3. Locate lines 9-12 and replace the placeholders with your Hostinger database details:
   ```php
   define('DB_HOST', 'localhost'); // Hostinger MySQL Host
   define('DB_NAME', 'YOUR_HOSTINGER_DATABASE_NAME');
   define('DB_USER', 'YOUR_HOSTINGER_DATABASE_USER');
   define('DB_PASSWORD', 'YOUR_HOSTINGER_DATABASE_PASSWORD');
   ```
4. Save and close the file.

### Step 6: Create the First Administrator
1. In your browser, navigate to the setup script:
   `https://yourdomain.com/backend/admin/create-admin.php`
2. Enter your desired **Admin Email** and a **Secure Password** (minimum 8 characters).
3. Confirm the password and click **Create Admin Account**.
4. The script will securely hash the password and insert it into your database.
5. It will attempt to self-delete itself. **To confirm this, refresh your File Manager inside `public_html/backend/admin/` and verify `create-admin.php` is gone. If it is still there, delete it manually.**

### Step 7: Enable SSL (HTTPS)
1. In Hostinger hPanel, search for **SSL** and install/activate it on your domain.
2. The root `.htaccess` file is already pre-configured to redirect all HTTP traffic to HTTPS securely.

---

## Verifying the Installation

Test the following workflows to verify a successful deployment:
1. **Public Website**: Load `https://yourdomain.com/` and navigate the menus to ensure styles, fonts, and assets load correctly.
2. **Student Registration**: 
   - Fill out the registration form at `https://yourdomain.com/registration.html`.
   - Upload a test photo and a test NID document.
   - Click submit and verify the modal shows submission success.
3. **Admin Portal**: 
   - Go to `https://yourdomain.com/admin/index.php`.
   - Log in using your admin credentials.
   - Check if you see the new student registration.
   - Click "View" to verify student information and check if files (Photo, NID) open/download correctly.
   - Verify that trying to access files directly in `/uploads/registrations/` via browser URL returns **403 Forbidden**.
4. **Magazine Manager**: 
   - Go to the "Magazine Manager" tab in the admin panel.
   - Upload a test cover image and a test PDF file.
   - Verify that the issue loads successfully.
   - Check the front-end flipbook at `https://yourdomain.com/magazine.html` to ensure the new magazine appears and the PDF page flip works correctly.
5. **Clean Up**: Make sure you have deleted `create-admin.php` and `database.sql` from your server if they were not removed automatically.
