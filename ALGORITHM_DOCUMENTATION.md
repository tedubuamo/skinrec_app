# SkinRec Recommendation Algorithm - Technical Documentation

## Data Flow Diagram

```
User Form Input
    ↓
    ├─ product (category name)
    ├─ skinType[] (array of selected types)
    ├─ skinProblem[] (array of selected problems)
    └─ effect[] (array of desired effects)
    ↓
Controller: SkincareRecomendationController@recommend
    ↓
Service: Skinrec::recommendSkincare()
    ↓
    ├─ Find id_category by category_name
    ├─ Find all matching id_skintype (where user selected types = 1)
    ├─ Query products with:
    │  ├─ id_category match
    │  ├─ id_skintype in matching types
    │  └─ Join skin_problem and notable_effect tables
    └─ Calculate scores and sort
    ↓
View: recommended_products.blade.php
    ↓
Display Results
```

## Algorithm Details

### Step 1: Input Normalization
```php
$skinTypes = ['sensitive', 'oily']  // User input (lowercase)
$skinProblems = ['jerawat', 'kulit_kusam']
$effects = ['acne_free', 'brightening']
```

### Step 2: Find Matching Categories
```php
$id_category = DB::table('category')
    ->where('category_name', 'Face Wash')
    ->value('id_category');
// Result: 'CTG01'
```

### Step 3: Find Matching Skin Types
```php
// User selected: sensitive, oily
// Query: Find all skin types where sensitive=1 OR oily=1
$matchingSkintypes = DB::table('skintype')
    ->where(function ($query) {
        $query->orWhere('sensitive', 1)
              ->orWhere('oily', 1);
    })
    ->pluck('id_skintype')
    ->toArray();
// Result: ['SKT01', 'SKT03', 'SKT07', 'SKT11'] etc.
```

### Step 4: Query Products
```php
$products = DB::table('product')
    ->join('category', ...)
    ->join('brand', ...)
    ->leftJoin('skin_problem', ...)    // LEFT JOIN for nullable
    ->leftJoin('notable_effect', ...)  // LEFT JOIN for nullable
    ->where('product.id_category', $id_category)
    ->whereIn('product.id_skintype', $matchingSkintypes)
    ->select([...all product columns...])
    ->get();
```

### Step 5: Score Calculation

For each product, we calculate:

#### Skin Problems Score
```php
$skinProblems = ['jerawat', 'kulit_kusam'];
$productProblems = [
    'kulit_kusam' => 1,      // Product has this problem
    'jerawat' => 1,          // Product has this problem
    'bekas_jerawat' => 0,    // Product doesn't have this
    ...
];

// Count matches
$totalProblems = 2;  // Total problems in product
$matchedProblems = 2;  // How many user selected
$skinProblemsScore = 2 / 2 = 1.0  // Perfect match!

// If product has 4 problems total but user only selected 2
$skinProblemsScore = 2 / 4 = 0.5
```

#### Notable Effects Score
```php
$desiredEffects = ['acne_free', 'brightening'];
$productEffects = [
    'acne_free' => 1,       // Product has this effect
    'brightening' => 1,     // Product has this effect
    'moisturizing' => 1,    // Product has this but not selected
    'soothing' => 0,
    ...
];

// Count matches
$totalEffects = 3;  // Total effects in product
$matchedEffects = 2;  // How many user selected
$effectsScore = 2 / 3 = 0.67

// But can also be user selected all effects
$effectsScore = 2 / 2 = 1.0
```

#### Total Score Calculation
```php
// Case 1: User provided both skin problems and effects
if (!empty($skinProblems) && !empty($effects)) {
    $totalScore = ($skinProblemsScore + $effectsScore) / 2;
    // Average of both scores
}

// Case 2: User only provided skin problems
elseif (!empty($skinProblems)) {
    $totalScore = $skinProblemsScore;
}

// Case 3: User only provided effects
elseif (!empty($effects)) {
    $totalScore = $effectsScore;
}

// Case 4: User only selected category and skin type
else {
    $totalScore = 0.5;  // All matching products get same score
}
```

### Step 6: Sort and Return
```php
// Sort by total_score descending
$recommended = $products
    ->sortByDesc('total_score')
    ->map(function($p) {
        return [
            'product_name' => $p->product_name,
            'brand' => $p->brand_name,
            'price' => $p->price,
            'description' => $p->description,
            'picture_src' => $p->picture_src,
            'total_score' => round($p->total_score, 2),
            'rating' => $p->rating,
            'category_name' => $p->category_name,
            'id_product' => $p->id_product,
        ];
    });
```

## Example Walkthrough

### Input:
```
Product Type: "Face Wash"
Skin Type: ["oily"]
Skin Problems: ["jerawat", "komedo"]
Effects: ["acne_free", "oil_control"]
```

### Processing:

1. **Category**: "Face Wash" → id_category = "CTG01"

2. **Skin Types**: oily=1 → Find all skin types with oily=1
   - Result: id_skintype ∈ ['SKT02', 'SKT05', 'SKT09', ...]

3. **Products Query**:
   ```sql
   SELECT product.*, category.*, brand.*, skin_problem.*, notable_effect.*
   FROM product
   JOIN category ON product.id_category = category.id_category
   JOIN brand ON product.id_brand = brand.id_brand
   LEFT JOIN skin_problem ON product.id_problem = skin_problem.id_problem
   LEFT JOIN notable_effect ON product.id_notable = notable_effect.id_notable
   WHERE product.id_category = 'CTG01'
   AND product.id_skintype IN ('SKT02', 'SKT05', 'SKT09', ...)
   ```

4. **Example Product Results**:

   **Product A**: "ACWELL Bubble Free PH Balancing Cleanser"
   - Skin Problems: jerawat=1, komedo=1, pori_pori_besar=1 (total=3)
   - User wants: jerawat=1, komedo=1 (matched=2)
   - Skin Problems Score: 2/3 = 0.67
   
   - Notable Effects: acne_free=1, pore_care=1, brightening=1 (total=3)
   - User wants: acne_free=1, oil_control=0 (matched=1)
   - Effects Score: 1/3 = 0.33
   
   - Total Score: (0.67 + 0.33) / 2 = 0.50

   **Product B**: "AVOSKIN YOUR SKIN BAE SERIES Toner Salicylic Acid"
   - Skin Problems: jerawat=1, komedo=1, kemerahan=1 (total=3)
   - User wants: jerawat=1, komedo=1 (matched=2)
   - Skin Problems Score: 2/3 = 0.67
   
   - Notable Effects: acne_free=1, oil_control=1, pore_care=1 (total=3)
   - User wants: acne_free=1, oil_control=1 (matched=2)
   - Effects Score: 2/3 = 0.67
   
   - Total Score: (0.67 + 0.67) / 2 = 0.67 ⭐️ WINNER

5. **Results**: Sorted by total_score descending
   - Product B: 0.67
   - Product A: 0.50
   - Product C: 0.40
   - ...

## Column Mapping

### Skin Problems
```php
'kulit_kusam'              // Dull skin
'jerawat'                  // Acne
'bekas_jerawat'            // Acne scars
'pori_pori_besar'          // Large pores
'flek_hitam'               // Dark spots
'garis_halus_dan_kerutan'  // Fine lines and wrinkles
'komedo'                   // Comedones
'warna_kulit_tidak_merata' // Uneven skin tone
'kemerahan'                // Redness
'kulit_kendur'             // Sagging skin
```

### Notable Effects
```php
'acne_free'      // Prevents acne
'soothing'       // Calming
'brightening'    // Whitening/lightening
'moisturizing'   // Moisturizing
'hydrating'      // Hydrating
'pore_care'      // Pore minimizing
'anti_aging'     // Anti-aging
'balancing'      // pH balancing
'uv_protection'  // Sun protection
'skin_barrier'   // Strengthens barrier
'refreshing'     // Refreshing
'oil_control'    // Oil control
'no_whitecast'   // No white cast
'black_spot'     // Dark spot removal
```

## Error Handling

### Empty Inputs
```php
if (empty($productType)) {
    return collect([]);  // Return empty collection
}

if (empty($matchingSkintypes)) {
    return collect([]);  // No matching skin types
}
```

### NULL Values
Using `leftJoin` instead of `join` to handle products without skin_problem or notable_effect assigned.

## Performance Notes

- Database: SQLite (suitable for this size: 1166 products)
- Query: Single query with joins + PHP collection mapping
- Caching: None currently (can be added later)
- Index: Consider adding on id_category, id_skintype if queries slow

## Future Improvements

1. Add weighted scoring (skin problems 60%, effects 40%)
2. Implement fuzzy matching for typos
3. Add user ratings/feedback for better recommendations
4. Cache recommendations for popular combinations
5. Add "similar products" feature
6. Implement machine learning for personalized recommendations
