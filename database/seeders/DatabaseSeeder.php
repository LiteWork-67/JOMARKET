<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Role;
use App\Models\Seller;
use App\Models\SellerOffer;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);

        $adminRole = Role::where('slug', 'admin')->first();
        $supportRole = Role::where('slug', 'support')->first();
        $sellerRole = Role::where('slug', 'seller')->first();
        $customerRole = Role::where('slug', 'customer')->first();

        // 1. Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@jordanmart.com'],
            [
                'name' => 'JordanMart Administrator',
                'phone' => '+962790000001',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'locale' => 'ar',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ]
        );
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        UserProfile::firstOrCreate(['user_id' => $admin->id], [
            'bio' => 'Lead Administrator for JordanMart Marketplace Jordan',
            'preferred_currency' => 'JOD',
        ]);

        // 2. Support Agent User
        $support = User::firstOrCreate(
            ['email' => 'support@jordanmart.com'],
            [
                'name' => 'Jordan Support Team',
                'phone' => '+962790000002',
                'password' => Hash::make('password'),
                'role' => 'support',
                'status' => 'active',
                'locale' => 'ar',
                'email_verified_at' => now(),
            ]
        );
        $support->roles()->syncWithoutDetaching([$supportRole->id]);

        // 3. Demo Verified Sellers
        $sellerUser1 = User::firstOrCreate(
            ['email' => 'tech@jordanmart.com'],
            [
                'name' => 'Fadi Electronics Amman',
                'phone' => '+962791112233',
                'password' => Hash::make('password'),
                'role' => 'seller',
                'status' => 'active',
                'locale' => 'ar',
                'email_verified_at' => now(),
            ]
        );
        $sellerUser1->roles()->syncWithoutDetaching([$sellerRole->id]);

        $seller1 = Seller::firstOrCreate(
            ['user_id' => $sellerUser1->id],
            [
                'store_name_ar' => 'متجر عمّان للإلكترونيات',
                'store_name_en' => 'Amman Tech Hub',
                'slug' => 'amman-tech-hub',
                'description_ar' => 'أفضل الأجهزة الذكية وملحقات الحواسيب الأصلية في المملكة الأردنية الهاشمية مع كفالة رسمية.',
                'description_en' => 'Leading supplier of authentic smart electronics and tech gadgets in Jordan with official warranty.',
                'commission_rate' => 8.50,
                'status' => 'approved',
                'commercial_registration_no' => 'CR-1049281',
                'tax_number' => 'TAX-99882211',
                'bank_name' => 'Arab Bank - Jordan',
                'iban' => 'JO94ARAB00000012345678901234',
                'account_holder_name' => 'Amman Tech Hub Co.',
                'contact_email' => 'tech@jordanmart.com',
                'contact_phone' => '+962791112233',
                'governorate' => 'Amman',
                'city' => 'Amman',
                'address_details' => 'Wasfi Al-Tal St (Gardens), Amman',
                'rating' => 4.95,
                'reviews_count' => 84,
                'verified_at' => now(),
            ]
        );

        $sellerUser2 = User::firstOrCreate(
            ['email' => 'petra@jordanmart.com'],
            [
                'name' => 'Noor Al-Huda Lifestyle',
                'phone' => '+962792223344',
                'password' => Hash::make('password'),
                'role' => 'seller',
                'status' => 'active',
                'locale' => 'ar',
                'email_verified_at' => now(),
            ]
        );
        $sellerUser2->roles()->syncWithoutDetaching([$sellerRole->id]);

        $seller2 = Seller::firstOrCreate(
            ['user_id' => $sellerUser2->id],
            [
                'store_name_ar' => 'أزياء وجمال البتراء',
                'store_name_en' => 'Petra Lifestyle & Perfumes',
                'slug' => 'petra-lifestyle',
                'description_ar' => 'تشكيلات راقية من العطور الشرقية والغربية والأزياء العصرية لكل أفراد العائلة.',
                'description_en' => 'Premium perfumes, fragrances, and contemporary fashion for the whole family.',
                'commission_rate' => 12.00,
                'status' => 'approved',
                'commercial_registration_no' => 'CR-2059382',
                'tax_number' => 'TAX-77665544',
                'bank_name' => 'Housing Bank for Trade & Finance',
                'iban' => 'JO68HBTF00000098765432109876',
                'account_holder_name' => 'Petra Lifestyle LLC',
                'contact_email' => 'petra@jordanmart.com',
                'contact_phone' => '+962792223344',
                'governorate' => 'Amman',
                'city' => 'Amman',
                'address_details' => '7th Circle, Zahran St, Amman',
                'rating' => 4.88,
                'reviews_count' => 62,
                'verified_at' => now(),
            ]
        );

        // 4. Demo Customer User
        $customer = User::firstOrCreate(
            ['email' => 'customer@jordanmart.com'],
            [
                'name' => 'Tariq Al-Majali',
                'phone' => '+962793334455',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'status' => 'active',
                'locale' => 'ar',
                'email_verified_at' => now(),
            ]
        );
        $customer->roles()->syncWithoutDetaching([$customerRole->id]);

        UserProfile::firstOrCreate(['user_id' => $customer->id], [
            'preferred_currency' => 'JOD',
            'national_id' => '9941029482',
        ]);

        Address::firstOrCreate(
            ['user_id' => $customer->id, 'is_default' => true],
            [
                'type' => 'both',
                'contact_name' => 'Tariq Al-Majali',
                'phone' => '+962793334455',
                'governorate' => 'Amman',
                'city' => 'Amman',
                'district' => 'Sweifieh',
                'street_address' => 'Ali Nasouh Al-Taher St',
                'building_number' => '14',
                'floor' => '2',
                'apartment' => '4',
                'landmark' => 'Near Baraka Mall',
                'delivery_instructions' => 'Call upon arrival at building gate',
                'latitude' => 31.95620000,
                'longitude' => 35.86430000,
            ]
        );

        // 5. Categories
        $electronics = Category::firstOrCreate(['slug' => 'electronics'], [
            'name_ar' => 'الإلكترونيات والهواتف',
            'name_en' => 'Electronics & Mobiles',
            'description_ar' => 'أحدث الهواتف الذكية، الحواسيب المحمولة والشاشات',
            'description_en' => 'Latest smartphones, laptops, audio, and screens',
            'icon' => 'device-phone-mobile',
            'commission_rate' => 8.00,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $fashion = Category::firstOrCreate(['slug' => 'fashion'], [
            'name_ar' => 'الأزياء والملابس',
            'name_en' => 'Fashion & Apparel',
            'description_ar' => 'أرقى صيحات الموضة والأزياء الرجالية والنسائية',
            'description_en' => 'Premium trendy menswear, womenswear and accessories',
            'icon' => 'sparkles',
            'commission_rate' => 12.00,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $home = Category::firstOrCreate(['slug' => 'home-kitchen'], [
            'name_ar' => 'المنزل والمطبخ',
            'name_en' => 'Home & Kitchen',
            'description_ar' => 'أجهزة المطبخ، الديكورات، ومستلزمات المعيشة',
            'description_en' => 'Appliances, cookware, and home essentials',
            'icon' => 'home',
            'commission_rate' => 10.00,
            'is_active' => true,
            'sort_order' => 3,
        ]);

        $perfumes = Category::firstOrCreate(['slug' => 'beauty-perfumes'], [
            'name_ar' => 'العطور والعناية الشخصية',
            'name_en' => 'Perfumes & Beauty',
            'description_ar' => 'عطور أصلية فاخرة ومستحضرات عناية معتمدة',
            'description_en' => 'Authentic luxury fragrances and beauty care',
            'icon' => 'heart',
            'commission_rate' => 14.00,
            'is_active' => true,
            'sort_order' => 4,
        ]);

        // 6. Brands
        $apple = Brand::firstOrCreate(['slug' => 'apple'], [
            'name_ar' => 'أبل',
            'name_en' => 'Apple',
            'is_featured' => true,
            'is_active' => true,
        ]);

        $samsung = Brand::firstOrCreate(['slug' => 'samsung'], [
            'name_ar' => 'سامسونج',
            'name_en' => 'Samsung',
            'is_featured' => true,
            'is_active' => true,
        ]);

        $nike = Brand::firstOrCreate(['slug' => 'nike'], [
            'name_ar' => 'نايكي',
            'name_en' => 'Nike',
            'is_featured' => true,
            'is_active' => true,
        ]);

        $dior = Brand::firstOrCreate(['slug' => 'dior'], [
            'name_ar' => 'ديور',
            'name_en' => 'Dior',
            'is_featured' => true,
            'is_active' => true,
        ]);

        // 7. Demo Products & Seller Offers
        // Product 1: iPhone 16 Pro Max
        $product1 = Product::firstOrCreate(['slug' => 'apple-iphone-16-pro-max-256gb'], [
            'category_id' => $electronics->id,
            'brand_id' => $apple->id,
            'name_ar' => 'أبل آيفون 16 برو ماكس - سعة 256 جيجابايت - تيتانيوم صحراوي',
            'name_en' => 'Apple iPhone 16 Pro Max - 256GB - Desert Titanium',
            'description_ar' => 'الهاتف الرائد الأقوى من أبل مزود بشريحة A18 Pro المتطورة، كاميرا بدقة 48 ميجابكسل، وشاشة Super Retina XDR مقاس 6.9 بوصة مع دعم الذكاء الاصطناعي وكفالة أصلية داخل الأردن.',
            'description_en' => 'The ultimate iPhone powered by A18 Pro chip, 48MP fusion camera, 6.9-inch Super Retina XDR display with Apple Intelligence and Jordan warranty.',
            'short_description_ar' => 'هاتف آيفون 16 برو ماكس سعة 256 جيجا تيتانيوم، كفالة سنة أصلية.',
            'short_description_en' => 'iPhone 16 Pro Max 256GB Titanium with 1-Year Jordan warranty.',
            'main_image_url' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&auto=format&fit=crop&q=80',
            'barcode' => '195949012345',
            'sku_prefix' => 'IPH16PM',
            'status' => 'approved',
            'is_featured' => true,
            'is_active' => true,
            'views_count' => 1240,
        ]);

        ProductImage::firstOrCreate(['product_id' => $product1->id, 'image_url' => 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&auto=format&fit=crop&q=80'], [
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        SellerOffer::firstOrCreate(
            ['seller_id' => $seller1->id, 'product_id' => $product1->id],
            [
                'seller_sku' => 'ATH-IPH16PM-256-DES',
                'condition' => 'new',
                'price' => 1049.000, // 1,049.000 JOD
                'sale_price' => 999.000, // 999.000 JOD deal
                'currency' => 'JOD',
                'stock_quantity' => 25,
                'reserved_quantity' => 0,
                'min_order_quantity' => 1,
                'max_order_quantity' => 2,
                'handling_days' => 1,
                'is_active' => true,
                'is_featured' => true,
            ]
        );

        // Product 2: Samsung Galaxy S25 Ultra
        $product2 = Product::firstOrCreate(['slug' => 'samsung-galaxy-s25-ultra-512gb'], [
            'category_id' => $electronics->id,
            'brand_id' => $samsung->id,
            'name_ar' => 'سامسونج جالاكسي إس 25 ألترا - سعة 512 جيجابايت - تيتانيوم رمادي',
            'name_en' => 'Samsung Galaxy S25 Ultra - 512GB - Titanium Gray',
            'description_ar' => 'هاتف سامسونج الرائد المزود بقلم S Pen المدمج، كاميرا بدقة 200 ميجابكسل، معالج Snapdragon 8 Elite وتقنيات Galaxy AI المتطورة.',
            'description_en' => 'Ultimate Android flagship with built-in S Pen, 200MP camera, Snapdragon 8 Elite and Galaxy AI capabilities.',
            'short_description_ar' => 'سامسونج إس 25 ألترا سعة 512 جيجا مع شاشة 6.8 بوصة وقلم مدمج.',
            'short_description_en' => 'Samsung S25 Ultra 512GB Titanium with built-in S Pen.',
            'main_image_url' => 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=800&auto=format&fit=crop&q=80',
            'barcode' => '880609123456',
            'sku_prefix' => 'SAMS25U',
            'status' => 'approved',
            'is_featured' => true,
            'is_active' => true,
            'views_count' => 890,
        ]);

        ProductImage::firstOrCreate(['product_id' => $product2->id, 'image_url' => 'https://images.unsplash.com/photo-1610945415295-d9bbf067e59c?w=800&auto=format&fit=crop&q=80'], [
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        SellerOffer::firstOrCreate(
            ['seller_id' => $seller1->id, 'product_id' => $product2->id],
            [
                'seller_sku' => 'ATH-S25U-512-GRY',
                'condition' => 'new',
                'price' => 979.000,
                'sale_price' => 929.000,
                'currency' => 'JOD',
                'stock_quantity' => 18,
                'reserved_quantity' => 0,
                'min_order_quantity' => 1,
                'max_order_quantity' => 2,
                'handling_days' => 1,
                'is_active' => true,
                'is_featured' => true,
            ]
        );

        // Product 3: Sauvage Eau de Parfum
        $product3 = Product::firstOrCreate(['slug' => 'dior-sauvage-eau-de-parfum-100ml'], [
            'category_id' => $perfumes->id,
            'brand_id' => $dior->id,
            'name_ar' => 'عطر سوفاج من ديور - أو دو بارفيوم 100 مل رجالي',
            'name_en' => 'Dior Sauvage Eau de Parfum - 100ml For Men',
            'description_ar' => 'عطر رجالي شرقي منعش مستوحى من سحر الصحراء وقت الغسق، غني بمكونات البرغموت والفانيليا الحارة والباتشولي، أصلي 100%.',
            'description_en' => 'Signature masculine fragrance inspired by twilight in the desert, combining Calabrian bergamot with spicy vanilla accents.',
            'short_description_ar' => 'عطر ديور سوفاج أو دو بارفيوم 100 مل أصلي.',
            'short_description_en' => 'Original Dior Sauvage EDP 100ml Spray.',
            'main_image_url' => 'https://images.unsplash.com/photo-1523293182086-7651a899d37f?w=800&auto=format&fit=crop&q=80',
            'barcode' => '3348901234567',
            'sku_prefix' => 'DIOR-SAV',
            'status' => 'approved',
            'is_featured' => true,
            'is_active' => true,
            'views_count' => 650,
        ]);

        ProductImage::firstOrCreate(['product_id' => $product3->id, 'image_url' => 'https://images.unsplash.com/photo-1523293182086-7651a899d37f?w=800&auto=format&fit=crop&q=80'], [
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        SellerOffer::firstOrCreate(
            ['seller_id' => $seller2->id, 'product_id' => $product3->id],
            [
                'seller_sku' => 'PETRA-SAV-EDP-100',
                'condition' => 'new',
                'price' => 115.000,
                'sale_price' => 98.500, // 98.500 JOD
                'currency' => 'JOD',
                'stock_quantity' => 40,
                'reserved_quantity' => 0,
                'min_order_quantity' => 1,
                'max_order_quantity' => 5,
                'handling_days' => 1,
                'is_active' => true,
                'is_featured' => true,
            ]
        );

        // Product 4: Nike Air Max
        $product4 = Product::firstOrCreate(['slug' => 'nike-air-max-pulse-shoes'], [
            'category_id' => $fashion->id,
            'brand_id' => $nike->id,
            'name_ar' => 'حذاء رياضي نايكي إير ماكس بلس رجالي - مريح للجري والمشي',
            'name_en' => 'Nike Air Max Pulse Men Running & Lifestyle Shoes',
            'description_ar' => 'حذاء نايكي العصري بنظام توسيد هوائي متطور يوفر أقصى درجات الراحة والدعم طوال اليوم بتصميم ديناميكي جذاب.',
            'description_en' => 'Featuring point-loaded cushioning that revamps the iconic Air Max comfort for everyday athletic lifestyle.',
            'short_description_ar' => 'حذاء نايكي إير ماكس أصلي بتصميم مريح للجري والأنشطة اليومية.',
            'short_description_en' => 'Authentic Nike Air Max Pulse athletic footwear.',
            'main_image_url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&auto=format&fit=crop&q=80',
            'barcode' => '091201928374',
            'sku_prefix' => 'NKE-AMP',
            'status' => 'approved',
            'is_featured' => true,
            'is_active' => true,
            'views_count' => 520,
        ]);

        ProductImage::firstOrCreate(['product_id' => $product4->id, 'image_url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&auto=format&fit=crop&q=80'], [
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        SellerOffer::firstOrCreate(
            ['seller_id' => $seller2->id, 'product_id' => $product4->id],
            [
                'seller_sku' => 'PETRA-NKE-AMP-43',
                'condition' => 'new',
                'price' => 89.000,
                'sale_price' => 74.000,
                'currency' => 'JOD',
                'stock_quantity' => 30,
                'reserved_quantity' => 0,
                'min_order_quantity' => 1,
                'max_order_quantity' => 3,
                'handling_days' => 1,
                'is_active' => true,
                'is_featured' => false,
            ]
        );

        // 8. Core Settings for Jordan
        Setting::set('site_name_ar', 'جوردن مارت', 'general', true);
        Setting::set('site_name_en', 'JordanMart', 'general', true);
        Setting::set('default_currency', 'JOD', 'general', true);
        Setting::set('default_locale', 'ar', 'general', true);
        Setting::set('platform_default_commission', '10.00', 'finance', false);
        Setting::set('vat_rate', '0.16', 'finance', false); // 16% Jordan Sales Tax
        Setting::set('standard_shipping_rate', '3.000', 'shipping', true); // 3 JOD inside Amman
        Setting::set('free_shipping_threshold', '50.000', 'shipping', true); // Free shipping above 50 JOD
        Setting::set('cod_fee', '1.000', 'payments', true); // 1 JOD COD handling fee
    }
}
