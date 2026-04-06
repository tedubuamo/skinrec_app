<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Read CSV file
        $csvFile = database_path('data_skinrec_fix.csv');
        
        if (!file_exists($csvFile)) {
            echo "CSV file not found!\n";
            return;
        }

        $handle = fopen($csvFile, 'r');
        $header = fgetcsv($handle, 0, ';');
        
        // Cache for storing already created records
        $brands = [];
        $categories = [];
        $skintypes = [];
        $skinProblems = [];
        $notableEffects = [];

        $rowNum = 0;
        while ($row = fgetcsv($handle, 0, ';')) {
            if (++$rowNum === 1) continue; // Skip header
            
            try {
                // Parse CSV row
                $product_href = $row[0] ?? null;
                $product_name = $row[1] ?? null;
                $product_type = $row[2] ?? null;
                $brand_name = trim($row[3] ?? '');
                $notable_effects_str = trim($row[4] ?? '');
                $skintype_str = trim($row[5] ?? '');
                $price = floatval($row[6] ?? 0);
                $description = $row[7] ?? null;
                $picture_src = $row[8] ?? null;
                $labels = $row[9] ?? null;
                
                // Boolean columns for skin types
                $sensitive = intval($row[10] ?? 0);
                $combination = intval($row[11] ?? 0);
                $oily = intval($row[12] ?? 0);
                $dry = intval($row[13] ?? 0);
                $normal = intval($row[14] ?? 0);
                $rating = floatval($row[15] ?? 0);

                if (!$product_name || !$product_type) {
                    continue;
                }

                // Get or create category
                if (!isset($categories[$product_type])) {
                    $id_category = 'CTG' . str_pad(count($categories) + 1, 3, '0', STR_PAD_LEFT);
                    
                    DB::table('category')->updateOrInsert(
                        ['id_category' => $id_category],
                        [
                            'category_name' => $product_type,
                            'description' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                    $categories[$product_type] = $id_category;
                }
                $id_category = $categories[$product_type];

                // Get or create brand
                if (!isset($brands[$brand_name])) {
                    $id_brand = 'BRD' . str_pad(count($brands) + 1, 3, '0', STR_PAD_LEFT);
                    
                    DB::table('brand')->updateOrInsert(
                        ['id_brand' => $id_brand],
                        [
                            'brand_name' => $brand_name ?? 'Unknown',
                            'description' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                    $brands[$brand_name] = $id_brand;
                }
                $id_brand = $brands[$brand_name];

                // Get or create skin type combination
                $skintype_key = "$sensitive|$combination|$oily|$dry|$normal";
                if (!isset($skintypes[$skintype_key])) {
                    $id_skintype = 'SKT' . str_pad(count($skintypes) + 1, 3, '0', STR_PAD_LEFT);
                    
                    DB::table('skintype')->updateOrInsert(
                        ['id_skintype' => $id_skintype],
                        [
                            'skintype_name' => $this->generateSkintypeName($sensitive, $combination, $oily, $dry, $normal),
                            'sensitive' => $sensitive,
                            'combination' => $combination,
                            'oily' => $oily,
                            'dry' => $dry,
                            'normal' => $normal,
                            'description' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                    $skintypes[$skintype_key] = $id_skintype;
                }
                $id_skintype = $skintypes[$skintype_key];

                // Create or get skin problems (using labels or empty if none)
                $id_problem = $this->getOrCreateSkinProblem($skinProblems);

                // Get or create notable effects
                $id_notable = $this->getOrCreateNotableEffect($notable_effects_str, $notableEffects);

                // Create product
                $id_product = 'PR' . str_pad($rowNum, 5, '0', STR_PAD_LEFT);
                
                DB::table('product')->updateOrInsert(
                    ['id_product' => $id_product],
                    [
                        'product_name' => $product_name,
                        'id_brand' => $id_brand,
                        'id_category' => $id_category,
                        'id_skintype' => $id_skintype,
                        'id_problem' => $id_problem,
                        'id_notable' => $id_notable,
                        'description' => $description,
                        'price' => $price,
                        'picture_src' => $picture_src,
                        'rating' => $rating,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

            } catch (\Exception $e) {
                echo "Error on row $rowNum: " . $e->getMessage() . "\n";
                continue;
            }
        }

        fclose($handle);
        echo "CSV data seeding completed!\n";
    }

    /**
     * Generate skin type name from boolean flags
     */
    private function generateSkintypeName($sensitive, $combination, $oily, $dry, $normal)
    {
        $types = [];
        if ($sensitive) $types[] = 'Sensitive';
        if ($combination) $types[] = 'Combination';
        if ($oily) $types[] = 'Oily';
        if ($dry) $types[] = 'Dry';
        if ($normal) $types[] = 'Normal';
        
        return implode(', ', $types) ?: 'All Types';
    }

    /**
     * Get or create skin problem record
     */
    private function getOrCreateSkinProblem(&$skinProblems)
    {
        // For now, create a default "No specific problem" entry
        $key = 'default';
        if (!isset($skinProblems[$key])) {
            $id_problem = 'SKP' . str_pad(count($skinProblems) + 1, 3, '0', STR_PAD_LEFT);
            
            DB::table('skin_problem')->updateOrInsert(
                ['id_problem' => $id_problem],
                [
                    'problem_name' => 'General',
                    'kulit_kusam' => 0,
                    'jerawat' => 0,
                    'bekas_jerawat' => 0,
                    'pori_pori_besar' => 0,
                    'flek_hitam' => 0,
                    'garis_halus_dan_kerutan' => 0,
                    'komedo' => 0,
                    'warna_kulit_tidak_merata' => 0,
                    'kemerahan' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            $skinProblems[$key] = $id_problem;
        }
        
        return $skinProblems[$key];
    }

    /**
     * Get or create notable effects record from comma-separated string
     */
    private function getOrCreateNotableEffect($effects_str, &$notableEffects)
    {
        // Map user-provided effects to database columns
        $effectsMap = [
            'acne-free' => 'acne_free',
            'acne_free' => 'acne_free',
            'soothing' => 'soothing',
            'brightening' => 'brightening',
            'moisturizing' => 'moisturizing',
            'hydrating' => 'hydrating',
            'pore-care' => 'pore_care',
            'pore_care' => 'pore_care',
            'anti-aging' => 'anti_aging',
            'anti_aging' => 'anti_aging',
            'balancing' => 'balancing',
            'uv-protection' => 'uv_protection',
            'uv_protection' => 'uv_protection',
            'skin-barrier' => 'skin_barrier',
            'skin_barrier' => 'skin_barrier',
            'refreshing' => 'refreshing',
            'oil-control' => 'oil_control',
            'oil_control' => 'oil_control',
            'no-whitecast' => 'no_whitecast',
            'no_whitecast' => 'no_whitecast',
            'black-spot' => 'black_spot',
            'black_spot' => 'black_spot',
        ];

        // Create key from effects string
        $effects_normalized = strtolower(str_replace(' ', '-', trim($effects_str)));
        
        if (!isset($notableEffects[$effects_normalized])) {
            $id_notable = 'NTE' . str_pad(count($notableEffects) + 1, 3, '0', STR_PAD_LEFT);
            
            // Initialize all effect columns to 0
            $effectData = [
                'effect_name' => $effects_str ?: 'General',
                'acne_free' => 0,
                'soothing' => 0,
                'brightening' => 0,
                'moisturizing' => 0,
                'hydrating' => 0,
                'pore_care' => 0,
                'anti_aging' => 0,
                'balancing' => 0,
                'uv_protection' => 0,
                'skin_barrier' => 0,
                'refreshing' => 0,
                'oil_control' => 0,
                'no_whitecast' => 0,
                'black_spot' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Parse and set the effects that exist
            $effectsList = array_map('trim', explode(',', $effects_str));
            foreach ($effectsList as $effect) {
                $effectLower = strtolower(str_replace(' ', '-', $effect));
                if (isset($effectsMap[$effectLower])) {
                    $effectData[$effectsMap[$effectLower]] = 1;
                }
            }

            DB::table('notable_effect')->updateOrInsert(
                ['id_notable' => $id_notable],
                $effectData
            );
            $notableEffects[$effects_normalized] = $id_notable;
        }
        
        return $notableEffects[$effects_normalized];
    }
}
