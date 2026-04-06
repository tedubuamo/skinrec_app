# SkinRec - Laravel Cloud Deployment Guide

## 📍 Apa itu Laravel Cloud?

**Laravel Cloud** adalah platform deploy khusus Laravel dari Taylor Otwell (creator Laravel). Features:
- ✅ One-click deploy dari GitHub
- ✅ Built-in database (MySQL/PostgreSQL)
- ✅ CDN untuk static files
- ✅ Zero configuration
- ✅ Support for queues, caching, etc.

---

## 🚀 Prerequisites

- [x] GitHub account dengan repo `tedubuamo/skinrec_app`
- [x] Code sudah push ke GitHub
- [ ] Laravel Cloud account (buat baru)

---

## 📋 STEP 1: Create Laravel Cloud Account

1. Go to **https://laravel.cloud**
2. Click "**Sign in with GitHub**"
3. Authorize Laravel Cloud untuk akses GitHub
4. Complete profile setup

---

## 🔗 STEP 2: Create New Project

Di Laravel Cloud dashboard:

1. Click "**Create Project**"
2. Select GitHub organization: **tedubuamo**
3. Select repository: **skinrec_app**
4. Set branch: **main** (default)
5. Click "**Create Project**"

Laravel Cloud akan:
- Clone repo dari GitHub
- Detect Laravel framework
- Create database MySQL
- Setup environment automatically

---

## ⚙️ STEP 3: Configure Environment

Laravel Cloud akan guide Anda through setup wizard:

### Database Configuration
- ✅ MySQL otomatis dibuat
- ✅ Database credentials otomatis diset di env

### Environment Variables
Pastikan ini ada di Laravel Cloud:

```
APP_ENV=production
APP_DEBUG=false
LOG_CHANNEL=stderr
DB_CONNECTION=mysql
SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

Laravel Cloud otomatis generate `APP_KEY`, jadi Anda cukup approve.

### Build Configuration
Laravel Cloud auto-detect:
```
Build command: composer install && npm install && npm run build
Start command: php artisan serve
```

---

## 🚀 STEP 4: Deploy

Setelah setting selesai:

1. Click "**Deploy**" button
2. Laravel Cloud akan:
   - Run `composer install`
   - Run `npm install && npm run build`
   - Run migrations
   - Run seeders (jika ada di Procfile atau artisan)

**Tunggu 5-10 menit untuk selesai** ⏳

---

## ✅ STEP 5: Verify Deployment

Setelah deployment selesai:

1. Laravel Cloud akan provide **Production URL**
   - Format: `https://project-name.laravel.cloud`
   - Atau custom domain Anda

2. **Test di browser:**
   ```
   https://[your-project].laravel.cloud
   ```

3. **Test form rekomendasi:**
   - Select: Face Wash
   - Select Skin Type: Oily
   - Submit
   - Should see products list ✅

---

## 🔄 Continuous Deployment

Setelah setup, setiap push ke GitHub otomatis deploy:

```bash
# Local machine
git add .
git commit -m "Fix: something"
git push origin main

# Laravel Cloud otomatis notify & deploy
# Check Laravel Cloud dashboard untuk status
```

---

## 📊 Database Management

Di Laravel Cloud Dashboard:

### View Database
1. Go to "Database" section
2. View MySQL credentials
3. Can connect via phpMyAdmin atau MySQL client

### Run Migrations
Jika butuh manual migration:
```
Laravel Cloud → SSH → Run:
php artisan migrate
php artisan db:seed --class=ProductDataSeeder
```

### Backup Database
```
Laravel Cloud → Database → Backup
Otomatis backup setiap hari
```

---

## 🔐 Security Features (Automatic)

- [x] SSL/HTTPS enabled
- [x] Environment variables encrypted
- [x] Database backup daily
- [x] DDoS protection via CDN
- [x] GitHub integration dengan status checks

---

## 💰 Pricing

**Laravel Cloud Starter Plan:**
- $9/month
- Unlimited projects
- 1 database
- 1 environment (staging + production extra)

**Recommended** untuk production SkinRec

---

## 🔗 Custom Domain Setup

1. Laravel Cloud Dashboard → Domains
2. Add custom domain: `skinrec.com` (example)
3. Update DNS records:
   ```
   CNAME: skinrec.com -> your-project.laravel.cloud
   ```
4. Laravel Cloud auto-issue SSL certificate

---

## 📞 Troubleshooting

### Build Failed
```
Check:
- Procfile valid?
- resources/css/app.css exists? ✓ (sudah dibuat)
- resources/js/app.js exists? ✓ (sudah dibuat)
```

### Database Error
```
Laravel Cloud → Database → Logs
Check migration errors
```

### Performance Issues
```
Laravel Cloud → Scaling
Increase server resources (paid feature)
Or enable queue workers
```

---

## 🎯 Common Tasks

### View Live Logs
```
Laravel Cloud → Logs tab
Real-time application output
```

### Scale Up
```
Laravel Cloud → Scaling
Increase RAM/CPU (additional cost)
```

### Custom Environment
```
Laravel Cloud → Environment
Add custom variables
Auto-reload application
```

### SSH Access
```
Laravel Cloud → SSH
Remote access untuk debugging
```

---

## ✨ Advantages vs Railway

| Feature | Laravel Cloud | Railway |
|---------|--------------|---------|
| Laravel Optimized | ✅ Yes | ✅ Yes |
| One-click Deploy | ✅ Yes | ✅ Yes |
| Database | ✅ Built-in MySQL | ⚠️ Manual |
| Cost | $9/month | Free tier (limited) |
| Support | ✅ Official Laravel support | Community |

---

## 📝 Next Steps

1. Go to **https://laravel.cloud**
2. Sign in dengan GitHub
3. Create project dari repo
4. Fill environment setup
5. Click Deploy
6. Done! 🎉

---

## 🆘 Need Help?

### Laravel Cloud Docs
- https://laravel.cloud/docs

### Laravel Community
- https://laravel.io
- Discord: Laravel

---

**Status**: Ready to deploy ke Laravel Cloud! ✅

Sudah di-push semua file yang diperlukan ke GitHub.

Sekarang tinggal buat account dan deploy! 🚀
