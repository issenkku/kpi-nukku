Mail TLS (Gmail SMTP)

Set the following in your .env (use an App Password):

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
# For Laravel 10/11 with Symfony Mailer, STARTTLS is used on port 587.
# Optionally set:
MAIL_SCHEME=smtp
MAIL_FROM_ADDRESS=your_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

Notes
- Use a Google App Password (2‑Step Verification required). Plain account password will not work.
- If you prefer implicit TLS on port 465, use MAIL_PORT=465 and MAIL_SCHEME=smtps.
- Default config/mail.php uses MAIL_SCHEME/MAIL_HOST/MAIL_PORT and ignores legacy MAIL_ENCRYPTION.

Testing
- In local/dev, you can temporarily set MAIL_MAILER=log to verify messages in logs instead of sending.

