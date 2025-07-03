# Dokumentasi Sinkronisasi Status Online Anggota UKM

## Overview
Fitur ini memungkinkan sistem chat untuk menampilkan status online yang tersinkronisasi hanya untuk anggota yang tergabung dalam UKM/grup yang sama dan sedang aktif (last_seen_at dalam 5 menit terakhir).

## Komponen Yang Diimplementasikan

### 1. Backend - Model User (app/Models/User.php)
#### Method yang ditambahkan:
- `isOnline()`: Mengecek apakah user online (last_seen_at < 5 menit)
- `getOnlineMembersInGroup($groupId)`: Mendapatkan anggota online di grup tertentu
- `getOnlineCountInGroup($groupId)`: Menghitung jumlah anggota online di grup
- `getOnlineStatusInGroup($groupId)`: Mendapatkan status online user di grup tertentu

### 2. Backend - ChatController (app/Http/Controllers/ChatController.php)
#### Method yang ditambahkan:
- `getOnlineMembers(Request $request)`: API endpoint untuk mendapatkan daftar anggota online
- `updateOnlineStatus(Request $request)`: API endpoint untuk update status online dan broadcast

### 3. Event Broadcasting (app/Events/UserOnlineStatusChanged.php)
- Event baru untuk broadcast perubahan status online ke channel grup
- Broadcast ke channel `group.{groupCode}` untuk anggota grup tertentu

### 4. Frontend - Chat Interface (resources/views/chat.blade.php)
#### Fitur yang ditambahkan:
- Polling otomatis status online setiap 30 detik
- Update tampilan jumlah anggota online dan total anggota
- Daftar nama anggota yang sedang online
- Real-time update melalui Pusher/Echo event

### 5. Routes (routes/web.php)
#### Endpoint baru:
- `GET /chat/online-members`: Mendapatkan daftar anggota online
- `POST /chat/update-online-status`: Update status online user

### 6. Broadcasting Channels (routes/channels.php)
#### Channel authorization:
- `group.{groupCode}`: Channel untuk broadcast status online ke anggota grup

## Cara Kerja Sistem

### 1. Update Status Online
- Middleware `UpdateLastSeen` otomatis update `last_seen_at` setiap request
- Frontend melakukan polling update status setiap 30 detik via `/chat/update-online-status`
- User dianggap online jika `last_seen_at` kurang dari 5 menit yang lalu

### 2. Sinkronisasi Real-time
- Ketika status online berubah, event `UserOnlineStatusChanged` di-broadcast
- Event diterima oleh semua anggota grup melalui channel `group.{groupCode}`
- Frontend otomatis update tampilan tanpa refresh

### 3. Filter Anggota
- Hanya menampilkan anggota yang:
  - Tergabung dalam grup yang sama (relasi `group_user`)
  - Status online aktif (last_seen_at < 5 menit)

## Tampilan Frontend

### Header Chat
```
UKM Programming Club
● 3 online  👥 15
🟢 Online: John, Jane, Alice
```

### Console Log
```javascript
Anggota online di grup: 3/15
Daftar anggota online: John, Jane, Alice
User online status changed: {online_members: [...], total_members: 15}
```

## API Endpoints

### GET /chat/online-members
**Parameters:**
- `group_id`: ID grup yang ingin dicek

**Response:**
```json
{
  "status": "success",
  "online_members": [
    {"id": 1, "name": "John", "last_seen_at": "2025-01-03 10:30:00"},
    {"id": 2, "name": "Jane", "last_seen_at": "2025-01-03 10:29:00"}
  ],
  "total_members": 15
}
```

### POST /chat/update-online-status
**Parameters:**
- `group_id`: ID grup

**Response:**
```json
{
  "status": "success",
  "message": "Online status updated",
  "is_online": true
}
```

## Event Broadcasting

### UserOnlineStatusChanged
**Channel:** `group.{groupCode}`
**Event:** `user-online-status-changed`
**Data:**
```json
{
  "user_id": 1,
  "user_name": "John",
  "is_online": true,
  "group_code": "ABC123",
  "online_members": [...],
  "total_members": 15
}
```

## Testing

### Manual Testing
1. Buka halaman chat UKM di browser
2. Buka tab/window baru dengan user lain di grup yang sama
3. Perhatikan update status online real-time
4. Check console browser untuk log activity

### Script Testing
```bash
# Windows
scripts\test-online-status.bat

# Linux/Mac
scripts/test-online-status.sh
```

## Konfigurasi

### Environment Variables
```env
PUSHER_APP_ID=your_pusher_app_id
PUSHER_APP_KEY=your_pusher_app_key
PUSHER_APP_SECRET=your_pusher_app_secret
PUSHER_APP_CLUSTER=your_pusher_cluster
```

### Database
Pastikan tabel `users` memiliki field:
- `last_seen_at` (timestamp, nullable)

### Middleware
Pastikan `UpdateLastSeen` middleware aktif di route grup yang dilindungi.

## Troubleshooting

### Status Online Tidak Update
1. Periksa middleware `UpdateLastSeen` aktif
2. Pastikan field `last_seen_at` di database
3. Check console browser untuk error AJAX

### Real-time Tidak Bekerja
1. Periksa konfigurasi Pusher
2. Pastikan channel authorization benar
3. Check network tab browser untuk WebSocket connection

### Anggota Tidak Muncul
1. Pastikan user tergabung dalam grup (tabel `group_user`)
2. Check permission channel broadcasting
3. Pastikan `last_seen_at` ter-update

## Performance Considerations

### Optimasi Query
- Index pada `last_seen_at` dan `group_id`
- Limit hasil query anggota online
- Cache hasil untuk grup besar

### Polling Frequency
- Default: 30 detik untuk update status
- Bisa disesuaikan berdasarkan kebutuhan
- Pertimbangkan beban server untuk banyak user

### Broadcasting
- Gunakan queue untuk event broadcasting
- Batasi ukuran data yang di-broadcast
- Monitor Pusher usage dan limits
