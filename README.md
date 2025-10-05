# SecureShare — File Sharing Web Application

**Live Demo:** [http://ec2-3-21-76-95.us-east-2.compute.amazonaws.com/~tavialiu/login.php](http://ec2-3-21-76-95.us-east-2.compute.amazonaws.com/~tavialiu/login.php)

SecureShare is a lightweight **PHP / MySQL web application** hosted on **AWS EC2** for secure file sharing and collaboration.  
It allows registered users to upload, download, share, unshare, and manage personal files within a protected directory structure.

---

## Features
- **User Authentication:** Register, login, logout, and password reset (bcrypt hashing)
- **File Management:** Upload, download, delete, restore (recycle bin)
- **Sharing System:** Share/unshare files with other registered users
- **Access Control:** User-specific directory permissions
- **Security:** CSRF protection, session management, MIME/type and file-size validation
- **Interface:** Simple PHP-based UI with `style.css` for layout consistency

---

## Project Structure

| File | Purpose |
|------|----------|
| `login.php` / `register.php` / `logout.php` / `forgot_password.php` | User authentication and password management |
| `dashboard.php` | Main user dashboard after login |
| `upload.php` / `view.php` | File upload and viewing |
| `share.php` / `unshare.php` / `view_shared.php` | File sharing and shared file display |
| `trash.php` / `restore.php` / `pdeleted.php` | Recycle bin, restore, and permanent delete logic |
| `style.css` | App styling |


---

## Tech Stack
- **Frontend:** HTML, CSS, JavaScript  
- **Backend:** PHP 8 + Apache + MySQL  
- **Infrastructure:** AWS EC2 (Ubuntu/Linux)  
- **Security:** bcrypt password hashing, CSRF tokens, server-side validation

---

