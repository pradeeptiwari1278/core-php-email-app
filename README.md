# 📧 Core PHP Email Sender App

A lightweight, no-framework PHP project for sending emails using PHPMailer.  
Includes a Gmail-style frontend with support for dynamic **To**, **CC**, and **BCC** input fields with name + email format, file attachment, and `.env` configuration.

---

# 🚀 Getting Started

## ✅ Requirements

1. PHP >= 7.4
2. Composer

---

## 📦 Installation

1. Clone the project:

    ```bash
    git clone https://github.com/your-username/php-email-sender.git
    cd php-email-sender
    ```

2. Install dependencies using Composer:

    ```bash
    composer install
    ```

3. Copy the `.env` example file:

    ```bash
    cp .env.example .env
    ```

4. Configure the `.env` file with your SMTP credentials (see below).

5. Run the app using PHP's built-in server:

    ```bash
    php -S localhost:8000
    ```

6. Visit the app in your browser:

    ```
    http://localhost:8000
    ```

---

# ⚙️ SMTP Configuration

Open your `.env` file and fill in the values:

```env
SMTP_HOST=smtp.gmail.com         # SMTP server hostname
SMTP_PORT=587                    # Port for TLS (usually 587)
SMTP_USERNAME=you@example.com    # Your email address
SMTP_PASSWORD=your_password      # Your email password or app password
SMTP_FROM_NAME=Your Name         # Sender name for the email
