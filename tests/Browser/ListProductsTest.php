<?php

namespace Tests\Browser;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ListProductsTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function you_can_show_the_differents_products_of_a_category()
    {
        $category = Category::factory()->create([
            'name' => 'Celulares y tablets',
            'slug' => Str::slug('Celulares y tablets'),
            'icon' => '<i class="fas fa-mobile-alt"></i>',
        ]);

        $category->brands()->attach(Brand::factory()->create());

        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Celulares y smartphones',
            'slug' => Str::slug('Celulares y smartphones'),
        ]);

        $product1 = Product::factory()->create([
            'name' => 'IPHONE 15',
            'subcategory_id' => $subcategory->id,
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product1->id,
            'imageable_type' => Product::class
        ]);

        $product2 = Product::factory()->create([
            'name' => 'IPHONE 14',
            'subcategory_id' => $subcategory->id,
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product2->id,
            'imageable_type' => Product::class
        ]);

        $product3 = Product::factory()->create([
            'name' => 'IPHONE 13',
            'subcategory_id' => $subcategory->id,
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product3->id,
            'imageable_type' => Product::class
        ]);

        $product4 = Product::factory()->create([
            'name' => 'IPHONE 12',
            'subcategory_id' => $subcategory->id,
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product4->id,
            'imageable_type' => Product::class
        ]);

        $product5 = Product::factory()->create([
            'name' => 'IPHONE 11',
            'subcategory_id' => $subcategory->id,
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product5->id,
            'imageable_type' => Product::class
        ]);

        $this->browse(function (Browser $browser) use ($product1,$product2,$product3,$product4,$product5) {
            $browser->visit('/')
                ->assertSee($product1->name)
                ->assertSee($product2->name)
                ->assertSee($product3->name)
                ->assertSee($product4->name)
                ->assertSee($product5->name);
        });
    }

    /** @test */
    public function you_can_show_the_published_products_of_a_category()
    {
        $category = Category::factory()->create([
            'name' => 'Celulares y tablets',
            'slug' => Str::slug('Celulares y tablets'),
            'icon' => '<i class="fas fa-mobile-alt"></i>',
        ]);

        $category->brands()->attach(Brand::factory()->create());

        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Celulares y smartphones',
            'slug' => Str::slug('Celulares y smartphones'),
        ]);

        $product1 = Product::factory()->create([
            'name' => 'IPHONE 14',
            'status' => 1,
            'subcategory_id' => $subcategory->id,
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product1->id,
            'imageable_type' => Product::class
        ]);

        $product2 = Product::factory()->create([
            'name' => 'IPHONE 15',
            'status' => 2,
            'subcategory_id' => $subcategory->id,
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product2->id,
            'imageable_type' => Product::class
        ]);

        $this->browse(function (Browser $browser) use ($product1,$product2) {
            $browser->visit('/')
                ->assertSee($product2->name)
                ->assertDontSee($product1->name);
        });
    }
}
