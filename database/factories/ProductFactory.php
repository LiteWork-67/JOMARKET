<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $nameEn = fake()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'name_ar' => 'منتج '.$nameEn,
            'name_en' => ucfirst($nameEn),
            'slug' => Str::slug($nameEn).'-'.fake()->unique()->numberBetween(100, 9999),
            'description_ar' => fake()->paragraph(),
            'description_en' => fake()->paragraph(),
            'short_description_ar' => fake()->sentence(),
            'short_description_en' => fake()->sentence(),
            'main_image_url' => null,
            'barcode' => fake()->ean13(),
            'sku_prefix' => strtoupper(fake()->lexify('???')),
            'status' => 'approved',
            'is_featured' => false,
            'is_active' => true,
            'views_count' => fake()->numberBetween(10, 500),
        ];
    }
}
