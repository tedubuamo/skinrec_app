# SkinRec - Railway Deployment Guide

## 📍 Prerequisites Checklist

- [x] GitHub repository dengan semua code sudah push (`tedubuamo/skinrec_app`)
- [x] Procfile sudah dibuat untuk Railway
- [x] .env.example sudah ada
- [x] Migrations dan Seeders siap
- [ ] Railway account (buat di: https://railway.app)

---

## 🚀 Step 1: Create Railway Account

1. Go to **https://railway.app**
2. Click "**Start New Project**"
3. Sign in with GitHub
4. Choose "**Deploy from GitHub repo**"

---

## 🔗 Step 2: Connect GitHub Repository

1. Select repo: **tedubuamo/skinrec_app**
2. Railway akan auto-detect sebagai **Laravel project**
3. Click "**Deploy**"

Railway akan:
- Auto-build dari Procfile
- Auto-create environment variables
- Setup database

Wait 2-3 minutes untuk build...

---

## ⚙️ Step 3: Configure Environment Variables di Railway

Setelah deploy, buka Railway Dashboard → Project Settings → **Variables**

Tambahkan variables berikut:

### Required Variables:

```
APP_KEY = base64:hDtTHOoDkLjl9fIUNijVH5wh8zokhTOETDfrrKIGEXA=
APP_ENV = production
APP_DEBUG = false
APP_URL = https://[your-railway-url].railway.app

DB_CONNECTION = sqlite
DB_DATABASE = database/database.sqlite

LOG_CHANNEL = stderr
SESSION_DRIVER = file
CACHE_DRIVER = file
QUEUE_CONNECTION = sync
```

> **KEY PENTING**: Copy `APP_KEY` dari file `.env` Anda atau generate baru dengan:
```bash
php artisan key:generate
```

---

## 🗄️ Step 4: Setup Database & Run Migrations

Di Railway Dashboard:

1. Go ke **Deploys** tab
2. Find latest deploy
3. Click **View Logs**
4. Check bahwa migrations berhasil run

Jika belum ada data produk:

### Option A: Via Railway Shell
```bash
1. Di Railway Dashboard, click "Connect" or shell icon
2. Run: php artisan migrate --seed
3. This akan seed 1,166 produk dari CSV
```

### Option B: Via GitHub (Recommended)

Update `Procfile` untuk auto-run migration:

**Current Procfile:**
```
web: vendor/bin/heroku-php-apache2 public/
```

**Change to:**
```
release: php artisan migrate --force --seed
web: vendor/bin/heroku-php-apache2 public/
```

Then push:
```bash
git add Procfile
git commit -m "Add release command for auto migration"
git push
```

Railway akan automatically redeploy dan seed database.

---

## ✅ Step 5: Verify Deployment

### Test di Browser:
```
https://[your-railway-url].railway.app
```

You should see:
- ✅ SkinRec homepage
- ✅ Recommendation form
- ✅ Can submit recommendations

### Test Recommendation:
1. Select: **Face Wash**
2. Select Skin Type: **Oily**
3. Click Submit
4. Should see products list with scores

---

## 🔍 Troubleshooting

### Problem: "Application error" saat akses

**Solution 1: Check Logs**
- Railway Dashboard → Logs
- Look untuk error messages
- Most common: missing APP_KEY

**Solution 2: Check DATABASE**
```bash
# Via Railway SSH:
php artisan migrate --force
php artisan db:seed --class=ProductDataSeeder
```

### Problem: Results tidak muncul

**Check:**
```bash
# Via Railway Shell:
php artisan tinker
>>> DB::table('product')->count()
# Should return 1166
```

If returns 0, database belum di-seed:
```bash
php artisan migrate --seed
```

### Problem: "SQLSTATE[HY000]: General error"

Solution:
```bash
php artisan migrate:fresh --seed
```

---

## 📱 Production URLs

Setelah deploy, aplikasi akan accessible di:

```
https://[your-railway-project-name].railway.app
```

Railway akan give you exact URL setelah deployment selesai.

---

## 🚀 Auto-Deploy from GitHub

Railway otomatis redeploy setiap kali Anda:

1. Push code ke `main` branch
2. Changes akan live dalam 2-3 menit

Contoh workflow:
```bash
# Make changes locally
git add .
git commit -m "Fix: something"
git push origin main

# Railway automatically deploys!
# Check Railway Dashboard untuk status
```

---

## 📊 Monitoring

Di Railway Dashboard, bisa monitor:
- **Logs**: Real-time application logs
- **Metrics**: CPU, Memory usage
- **Deployments**: Deploy history
- **Domains**: Custom domain setup (optional)

---

## 💾 Backup Database

SQLite database ada di: `database/database.sqlite`

Untuk backup sebelum membuat changes besar:

```bash
# Di local
php artisan tinker
>>> DB::connection('sqlite')->dump('database/backup.sql');

# Or manually download dari Railway storage
```

---

## 🎯 Common Tasks

### Add Custom Domain
```
Railway Dashboard → Project Settings → Domains → Add Domain
Then update DNS records at domain provider
```

### View Application Logs
```
Railway Dashboard → Logs tab
Filter by component: "web"
```

### Restart Application
```
Railway Dashboard → Deployments → Click latest → Restart
```

### Scale Resources
```
Railway Dashboard → Settings → Resources
Increase RAM/CPU jika perlu (paid feature)
```

---

## 🔐 Security Notes

- [x] APP_DEBUG = false (production)
- [x] APP_ENV = production
- [x] SQLite untuk development OK, tapi tidak scalable untuk ratusan users
- [ ] Consider PostgreSQL untuk production skala besar (Railway support)

---

## 📞 Support

### Railway Support:
- Docs: https://docs.railway.app
- Discord: https://discord.gg/railway

### SkinRec Issues:
- Check application logs
- Review TESTING_GUIDE.md untuk debugging
- Check database dengan tinker

---

## ✨ Done!

Aplikasi Anda sekarang live di Railway! 🎉

Share URL: `https://[your-railway-url].railway.app`

Setiap push ke GitHub akan otomatis deploy!
