# 📧 Core PHP Email Sender App

A lightweight, no-framework PHP project for sending emails using PHPMailer.  
Includes a Gmail-style frontend with dynamic **To**, **CC**, and **BCC** fields, support for name + email format, attachments, and `.env`-based configuration.

---

## ✨ Features

- ✅ Send HTML emails with attachments
- 📬 Support for Gmail, SendGrid, Mailgun, and Amazon SES
- 👥 Dynamic inputs for **To**, **CC**, and **BCC** with `Name <email@example.com>` format
- 🔒 Secure `.env` configuration (not committed to Git)
- ⚙️ SOLID architecture with service classes and interfaces
- 🧪 Form validation with proper error messages
- 🪵 Centralized logging for exceptions and debug info

---

## 🚀 Getting Started

### ✅ Requirements

- PHP >= 7.4
- Composer

---

## ⚙️ Installation

1. **Clone the project**

    ```bash
    git clone https://github.com/pradeeptiwari1278/core-php-email-app.git
    cd php-email-sender
    ```

2. **Install dependencies**

    ```bash
    composer install
    ```

    > Don't have Composer? Install it:

    ```bash
    sudo apt install composer
    ```

3. **Set up your environment**

    ```bash
    cp .env.example .env
    ```

    Then, open `.env` and configure your desired provider:

    ```dotenv
    SMTP_HOST=smtp.gmail.com
    SMTP_PORT=587
    SMTP_USERNAME=your-email@gmail.com
    SMTP_PASSWORD=your-app-password
    SMTP_FROM_NAME=Your Name
    ```

    > Use real credentials from Gmail, SendGrid, Mailgun, or Amazon SES.

4. **Run the app locally**

    ```bash
    php -S localhost:8000
    ```

5. **Open the browser**

    ```
    http://localhost:8000
    ```
