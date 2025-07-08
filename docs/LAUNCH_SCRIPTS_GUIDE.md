# MyUKM Launch Scripts Documentation

## 🚀 Main Launch Scripts

### 1. `launch-myukm.bat` - Complete Setup & Launch
**Use this for first time setup or complete environment reset**

Features:
- ✅ Installs all dependencies (Composer + NPM)
- ✅ Creates .env file from template if missing
- ✅ Generates application key
- ✅ Runs database migrations and seeds
- ✅ Clears and optimizes application caches
- ✅ Starts queue worker in background
- ✅ Launches Laravel development server
- ✅ Opens browser to http://localhost:8000 automatically

**Perfect for:**
- First time project setup
- After pulling major updates
- When dependencies have changed
- Environment reset

### 2. `instant-launch.bat` - Quick Daily Launch
**Use this for daily development after initial setup**

Features:
- ⚡ Quick environment validation
- ⚡ Clears application caches
- ⚡ Starts queue worker in background
- ⚡ Launches Laravel development server
- ⚡ Opens browser automatically

**Perfect for:**
- Daily development work
- Quick testing
- When you just want to start coding

## 🖥️ Desktop Shortcuts

### `create-shortcuts.bat`
Creates desktop shortcuts for easy access:

- **Launch MyUKM** → `launch-myukm.bat`
- **MyUKM Instant Launch** → `instant-launch.bat`
- **MyUKM Server Menu** → `server-menu.bat`
- **MyUKM Quick Start** → `quick-start.bat`

## 🧪 Testing & Utilities

### `test-launcher.bat`
Interactive menu for testing all launch options:
- Test complete launch process
- Test instant launch
- Test queue worker only
- Test environment setup
- Create shortcuts
- View all application URLs

## 📋 Legacy Scripts (Still Available)

- `quick-start.bat` - Original quick start script
- `server-menu.bat` - Interactive server menu
- `start-full-dev.bat` - Full development environment
- `start-realtime-dev.bat` - Real-time development mode
- `start-dev-server.bat` - Basic server start
- `start-production-like.bat` - Production-like environment

## 🌐 Application URLs

After launching, access these URLs:

### Main Application
- Homepage: http://localhost:8000/
- Login: http://localhost:8000/login
- Register: http://localhost:8000/register
- Dashboard: http://localhost:8000/dashboard

### Features
- Chat: http://localhost:8000/chat
- Groups: http://localhost:8000/groups
- Profile: http://localhost:8000/profile

### Admin Panel
- Admin Dashboard: http://localhost:8000/admin
- User Management: http://localhost:8000/admin/users
- Group Management: http://localhost:8000/admin/groups

## 💡 Recommended Workflow

1. **First Time:** Run `launch-myukm.bat` or use "Launch MyUKM" desktop shortcut
2. **Daily Use:** Run `instant-launch.bat` or use "MyUKM Instant Launch" desktop shortcut
3. **Testing:** Use `test-launcher.bat` for comprehensive testing
4. **Shortcuts:** Run `create-shortcuts.bat` once for desktop shortcuts

## 🔧 Troubleshooting

### If scripts don't work:
1. Ensure you're in the project root directory
2. Check that Composer and Node.js are installed
3. Verify .env file exists (first script will create it)
4. Make sure ports 8000 is available

### Queue Worker Issues:
- Queue worker runs in background automatically
- Check Task Manager for "Queue Worker" processes
- Restart if needed by running scripts again

### Database Issues:
- Scripts automatically run migrations
- Database file: `database/database.sqlite`
- Reset with `php artisan migrate:fresh --seed`

## 📊 Performance Notes

- Queue worker improves real-time chat performance
- Background job processing prevents UI blocking
- Automatic cache clearing ensures fresh starts
- Optimized for development workflow efficiency
