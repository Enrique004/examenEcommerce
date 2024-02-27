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

class CategoriesTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    function you_can_filter_by_subcategories() {
        $category = Category::factory()->create([
            'name' => 'Celulares y tablets',
            'slug' => Str::slug('Celulares y tablets'),
            'icon' => '<i class="fas fa-mobile-alt"></i>',
        ]);

        $category->brands()->attach(Brand::factory()->create());

        $subcategory1 = Subcategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Celulares y smartphones',
            'slug' => Str::slug('Celulares y smartphones'),
        ]);

        $subcategory2 = Subcategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Iphones',
            'slug' => Str::slug('Iphones'),
        ]);

        $product1 = Product::factory()->create([
            'subcategory_id' => $subcategory1->id,
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product1->id,
            'imageable_type' => Product::class
        ]);

        $product2 = Product::factory()->create([
            'subcategory_id' => $subcategory2->id,
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product2->id,
            'imageable_type' => Product::class
        ]);

        $this->browse(function (Browser $browser) use ($category,$subcategory1,$product1,$product2) {
            $browser->visit('/categories/' . $category->slug)
                ->screenshot('/categories/' . $category->slug)
                ->clickLink($subcategory1->name)
                ->assertSee($product1->name)
                ->assertDontSee($product2->name);
        });
    }

    /** @test */
    function you_can_filter_by_brands() {
        $category = Category::factory()->create([
            'name' => 'Celulares y tablets',
            'slug' => Str::slug('Celulares y tablets'),
            'icon' => '<i class="fas fa-mobile-alt"></i>',
        ]);

        $brand1 = Brand::factory()->create();
        $brand2 = Brand::factory()->create();

        $category->brands()->attach($brand1);
        $category->brands()->attach($brand2);

        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Celulares y smartphones',
            'slug' => Str::slug('Celulares y smartphones'),
        ]);

        $product1 = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'brand_id' => $brand1->id
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product1->id,
            'imageable_type' => Product::class
        ]);

        $product2 = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'brand_id' => $brand2->id
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product2->id,
            'imageable_type' => Product::class
        ]);

        $this->browse(function (Browser $browser) use ($category,$brand1,$product1,$product2) {
            $browser->visit('/categories/' . $category->slug)
                ->clickLink($brand1->name)
                ->assertSee($product1->name)
                ->assertDontSee($product2->name);
        });
    }
}
