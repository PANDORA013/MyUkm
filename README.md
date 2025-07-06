# MyUKM - University Student Organization Management System

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About MyUKM

MyUKM is a comprehensive University Student Organization (UKM) management system built with Laravel. It provides tools for managing student organizations, real-time chat systems, admin panels, and user management with modern web technologies.

### Features

- 🏛️ **Organization Management** - Complete UKM administration
- 💬 **Real-time Chat System** - Live messaging with queue-optimized performance
- ⚡ **Queue-powered Broadcasting** - Asynchronous message processing for better responsiveness
- 👥 **User Management** - Role-based access control
- 📊 **Admin Dashboard** - Comprehensive admin panel
- 🔐 **Authentication** - Secure login and registration
- 📱 **Responsive Design** - Mobile-friendly interface
- 🌐 **Browser Compatibility** - IE 10+ support with modern fallbacks
- 🔒 **Security** - Enhanced security headers and protection

## 🚀 Quick Start - One-Click Launch

### 🌟 For First Time Setup (Recommended)
```bash
# Complete setup and launch (runs everything automatically)
launch-myukm.bat
```
**What it does:**
- ✅ Install all dependencies (Composer + NPM)
- ✅ Setup environment and generate keys  
- ✅ Run database migrations and seeds
- ✅ Start queue worker for real-time features
- ✅ Launch Laravel server
- ✅ **Open browser to http://localhost:8000 automatically**

### ⚡ For Daily Development (Super Fast)
```bash
# Quick launch (for daily use after first setup)
instant-launch.bat
```
**What it does:**
- ⚡ Quick environment check
- ⚡ Start queue worker for real-time features
- ⚡ Launch Laravel server
- ⚡ **Open browser to http://localhost:8000 instantly**

### 🚀 NEW: Real-time Features Ready!
```bash
# Universal launcher with real-time options
start.bat
```
**Real-time Options:**
- **[7] Laravel + Queue Worker (Chat Ready) ⭐ RECOMMENDED**
- **[8] Complete Realtime Stack (All Services)**
- **[9] Demo Real-time Features** - Guided demo

### 🖥️ Create Desktop Shortcuts (One-time Setup)
```bash
# Create clickable desktop shortcuts
create-shortcuts.bat
```

**Creates shortcuts for:**
- 🌟 **Launch MyUKM** - Complete setup and launch
- ⚡ **MyUKM Instant Launch** - Quick daily launch  
- 📋 **MyUKM Server Menu** - Advanced options
- 🧪 **Test MyUKM** - Testing interface

> **💡 Pro Tip:** Run `create-shortcuts.bat` once, then use the shortcuts in the `shortcuts/` folder for easy access!

### 🎯 Recommended Workflow
1. **First time:** Double-click `launch-myukm.bat` → Complete setup → http://localhost:8000 opens
2. **Daily use:** Double-click `instant-launch.bat` → Quick start → http://localhost:8000 opens
3. **Easy access:** Use shortcuts in `shortcuts/` folder

## ⚡ Real-time Performance Features

MyUKM now includes optimized real-time features powered by Laravel Queue Workers:

### Queue-Optimized Real-time Features
- 🚀 **Asynchronous Chat Broadcasting** - Messages are processed in background for instant response
- 👤 **Background Online Status Updates** - User presence handled via queue jobs
- 📊 **Performance Monitoring** - Built-in queue performance tracking
- 🔄 **Auto-retry Mechanism** - Failed broadcasts are automatically retried
- 📈 **Scalable Architecture** - Handle multiple concurrent users efficiently

### Real-time Development Mode
```bash
# Start with queue worker for optimal real-time performance:
start-realtime-dev.bat
```

This will start:
- Laravel development server (http://localhost:8000)
- Queue worker for background job processing
- Real-time feature monitoring

### Queue Performance Testing
```bash
# Test queue performance:
php scripts/test-realtime-performance.php
```

## 🚀 Quick Start

### ⚡ One-Click Launch (Fastest Way)

For instant access to localhost:8000 without manual steps:

#### Option 1: Complete Setup + Launch
```bash
# Double-click or run - Does everything automatically:
launch-myukm.bat
```
**What it does:**
- ✅ Installs all dependencies (Composer + NPM)
- ✅ Sets up environment (.env)
- ✅ Runs database migrations and seeds
- ✅ Starts queue worker for real-time features
- ✅ Launches server at http://localhost:8000
- ✅ Auto-opens browser

#### Option 2: Instant Launch (Minimal Setup)
```bash
# Double-click or run - Super fast launch:
instant-launch.bat
```
**What it does:**
- ⚡ Quick environment setup
- ⚡ Starts queue worker (background)
- ⚡ Launches server immediately
- ⚡ Auto-opens browser to localhost:8000

### Automated Startup (Alternative Options)

We also provide several other automated startup scripts:

#### Option 3: Quick Start (Original)
```bash
# Double-click or run:
quick-start.bat
```

#### Option 4: Server Menu (All Options)
```bash
# Double-click or run:
server-menu.bat
```

#### Option 5: Real-time Development
```bash
# Double-click or run:
start-realtime-dev.bat
```

#### Option 4: Production-like Environment
```bash
# Double-click or run:
start-production-like.bat
```

### Create Desktop Shortcuts
```bash
# Run once to create desktop shortcuts:
create-shortcuts.bat
```

### Manual Installation

If you prefer manual setup:

1. **Clone the repository**
   ```bash
   git clone https://github.com/PANDORA013/MyUkm.git
   cd MyUkm
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Start development server**
   ```bash
   php artisan serve
   ```

## 📁 Clean Project Structure

MyUKM now features a well-organized, clean directory structure:

### 🏗️ Root Directory (Essential Files Only)
```
MyUkm/
├── � Core Laravel Files          # .env, artisan, composer.json, etc.
├── 🎬 Startup Scripts            # All *.bat files for easy development
├── 📂 app/                       # Laravel application code
├── 📂 resources/                 # Views, CSS, JS
├── 📂 public/                    # Public web assets
├── 📂 routes/                    # Route definitions
└── 📂 config/                    # Configuration files
```

### 📚 Organized Documentation
```
📂 docs/                          # 📖 All documentation centralized
├── 📄 QUEUE_SUCCESS_SUMMARY.md   # Queue implementation results
├── 📄 REFACTORING_SUMMARY.md     # Code refactoring documentation  
├── 📄 CLEAN_STRUCTURE.md         # Project organization guide
├── 📄 PROJECT_STRUCTURE.md       # Detailed structure documentation
└── 📄 Various implementation docs # Feature-specific documentation
```

### 🔧 Utility Scripts
```
📂 scripts/                       # 🛠️ Setup and utility scripts
├── 📄 Database management scripts # DB setup, checks, migrations
├── 📄 Testing scripts            # Performance and functionality tests
├── � Queue monitoring tools     # Real-time queue performance
└── 📄 Development utilities      # Various dev tools
```

### 🧪 Testing Environment  
```
📂 testing/                       # 🧪 Test files isolated
├── � Chat system tests         # Real-time feature testing
├── � Admin functionality tests # Admin panel testing  
├── 📄 Layout and UI tests       # Frontend testing
└── � Integration tests         # Full system testing
```

## 🛠️ Available Scripts

| Script | Description | Usage |
|--------|-------------|-------|
| `launch-myukm.bat` | 🌟 **Complete one-click setup + launch** | **First time setup** |
| `instant-launch.bat` | ⚡ **Super fast launch to localhost:8000** | **Daily development** |
| `create-shortcuts.bat` | 🖥️ Create desktop shortcuts | Run once for easy access |
| `test-launcher.bat` | 🧪 Testing interface with menu | Testing and debugging |
| `server-menu.bat` | 📋 Interactive menu with all options | Advanced server options |
| `start-realtime-dev.bat` | 🚀 Real-time dev with queue worker | Real-time features focus |

### 🎯 Primary Scripts (Most Important)
- **`launch-myukm.bat`** - Your go-to for first time setup
- **`instant-launch.bat`** - Your daily quick launcher  
- **`create-shortcuts.bat`** - Create shortcuts for even easier access

### 🎯 Recommended Workflow for Different Scenarios

#### � First Time Setup:
```bash
# Complete setup with all dependencies:
launch-myukm.bat
```
**Result:** Browser opens to http://localhost:8000 with full setup complete

#### ⚡ Daily Development:
```bash
# Quick launch for existing setup:
instant-launch.bat
```
**Result:** Browser opens to http://localhost:8000 within seconds

#### 🖥️ Easy Access Setup:
```bash
# Create desktop shortcuts (one-time):
create-shortcuts.bat
```
**Result:** Shortcuts created in `shortcuts/` folder for easy double-click access

#### 🧪 Testing Real-time Features:
```bash
# Start with queue worker:
start-realtime-dev.bat

# Test performance:
php scripts/test-realtime-performance.php
```

## 🌐 Application URLs

Once started, access the application at:

- **Homepage**: http://localhost:8000/
- **Login**: http://localhost:8000/login
- **Register**: http://localhost:8000/register
- **Dashboard**: http://localhost:8000/dashboard
- **Chat System**: http://localhost:8000/chat
- **Admin Panel**: http://localhost:8000/admin

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
