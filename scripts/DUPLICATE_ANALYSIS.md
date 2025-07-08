# MyUKM Project - Duplicate Analysis Report

## 🔍 DUPLICATE FOLDERS DETECTED

### 📁 Similar Function Folders:
1. **scripts/utilities/** vs **scripts/utils/**
2. **scripts/test/** vs **scripts/testing/**

### 📋 DUPLICATE FILES FOUND:

#### In scripts/ root vs subdirectories:
- `check_db.php` (scripts/ & scripts/utils/)
- `debug_routes.php` (scripts/ & scripts/utils/)
- `delete_admin_account.php` (scripts/ & scripts/utils/)
- `final_verification.php` (scripts/ & scripts/utils/ & scripts/testing/)
- `quick_db_setup.php` (scripts/ & scripts/utils/)
- `quick_setup.php` (scripts/ & scripts/utils/)
- `setup_admin.php` (scripts/ & scripts/utils/)
- `setup_admin_grup_data.php` (scripts/ & scripts/utils/)
- `create_deletion_history.php` (scripts/ & scripts/utils/)

#### In different subdirectories:
- `test_admin_grup_layout.php` (scripts/utils/ & scripts/testing/)
- `test_layout.php` (scripts/utils/ & scripts/testing/)
- `final_verification.php` (scripts/utils/ & scripts/testing/)

## 🎯 RECOMMENDED CONSOLIDATION:

### Keep: scripts/utilities/ (remove scripts/utils/)
- More descriptive name
- Already referenced in start.bat
- Contains essential utilities

### Keep: scripts/test/ (remove scripts/testing/)
- Shorter, cleaner name
- Already integrated with start.bat
- Contains consolidated test runner

### Keep: Subdirectories, Clean Root
- Move all loose files from scripts/ root into appropriate subdirectories
- Keep only README.md in scripts/ root

## 📊 FILE COUNT:
- **scripts/ root**: ~30 loose files (should be 1: README.md)
- **scripts/utilities/**: 5 files ✅
- **scripts/utils/**: 20 files ❌ (duplicate)
- **scripts/test/**: 3 files ✅
- **scripts/testing/**: 9 files ❌ (duplicate)
- **scripts/deprecated/**: 38 files ✅
