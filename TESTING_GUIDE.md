# Testing Guide - SkinRec Application

## Quick Start Testing

### 1. Start Development Server
```bash
cd d:\PROJECT\skinrec_app
php artisan serve
```

Expected output:
```
INFO  Server running on [http://127.0.0.1:8000].

Press Ctrl+C to quit.
```

Then visit: **http://localhost:8000**

### 2. Test the Recommendation Form

#### Test Case 1: Basic Recommendation
1. Navigate to "Recommendation" section
2. Select Product: **Face Wash**
3. Select Skin Type: **Oily**
4. Click Submit

Expected:
- Should show multiple Face Wash products filtered for Oily skin
- Products should be sorted by match score
- Results should display: Product name, Brand, Price, Rating, Description

#### Test Case 2: With Skin Problems
1. Product: **Moisturizer**
2. Skin Type: **Dry**
3. Skin Problem: **Kulit Kusam** ✓
4. Click Submit

Expected:
- Only Moisturizers for Dry skin with dull skin concerns
- Higher scores for products targeting kulit_kusam

#### Test Case 3: With Effects
1. Product: **Toner**
2. Skin Type: **Combination** ✓ and **Oily** ✓
3. Desired Effects: **Brightening** ✓ and **Oil-Control** ✓
4. Click Submit

Expected:
- Toners for combination/oily skin
- High scores for products with both brightening and oil-control effects

#### Test Case 4: Full Selection
1. Product: **Serum**
2. Skin Type: **Sensitive** ✓
3. Skin Problem: **Kemerahan** ✓
4. Effects: **Soothing** ✓ and **Skin-Barrier** ✓
5. Click Submit

Expected:
- Best results for soothing serums that calm sensitive, reddened skin
- Strong match scores for products with all requirements

#### Test Case 5: Multiple Problems
1. Product: **Sunscreen**
2. Skin Type: **Normal** ✓
3. Skin Problems: **Flek Hitam** ✓, **Kemerahan** ✓, **Kulit Kusam** ✓
4. Click Submit

Expected:
- Sunscreens protecting and treating multiple skin concerns
- Scoring based on how many selected problems product addresses

## Debugging Checklist

### Browser Console (F12)
- [ ] No JavaScript errors
- [ ] Network requests showing 200 status
- [ ] POST /recommend-products is being called

### Backend Logs
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log
```

Look for:
- [ ] No SQL errors
- [ ] No QueryException
- [ ] Service is returning data

### Database Verification
```bash
php artisan tinker --execute "
echo 'Total Products: ' . DB::table('product')->count() . \"\n\";
echo 'Sample Product: ' . json_encode(DB::table('product')->first(), JSON_PRETTY_PRINT) . \"\n\";
"
```

Expected output:
```json
{
  "id_product": "PR00001",
  "product_name": "ACWELL Bubble Free PH Balancing Cleanser",
  "id_brand": "BRD001",
  "id_category": "CTG01",
  "id_skintype": "SKT01",
  "id_problem": "SKP01",
  "id_notable": "NTE01",
  "price": 209000,
  "picture_src": "https://...",
  "rating": 5,
  ...
}
```

## Troubleshooting

### Problem: "No response" Error

**Check 1: Database has data**
```bash
php artisan tinker
>>> DB::table('product')->count()
// Should return 1166, not 0
```

**Check 2: Service is working**
```bash
php artisan tinker
>>> app('App\Services\Skinrec')->recommendSkincare('Face Wash', ['oily'], [], [])
// Should return collection with products
```

**Check 3: Category exists**
```bash
php artisan tinker
>>> DB::table('category')->where('category_name', 'Face Wash')->first()
// Should return category ID
```

### Problem: Empty Results

**Possible Causes:**
1. Wrong category name (check spelling)
2. No products for that skin type
3. Category name doesn't match database

**Debug:**
```bash
php artisan tinker
>>> DB::table('category')->pluck('category_name')
// Check available categories
>>> DB::table('skintype')->where('oily', 1)->count()
// Check if oily skin type exists
```

### Problem: Wrong Scoring

**Check Service logic:**
```bash
php artisan tinker
>>> $service = app('App\Services\Skinrec');
>>> $result = $service->recommendSkincare('Face Wash', ['oily'], ['jerawat'], ['acne_free']);
>>> collect($result)->first()
// Check total_score value - should be 0-1 range
```

### Problem: Form Not Submitting

**Check 1: Form validation**
- All fields are sent correctly? (Check Network tab)
- CSRF token present?

**Check 2: Route exists**
```bash
php artisan route:list | grep recommend
```

Should show: `POST /recommend-products` route

### Problem: Database Not Found

**Solution: Recreate database**
```bash
php artisan migrate:fresh --seed
```

Or use setup script:
```bash
./setup.bat  # Windows
./setup.sh   # Linux/Mac
```

## Performance Testing

### Load Test Sample
```bash
# Test with large number of results
php artisan tinker
>>> time(function() {
    return app('App\Services\Skinrec')->recommendSkincare('Face Wash', ['oily'], ['jerawat'], ['brightening']);
});
```

Expected: < 500ms response time

## Data Validation

### Check Data Integrity
```bash
php artisan tinker

# Check category
DB::table('category')->get()

# Check brand
DB::table('brand')->first()

# Check product with relationships
DB::table('product')->with('brand', 'category', 'skinType')->first()

# Check skin problem
DB::table('skin_problem')->first()

# Check notable effect
DB::table('notable_effect')->first()
```

## API Testing (Optional)

If you want to test the API directly:

### Using cURL
```bash
curl -X POST http://localhost:8000/recommend-products \
  -H "Content-Type: application/x-www-form-urlencoded" \
  -H "_token: YOUR_CSRF_TOKEN" \
  -d "product=Face Wash&skinType[]=oily&skinProblem[]=jerawat&effect[]=acne_free"
```

### Using Postman
1. Method: POST
2. URL: http://localhost:8000/recommend-products
3. Headers:
   - Content-Type: application/x-www-form-urlencoded
4. Body (form-data):
   - product: Face Wash
   - skinType[]: oily
   - skinProblem[]: jerawat
   - effect[]: acne_free

## Success Indicators

✓ When working correctly, you should see:
- [ ] Form submits without errors
- [ ] Page redirects to recommendations view
- [ ] Multiple products displayed (usually 20-100 results)
- [ ] Products sorted by score (highest first)
- [ ] Products show name, brand, price, rating
- [ ] Product images load correctly
- [ ] No console errors

## File Locations for Quick Reference

| Item | Location |
|------|----------|
| Form | `resources/views/index.blade.php` (Recommendation section) |
| Results View | `resources/views/recommended_products.blade.php` |
| Service | `app/Services/Skinrec.php` |
| Controller | `app/Http/Controllers/SkincareRecomendationController.php` |
| Database | `database/database.sqlite` |
| Models | `app/Models/` |
| Seeder | `database/seeders/ProductDataSeeder.php` |
| Logs | `storage/logs/laravel.log` |

## Contact Support

If issues persist:
1. Check `FIXES_SUMMARY.md` for changes made
2. Review `ALGORITHM_DOCUMENTATION.md` for how system works
3. Check Laravel logs: `storage/logs/laravel.log`
4. Verify database: run tinker commands above
