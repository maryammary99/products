# Product Management System

This Laravel project provides a solution for importing and synchronizing products with an external API. The core functionalities include importing products from a CSV file, synchronizing with an external data source, handling product variations, and implementing background job processing for improved performance.

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Queue Setup for Performance Optimization](#queue-setup-for-performance-optimization)
- [Command Overview](#command-overview)
- [Troubleshooting](#troubleshooting)

---

## Requirements

- **PHP 8.x**
- **Laravel 9.x**
- **MySQL 5.7+** or **PostgreSQL**
- **Composer**
- **Redis** (optional, for advanced queue management)

---

## Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/yourusername/yourrepository.git
   cd yourrepository

2.Install dependencies:
composer install

3.Environment setup: Copy the example environment configuration:

cp .env.example .env
4.Run migrations:

php artisan migrate


Voici le contenu complet du fichier README.md que vous pouvez utiliser dans votre projet. Vous pouvez simplement copier et coller ce code dans le fichier README.md à la racine de votre projet.

markdown
Copier le code
# Product Management System

This Laravel project provides a solution for importing and synchronizing products with an external API. The core functionalities include importing products from a CSV file, synchronizing with an external data source, handling product variations, and implementing background job processing for improved performance.

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Queue Setup for Performance Optimization](#queue-setup-for-performance-optimization)
- [Command Overview](#command-overview)
- [Troubleshooting](#troubleshooting)

---

## Requirements

- **PHP 8.x**
- **Laravel 9.x**
- **MySQL 5.7+** or **PostgreSQL**
- **Composer**
- **Redis** (optional, for advanced queue management)

---

## Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/maryammary99/products.git
   cd products
Install dependencies:
composer install

Environment setup: Copy the example environment configuration:
cp .env.example .env
Then, configure your .env file with database credentials and other necessary configurations (see Configuration).

Run migrations:
php artisan migrate

Configuration
External API Configuration
The system synchronizes with an external API for product data. Ensure the external API URL is defined in the ProductImportService.php file:
private const EXTERNAL_API_URL = 'https://5fc7a13cf3c77600165d89a8.mockapi.io/api/v5/products';

Queue Connection
To improve performance, configure the queue system in your .env file:
QUEUE_CONNECTION=database
If using database as the queue driver, ensure the queue table is migrated:
php artisan queue:table
php artisan migrate

To process the queue, you can start a worker:
php artisan queue:work

Usage
Product Import from CSV
The ProductImportService provides a method to import products from a CSV file. To run this import manually, use the command:
php artisan import:products

Daily Synchronization with External API
The system is set up to synchronize with an external API daily at midnight. This schedule is configured in app/Console/Kernel.php.
To manually trigger synchronization:
php artisan sync:products


### Instructions to Use the `README.md`

1. **Save** this content into a `README.md` file at the root of your Laravel project.
2. If any sections need to be customized (like repository links or environment variables), modify the content accordingly.
3. Developers can refer to this document for setup, configuration, and usage instructions. It also provides troubleshooting advice for common issues.

This will give users and other developers clear guidance on how to set up and use your Laravel project.


