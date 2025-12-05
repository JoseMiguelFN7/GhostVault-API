# 👻 GhostVault API

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=flat&logo=docker&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=flat)

**GhostVault** is a secure ephemeral messaging API designed under a **Zero-Knowledge** architecture. It allows for the storage of secrets (text and files) that self-destruct atomically after being read once (*Burn-on-Read*).

> **Security Note:** The server acts as a blind storage. It does not encrypt or decrypt information. It receives client-encrypted "blobs" and delivers them as-is. The decryption key never touches this Backend.

---

## 👥 Authors

- **José Ferreira** - [GitHub Profile](https://github.com/JoseMiguelFN7)
- **Cesar Vethencourt** - [GitHub Profile](https://github.com/Cvethencourt)
- **Javier Regnault** - [GitHub Profile](https://github.com/jregnaultt)

---

## 🚀 Key Features

-   🔥 **Strict Burn-on-Read:** Atomic deletion from the database immediately after the first successful read (ACID Transactions).
-   🛡️ **Zero-Knowledge Architecture:** The server stores client-side encrypted content; it has no knowledge of the actual data.
-   ⏰ **Auto-Expiration (Garbage Collector):** Automatic cleanup of secrets that were never read within the stipulated time frame.
-   🔑 **API Security:** Protected via `X-API-KEY` and strict CORS configuration.
-   🐳 **Dockerized:** Isolated and reproducible development environment using Laravel Sail.

---

## 📋 Prerequisites

To run this project locally, you only need:

* [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Running)
* [Git](https://git-scm.com/)
* A terminal (PowerShell, WSL2, or Mac/Linux Terminal)

**You do not need to have PHP or Composer installed on your local machine.**

---

## 🛠️ Installation Guide

Follow these steps to set up the development environment from scratch:

### 1. Clone the Repository
```bash
git clone [https://github.com/YOUR_USERNAME/ghostvault-backend.git](https://github.com/YOUR_USERNAME/ghostvault-backend.git)
cd ghostvault-backend
```

### 2. Install Dependencies

Since we use Laravel Sail, we will install dependencies using a temporary Docker container. Run the command corresponding to your operating system:

**Option A: Linux, Mac, or WSL2**
```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php84-composer:latest \
    composer install --ignore-platform-reqs
```

**Option B: Windows (PowerShell)**
```bash
docker run --rm `
    -v ${PWD}:/var/www/html `
    -w /var/www/html `
    laravelsail/php84-composer:latest `
    composer install --ignore-platform-reqs
```

### 3. Environment Configuration (.env)

Copy the example file:
```bash
cp .env.example .env
```

Open the newly created .env file and adjust the following variables to ensure proper connection with Docker:
```Ini
# API Port (Configured to 8000 to avoid conflicts)
APP_PORT=8000

# Database Configuration (Internal Docker Network)
# IMPORTANT: Host must be 'mysql', NOT 'localhost'
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=ghostvault
DB_USERNAME=sail
DB_PASSWORD=password

# External port for DB clients (TablePlus/HeidiSQL)
FORWARD_DB_PORT=3307

# Your Master Access Key (Define it yourself)
GHOSTVAULT_API_KEY=my_super_secret_api_key
```

### 4. Start Containers

Start the environment with Laravel Sail (this will download the necessary images the first time):
```bash
./vendor/bin/sail up -d
```

### 5. Final Configuration

Generate the application encryption key and run the migrations to create the database structure:
```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
```

**✅ Done! The API is running and accessible at: http://localhost:8000**

## 📡 API Usage

All requests must include the security header configured in your .env.

**Required Header: X-API-KEY: my_super_secret_api_key**

### 1. Create a Secret (POST)

The client (Frontend) is responsible for encrypting the content before sending it.

**Endpoint: POST /api/v1/secrets**

Body (JSON):
```json
{
  "content": "U2FsdGVkX1+...",  // Encrypted string
  "requires_password": true,      // Informational flag for the front-end
  "expires_in_hours": 2,          // Time to live (Default: 1, Max: 168)
  "files": [                      // Optional: Array of encrypted files
    {
      "encrypted_name": "U2FsdGVkX1+...", //Encrypted file name
      "file_data": "BASE64_ENCRYPTED_FILE_CONTENT..." // Encryped Base64 file string
    }
  ]
}
```

### 2. Read and Burn a Secret (GET)

This action is destructive. Once a successful response is delivered, the record and its associated files are permanently deleted from the database and file system.

**Endpoint: GET /api/v1/secrets/{uuid}**

Success Response (200):
```json
{
  "content": "U2FsdGVkX1+...",
    "requires_password": true,
    "files": [
        {
            "encrypted_name": "U2FsdGVkX1+...",
            "file_data": "BASE64_ENCRYPTED_FILE_CONTENT..."
        }
    ]
}
```

Response if already read or expired (404):
```json
{
  "message": "Resource not found."
}
```

## ⏰ Scheduled Tasks (Auto-Cleanup)

The system features a "Garbage Collector" that deletes expired secrets that were never read. To activate this worker in development:
```bash
./vendor/bin/sail artisan schedule:work
```

This will execute the cleanup task according to the configured frequency (default: hourly).

## 📄 License

This project is licensed under the MIT License.

---

## 🔗 Related Projects

- [GhostVault Frontend](https://github.com/JoseMiguelFN7/GhostVault) - React Platform