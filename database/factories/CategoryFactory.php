<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $nameEn = fake()->unique()->words(2, true);

        return [
            'parent_id' => null,
            'name_ar' => 'تصنيف '.$nameEn,
            'name_en' => ucfirst($nameEn),
            'slug' => Str::slug($nameEn).'-'.fake()->unique()->numberBetween(100, 9999),
            'description_ar' => fake()->sentence(),
            'description_en' => fake()->sentence(),
            'image_url' => null,
            'icon' => 'tag',
            'commission_rate' => 10.00,
            'is_active' => true,
            'sort_order' => 1,
        ];
    }
}
