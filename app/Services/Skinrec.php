<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class Skinrec
{
    public function recommendSkincare($productType, $skinTypes, $skinProblems, $notableEffects)
    {
        // Handle empty inputs
        if (empty($productType)) {
            return collect([]);
        }

        // Define all skin types
        $allskinTypes = ['sensitive', 'combination', 'oily', 'dry', 'normal'];
        $i = 0;
        $NskinTypes = [];
        
        // Create a binary array indicating the user's skin types
        // Map user input to database format (lowercase)
        $skinTypesLower = array_map('strtolower', $skinTypes);
        
        foreach ($allskinTypes as $AskinType) {
            $NskinTypes[$i] = in_array($AskinType, $skinTypesLower) ? 1 : 0;
            $i++;
        }
        
        // Get id_category from the category table
        $id_category = DB::table('category')
            ->where('category_name', $productType)
            ->value('id_category');
        
        if (!$id_category) {
            return collect([]);
        }

        // Get all skin type ids that match at least one of the user's selected skin types
        $matchingSkintypes = DB::table('skintype')
            ->where(function ($query) use ($NskinTypes) {
                if ($NskinTypes[0]) $query->orWhere('sensitive', 1);
                if ($NskinTypes[1]) $query->orWhere('combination', 1);
                if ($NskinTypes[2]) $query->orWhere('oily', 1);
                if ($NskinTypes[3]) $query->orWhere('dry', 1);
                if ($NskinTypes[4]) $query->orWhere('normal', 1);
            })
            ->pluck('id_skintype')
            ->toArray();

        // If no matching skin types found, return empty
        if (empty($matchingSkintypes)) {
            return collect([]);
        }

        // Define columns for skin problems and notable effects
        $skinProblemsColumns = [
            'kulit_kusam', 'jerawat', 'bekas_jerawat', 'pori_pori_besar', 
            'flek_hitam', 'garis_halus_dan_kerutan', 'komedo', 'warna_kulit_tidak_merata', 
            'kemerahan'
        ];
        
        $notableEffectsColumns = [
            'acne_free', 'soothing', 'brightening', 'moisturizing', 
            'hydrating', 'pore_care', 'anti_aging', 'balancing', 
            'uv_protection', 'skin_barrier', 'refreshing', 'oil_control', 
            'no_whitecast', 'black_spot'
        ];

        // Normalize skin problems input (lowercase with underscores)
        $skinProblemsLower = array_map(function($p) {
            return strtolower(str_replace('-', '_', $p));
        }, $skinProblems);

        // Normalize notable effects input (lowercase with underscores)
        $notableEffectsLower = array_map(function($e) {
            return strtolower(str_replace('-', '_', $e));
        }, $notableEffects);

        // Filter products by category and skin type
        $filteredProducts = DB::table('product')
            ->join('category', 'product.id_category', '=', 'category.id_category')
            ->join('brand', 'product.id_brand', '=', 'brand.id_brand')
            ->leftJoin('skin_problem', 'product.id_problem', '=', 'skin_problem.id_problem')
            ->leftJoin('notable_effect', 'product.id_notable', '=', 'notable_effect.id_notable')
            ->where('product.id_category', $id_category)
            ->whereIn('product.id_skintype', $matchingSkintypes)
            ->select([
                'product.id_product',
                'product.product_name',
                'product.price',
                'product.description',
                'product.picture_src',
                'product.rating',
                'product.id_category',
                'product.id_brand',
                'product.id_skintype',
                'product.id_problem',
                'product.id_notable',
                'category.category_name',
                'brand.brand_name',
                'skin_problem.kulit_kusam',
                'skin_problem.jerawat',
                'skin_problem.bekas_jerawat',
                'skin_problem.pori_pori_besar',
                'skin_problem.flek_hitam',
                'skin_problem.garis_halus_dan_kerutan',
                'skin_problem.komedo',
                'skin_problem.warna_kulit_tidak_merata',
                'skin_problem.kemerahan',
                'notable_effect.acne_free',
                'notable_effect.soothing',
                'notable_effect.brightening',
                'notable_effect.moisturizing',
                'notable_effect.hydrating',
                'notable_effect.pore_care',
                'notable_effect.anti_aging',
                'notable_effect.balancing',
                'notable_effect.uv_protection',
                'notable_effect.skin_barrier',
                'notable_effect.refreshing',
                'notable_effect.oil_control',
                'notable_effect.no_whitecast',
                'notable_effect.black_spot',
            ])
            ->get();

        // Calculate match scores
        $filteredProducts = $filteredProducts->map(function ($product) use (
            $skinProblemsLower, $notableEffectsLower,
            $skinProblemsColumns, $notableEffectsColumns
        ) {
            $skinProblemsScore = 0;
            $notableEffectsScore = 0;

            // Calculate skin problems match score
            if (!empty($skinProblemsLower)) {
                $totalProblems = 0;
                foreach ($skinProblemsColumns as $column) {
                    if ($product->$column) {
                        $totalProblems++;
                        if (in_array($column, $skinProblemsLower)) {
                            $skinProblemsScore++;
                        }
                    }
                }
                // Normalize to 0-1 scale
                $skinProblemsScore = $totalProblems > 0 ? $skinProblemsScore / $totalProblems : 0;
            }

            // Calculate notable effects match score
            if (!empty($notableEffectsLower)) {
                $totalEffects = 0;
                foreach ($notableEffectsColumns as $column) {
                    if ($product->$column) {
                        $totalEffects++;
                        if (in_array($column, $notableEffectsLower)) {
                            $notableEffectsScore++;
                        }
                    }
                }
                // Normalize to 0-1 scale
                $notableEffectsScore = $totalEffects > 0 ? $notableEffectsScore / $totalEffects : 0;
            }

            // Calculate total score
            // If user provided both skin problems and effects, average them
            // If user provided only one, use that score
            if (!empty($skinProblemsLower) && !empty($notableEffectsLower)) {
                $product->total_score = ($skinProblemsScore + $notableEffectsScore) / 2;
            } elseif (!empty($skinProblemsLower)) {
                $product->total_score = $skinProblemsScore;
            } elseif (!empty($notableEffectsLower)) {
                $product->total_score = $notableEffectsScore;
            } else {
                // If user didn't specify preferred problems or effects, all matching products get same score
                $product->total_score = 0.5;
            }

            return $product;
        });

        // Filter products with at least some score and sort by total score
        $recommendedProducts = $filteredProducts
            ->sortByDesc('total_score')
            ->values();

        // Return formatted results
        return $recommendedProducts->map(function ($product) {
            return [
                'product_name' => $product->product_name,
                'brand' => $product->brand_name,
                'price' => $product->price,
                'description' => $product->description,
                'picture_src' => $product->picture_src,
                'total_score' => round($product->total_score, 2),
                'rating' => $product->rating,
                'category_name' => $product->category_name,
                'id_product' => $product->id_product,
            ];
        });
    }
}
