# MyUKM Scripts Organization

This directory contains organized scripts for the MyUKM Laravel project.

## 📁 Directory Structure

```
scripts/
├── start/              # Development server scripts (if needed)
├── test/               # Testing utilities and scripts
├── utilities/          # Project maintenance utilities
└── deprecated/         # Old batch files (moved for cleanup)
```

## 🚀 Quick Start

**Primary Launch Script:**
```bash
# From project root
start.bat
```

This universal launcher provides:
- Development server with hot reload
- Production-like server
- Queue worker management
- Realtime development environment
- Full development stack
- Testing menu
- Utilities menu

## 🧪 Testing

**Run All Tests:**
```bash
scripts/test/run-all-tests.bat
```

**From Main Launcher:**
1. Run `start.bat`
2. Choose `[T] Test Menu`
3. Select test type

Available test options:
- All tests (Feature + Unit + Custom)
- Feature tests only
- Unit tests only
- Specific test files
- Tests with coverage
- Database connection test
- Broadcasting test
- Performance tests

## 🛠️ Utilities

**Available Utilities:**
- `scripts/utilities/check-status.bat` - System status check
- `scripts/utilities/check-group-urls.php` - Validate group codes
- `scripts/utilities/organize-files.php` - Project file organization

**From Main Launcher:**
1. Run `start.bat`
2. Choose `[U] Utilities Menu`
3. Select utility

Available utilities:
- Clear all caches
- Reset database
- Generate app key
- Check system status
- Fix file permissions
- Optimize application
- Check group URLs
- Organize files

## 📋 Migration from Old Scripts

All old batch files have been moved to `scripts/deprecated/` for reference.

**Old → New Mapping:**
- `start-dev-server.bat` → `start.bat` (option 1)
- `start-production-like.bat` → `start.bat` (option 2)
- `start-queue-worker.bat` → `start.bat` (option 3)
- `test-*.bat` → `start.bat` → Test Menu
- `check-*.bat` → `start.bat` → Utilities Menu

## 🎯 Benefits of New Structure

1. **Single Entry Point**: One `start.bat` for all operations
2. **Organized Structure**: Logical separation of concerns
3. **Better Maintenance**: Easier to update and maintain
4. **Cleaner Root**: Less clutter in project root
5. **Enhanced Features**: More comprehensive options and error handling

## 🔧 Customization

To add new scripts:
1. Place in appropriate subdirectory
2. Update main `start.bat` menu if needed
3. Follow naming conventions:
   - `run-*.bat` for executable scripts
   - `check-*.php` for validation utilities
   - `*.php` for Laravel-integrated scripts

## 📖 Usage Examples

**Start Development:**
```bash
start.bat
# Choose option 1 for dev server with hot reload
```

**Run Specific Tests:**
```bash
start.bat
# Choose T → 4 → enter test file path
```

**Check System Status:**
```bash
start.bat
# Choose U → 4
```

**Quick Database Reset:**
```bash
start.bat
# Choose U → 2
```
