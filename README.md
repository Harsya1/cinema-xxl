# Cinema XXL

A comprehensive cinema management system built with Laravel 12, featuring ticket booking with QR code e-tickets, food and beverage ordering with POS, studio management, and staff operations.

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Schema](#database-schema)
- [User Roles](#user-roles)
- [Default Credentials](#default-credentials)
- [API Integration](#api-integration)
- [Screenshots](#screenshots)
- [License](#license)

## Features

### Public Website
- **Movie Browsing**: Browse now playing, coming soon, and popular movies
- **Movie Details**: View movie information, trailers, cast, and reviews
- **Seat Selection**: Interactive seat map for ticket booking
- **User Registration**: Create account with email verification
- **User Profile**: View booking history, watchlist, and account settings

### Ticket Booking
- **Real-time Seat Selection**: Interactive seat map with availability status
- **Multiple Seats**: Book up to 6 seats per transaction
- **Payment Options**: Cash and QRIS payment methods
- **QR Code E-Tickets**: Downloadable PDF tickets with QR codes
- **Booking History**: View active and past bookings in user profile

### Food and Beverage
- **Menu Management**: Categorized menu items (Snacks, Drinks, Combos, Meals)
- **Inventory Control**: Track stock levels with low-stock alerts
- **Recipe Management**: Define ingredients for each menu item with automatic stock deduction
- **POS System**: Complete point-of-sale for FnB staff with order management
- **Inventory Dashboard**: Real-time stock monitoring and restocking

### Cinema Operations
- **Studio Management**: Support for Regular, 3D, and Premier studio types with configurable seating
- **Showtime Scheduling**: Create and manage movie schedules with TMDb integration
- **Cleaning Task Management**: Track and assign cleaning tasks after each show
- **Staff Management**: Role-based access for different staff types

### Administration
- **User Management**: Role-based access control with 6 user roles
- **Audit Logging**: Track system changes and user activities
- **Dashboard Analytics**: Real-time statistics and reports
- **Resource Management**: Full CRUD for all system entities

## Tech Stack

| Component | Technology |
|-----------|------------|
| Framework | Laravel 12 |
| PHP Version | 8.4 |
| Database | MySQL 8.0 |
| Admin Panel | FilamentPHP 3.x |
| Frontend | Livewire 3.x, Alpine.js, Tailwind CSS 4.x |
| PDF Generation | DomPDF |
| QR Code | Simple QRCode (via api.qrserver.com) |
| Build Tool | Vite 7.x |
| Containerization | Docker |

## Requirements

- Docker and Docker Compose
- Git
- TMDb API Key (for movie data)

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/cinema-xxl.git
cd cinema-xxl
```

### 2. Environment Setup

```bash
cp .env.example .env
```

### 3. Configure Environment Variables

Edit `.env` file with your configuration:

```env
APP_NAME="Cinema XXL"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=cinema_db
DB_USERNAME=root
DB_PASSWORD=aura1101

TMDB_API_KEY=your_tmdb_api_key_here
```

### 4. Start Docker Containers

```bash
docker-compose up -d
```

### 5. Install Dependencies

```bash
docker exec cinema-app composer install
docker exec cinema-app npm install
```

### 6. Generate Application Key

```bash
docker exec cinema-app php artisan key:generate
```

### 7. Run Migrations and Seeders

```bash
docker exec cinema-app php artisan migrate:fresh --seed
```

### 8. Build Assets

```bash
docker exec cinema-app npm run build
```

### 9. Access the Application

- **Main Application**: http://localhost:8000
- **Admin Panel**: http://localhost:8000/admin

## Configuration

### Docker Services

| Service | Container Name | Port |
|---------|---------------|------|
| PHP-FPM | cinema-app | 9000 (internal) |
| Nginx | cinema-web | 8000 |
| MySQL | cinema-db | 3306 |

### TMDb Integration

To enable movie browsing and data fetching:

1. Register at [The Movie Database (TMDb)](https://www.themoviedb.org/)
2. Generate an API key from your account settings
3. Add the key to your `.env` file:

```env
TMDB_API_KEY=your_api_key_here
```

## Database Schema

### Core Tables

| Table | Description |
|-------|-------------|
| users | System users with role assignments |
| studios | Cinema studios/halls |
| showtimes | Movie schedules |
| bookings | Ticket reservations |
| cleaning_tasks | Studio cleaning assignments |

### Food and Beverage Tables

| Table | Description |
|-------|-------------|
| inventory_items | Raw materials and supplies |
| menu_items | Food and beverage products |
| recipes | Ingredient mappings for menu items |
| fnb_orders | Customer orders |
| fnb_order_details | Order line items |

### System Tables

| Table | Description |
|-------|-------------|
| audit_logs | System activity tracking |
| sessions | User session management |
| cache | Application cache storage |

## User Roles

The system implements comprehensive Role-Based Access Control (RBAC) using Laravel Policies.

| Role | Access Level | Description |
|------|-------------|-------------|
| Admin | Full | Complete system access, all CRUD operations |
| Manager | High | Read access to all resources, operations oversight |
| Cashier | Medium | Ticket POS, booking management |
| FnB Staff | Medium | FnB POS, menu and inventory management |
| Cleaner | Low | View and update assigned cleaning tasks |
| User | Basic | Customer account, public website only |

### Access Control Matrix

| Resource | Admin | Manager | Cashier | FnB Staff | Cleaner |
|----------|-------|---------|---------|-----------|---------|
| Showtimes | Full | Read | Read | ❌ | ❌ |
| Studios | Full | Read | Read | ❌ | Read |
| Bookings | Full | Read | Create/Update | ❌ | ❌ |
| Cleaning Tasks | Full | Read | ❌ | ❌ | Update Own |
| Menu Items | Full | Read | ❌ | Full | ❌ |
| FnB Orders | Full | Read | ❌ | Full | ❌ |
| Inventory | Full | Read | ❌ | Full | ❌ |
| Users | Full | Read | ❌ | ❌ | ❌ |

### POS Access

| POS System | Admin | Manager | Cashier | FnB Staff | Cleaner |
|------------|-------|---------|---------|-----------|---------|
| Ticket POS | ✅ | ✅ | ✅ | ❌ | ❌ |
| FnB POS | ✅ | ✅ | ❌ | ✅ | ❌ |

### Dashboard Widgets

| Widget | Admin | Manager | Cashier | FnB Staff | Cleaner |
|--------|-------|---------|---------|-----------|---------|
| Stats Overview | ✅ | ✅ | ❌ | ❌ | ❌ |
| Today's Showtimes | ✅ | ✅ | ✅ | ❌ | ❌ |
| Latest Bookings | ✅ | ✅ | ✅ | ❌ | ❌ |
| Low Stock Alert | ✅ | ✅ | ❌ | ✅ | ❌ |

## Default Credentials

After running seeders, the following accounts are available:

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@cinema-xxl.com | password |
| Manager | manager@cinema-xxl.com | password |
| Cashier | cashier@cinema-xxl.com | password |
| FnB Staff | fnb@cinema-xxl.com | password |
| Cleaner | cleaner@cinema-xxl.com | password |
| User | user@cinema-xxl.com | password |

**Important**: Change these credentials immediately in production environments.

## API Integration

### TMDb Service

The system integrates with TMDb API for:
- Movie search and discovery
- Movie details and metadata
- Poster and backdrop images
- Genre information

### Available Methods

```php
// Search movies
$tmdb->searchMovies('movie title');

// Get movie details
$tmdb->getMovie($tmdbId);

// Get now playing movies
$tmdb->getNowPlaying($page);

// Get popular movies
$tmdb->getPopular($page);

// Get upcoming movies
$tmdb->getUpcoming($page);
```

## Project Structure

```
cinema-xxl/
├── app/
│   ├── Enums/              # Application enumerations
│   │   ├── BookingStatus.php
│   │   ├── CleaningStatus.php
│   │   ├── MenuCategory.php
│   │   ├── PaymentMethod.php
│   │   ├── StudioType.php
│   │   └── UserRole.php
│   ├── Filament/           # Admin panel resources
│   │   ├── Pages/          # Custom pages
│   │   ├── Resources/      # CRUD resources
│   │   └── Widgets/        # Dashboard widgets
│   ├── Http/Controllers/   # HTTP controllers
│   ├── Livewire/           # Livewire components
│   │   └── Pos/            # POS systems
│   │       ├── TicketPos.php
│   │       └── FnbPos.php
│   ├── Models/             # Eloquent models
│   ├── Policies/           # Authorization policies
│   │   ├── Traits/
│   │   │   └── ChecksUserRole.php
│   │   ├── BookingPolicy.php
│   │   ├── CleaningTaskPolicy.php
│   │   ├── FnbOrderPolicy.php
│   │   ├── InventoryItemPolicy.php
│   │   ├── MenuItemPolicy.php
│   │   ├── ShowtimePolicy.php
│   │   ├── StudioPolicy.php
│   │   └── UserPolicy.php
│   ├── Providers/          # Service providers
│   └── Services/           # Business logic services
│       └── TmdbService.php
├── database/
│   ├── factories/          # Model factories
│   ├── migrations/         # Database migrations
│   └── seeders/            # Database seeders
├── resources/
│   ├── css/                # Stylesheets
│   ├── js/                 # JavaScript files
│   └── views/              # Blade templates
│       ├── components/     # Blade components
│       ├── layouts/        # Layout templates
│       ├── livewire/       # Livewire views
│       └── pdf/            # PDF templates
├── routes/                 # Application routes
├── docker-compose.yml      # Docker configuration
├── Dockerfile              # PHP container definition
└── nginx/                  # Nginx configuration
```

## Development Commands

```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# View logs
docker-compose logs -f

# Run artisan commands
docker exec cinema-app php artisan <command>

# Run composer commands
docker exec cinema-app composer <command>

# Run npm commands
docker exec cinema-app npm <command>

# Clear all caches
docker exec cinema-app php artisan optimize:clear

# Fresh migration with seeds
docker exec cinema-app php artisan migrate:fresh --seed
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Screenshots

### Public Website
- Home page with now playing movies
- Movie details with showtime selection
- Interactive seat selection
- User profile with booking history

### Admin Panel
- Dashboard with analytics
- Studio management
- Showtime scheduling
- Ticket POS system
- FnB POS system

### E-Ticket
- PDF ticket with QR code
- QR modal in user profile

---

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Authors

- **Cinema XXL Team**

## Acknowledgments

- [Laravel](https://laravel.com/)
- [FilamentPHP](https://filamentphp.com/)
- [Livewire](https://livewire.laravel.com/)
- [The Movie Database (TMDb)](https://www.themoviedb.org/)
- [Tailwind CSS](https://tailwindcss.com/)
