# SKSystem – Backend API

Laravel REST API powering the **SKSystem Flutter mobile application**.

This backend provides the services and data management required by the mobile application, handling API requests, business logic, database operations and communication between the client and server.

## 🚀 Technologies

- **PHP** – Backend programming language
- **Laravel** – Backend framework
- **MySQL** – Database management system
- **REST API** – Client-server communication
- **Composer** – PHP dependency management

## 📁 Project Structure

```text
Api-Sksystem-App/
├── app/              # Application logic, controllers and models
├── bootstrap/        # Application bootstrap
├── config/           # Configuration files
├── database/         # Migrations, seeders and factories
├── public/           # Public entry point
├── resources/        # Views and resources
├── routes/           # API and web routes
├── storage/          # Logs and generated files
├── tests/            # Automated tests
├── .env.example      # Environment configuration example
├── artisan           # Laravel command-line tool
├── composer.json     # PHP dependencies
└── README.md
```

## ⚙️ Installation

### 1. Clone the repository

```bash
git clone https://github.com/nourddine20/Api-Sksystem-App.git
cd Api-Sksystem-App
```

### 2. Install dependencies

```bash
composer install
```

### 3. Configure environment variables

```bash
cp .env.example .env
```

Update your `.env` file with your database configuration:

```env
APP_NAME=SKSystem
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sksystem
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate application key

```bash
php artisan key:generate
```

### 5. Run database migrations

```bash
php artisan migrate
```

### 6. Start the development server

```bash
php artisan serve
```

The API will be available at:

```text
http://127.0.0.1:8000
```

## 🔗 Flutter Application

This repository serves as the backend for the SKSystem mobile application developed using Flutter.

**Client repository:**  
https://github.com/nourddine20/Sksystem-Client-App

The Flutter application communicates with this backend through HTTP requests to access application services and exchange data.

## 🧪 Testing

Run the automated tests using:

```bash
php artisan test
```

## 🔐 Security

- Keep environment variables and credentials private.
- Never commit the `.env` file.
- Configure authentication and API access according to application requirements.
- Validate incoming requests before processing data.

## 👨‍💻 Author

**Noureddine Aharbach**

GitHub: https://github.com/nourddine20

## 📄 License

This project is open-source. Refer to the repository's license for usage and distribution terms.
