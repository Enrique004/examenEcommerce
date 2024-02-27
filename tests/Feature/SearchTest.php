<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function you_can_search_a_product()
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
            'name' => 'IPHONE 13',
            'subcategory_id' => $subcategory->id,
            'quantity' => 5
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product1->id,
            'imageable_type' => Product::class
        ]);

        $product2 = Product::factory()->create([
            'name' => 'APPLE',
            'subcategory_id' => $subcategory->id,
            'quantity' => 5
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product2->id,
            'imageable_type' => Product::class
        ]);

        $this->get('/search?name=IPH')
            ->assertSee($product1->name)
            ->assertDontSee($product2->name);

        $this->get('search?name=')
            ->assertSee($product1->name)
            ->assertSee($product2->name);
    }
}
