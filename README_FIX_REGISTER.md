# Fixing PHP register.php serving issue

Steps to verify and fix PHP files being downloaded instead of executed:

1. Open XAMPP Control Panel and start **Apache** (and **MySQL** if you need the DB).
2. In your browser open: `http://localhost/qwerty/info.php` — you should see the PHP information page.
   - If it downloads or shows source, Apache is not processing PHP.
3. To access the register form use: `http://localhost/qwerty/register.php`

If PHP is not processed:
- Ensure XAMPP's Apache has the PHP module enabled (default XAMPP does).
- Check Apache's `error.log` in XAMPP Control Panel for module load errors.
- Do not open `register.php` via `file://` in the browser; always use `http://`.

If you want, I can guide you through checking XAMPP logs or enabling PHP in Apache.
