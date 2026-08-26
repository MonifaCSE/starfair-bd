# STAR FAIR - Production Deployment Checklist

Use this checklist during your deployment on Hostinger to ensure that all security, database, configuration, and feature tests pass.

---

## 1. File Upload & Extraction
- [ ] ZIP uploaded to Hostinger `public_html` folder.
- [ ] ZIP successfully extracted directly inside `public_html/` (no extra nested directory).
- [ ] ZIP file deleted from server post-extraction.

## 2. Database Initialization
- [ ] MySQL Database created via hPanel.
- [ ] `database.sql` imported successfully via Hostinger phpMyAdmin.
- [ ] All three tables (`admins`, `registrations`, `magazines`) created with correct columns.
- [ ] `database.sql` deleted from server to prevent exposing schema.

## 3. Configuration & Authentication
- [ ] `backend/config.php` database credentials replaced with actual Hostinger credentials.
- [ ] SSL activated in Hostinger hPanel.
- [ ] Initial admin account created using `backend/admin/create-admin.php`.
- [ ] Setup file `backend/admin/create-admin.php` successfully deleted (automatically or manually).
- [ ] Admin login page (`admin/index.php`) loads and authenticates successfully.
- [ ] Unauthorized users attempting to access `admin/dashboard.php` are redirected back to login.

## 4. Student Registration Workflow
- [ ] Form validates empty/invalid inputs on submission.
- [ ] Asynchronous upload works for photos and NID documents.
- [ ] Form successfully registers and displays success modal.
- [ ] Uploaded student files are stored inside `/uploads/registrations/{unique_hash}/`.

## 5. Security & Isolation
- [ ] Direct web browser URL access to files inside `/uploads/registrations/` returns a **403 Forbidden** error.
- [ ] Root `.htaccess` file prevents directory listing (e.g. going to `https://yourdomain.com/backend/` returns **403 Forbidden**).
- [ ] Direct web access to `backend/config.php` and `backend/database.php` is blocked.
- [ ] All forms and backend SQL transactions use secure PDO prepared statements (XSS/SQL injection protection).

## 6. Admin Management Features
- [ ] New registration shows up in the admin dashboard table.
- [ ] Searching/filtering registrations functions correctly.
- [ ] View modal loads the full registration details and links.
- [ ] Documents can be proxy-downloaded/viewed securely through `registration-view.php`.
- [ ] Deleting a registration removes it from the MySQL database and deletes its folder and files from disk.

## 7. Magazine flipbook System
- [ ] Adding a new magazine (Title, Description, Date, Order, Cover, PDF) in the Admin panel completes without error.
- [ ] New magazine appears dynamically in `magazine.html`.
- [ ] Cover image renders correctly in the issues grid.
- [ ] Clicking "Read Now" loads the flipbook viewer.
- [ ] StPageFlip and PDF.js load pages correctly from the PDF file.
- [ ] Page flip sounds and transitions execute without console errors.
- [ ] Deleting a magazine issue from the Admin panel removes it from the database and deletes files from `/uploads/magazines/`.

## 8. General Health Check
- [ ] Website is fully responsive on Mobile, Tablet, and Desktop.
- [ ] All canonical links, Open Graph tags, and structured JSON-LD point to `https://starfairbd.com/`.
- [ ] Browser developer tools console shows 0 errors.
- [ ] Sitemap (`sitemap.xml`) and crawler file (`robots.txt`) are active and validated.
