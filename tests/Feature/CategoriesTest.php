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

class CategoriesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function you_can_show_the_details_of_a_category() {
        $category = Category::factory()->create([
            'name' => 'Celulares y tablets',
            'slug' => Str::slug('Celulares y tablets'),
            'icon' => '<i class="fas fa-mobile-alt"></i>',
        ]);

        $brands = Brand::factory(5)->create();

        $category->brands()->attach($brands);

        $subcategories = Subcategory::factory(5)->create([
            'category_id' => $category->id,
            'name' => 'Celulares y smartphones',
            'slug' => Str::slug('Celulares y smartphones'),
        ]);

        foreach ($subcategories as $subcategory) {
            $product = Product::factory()->create([
                'name' => 'IPHONE 14', 'status' => 1,
                'subcategory_id' => $subcategory->id,
            ]);

            Image::factory(1)->create([
                'imageable_id' => $product->id,
                'imageable_type' => Product::class
            ]);
        }

        $response =  $this->get('/categories/' . $category->slug);

        foreach ($subcategories as $subcategory) {
            $response->assertSee($subcategory->name);
        }

        foreach ($brands as $brand) {
            $response->assertSee($brand->name);
        }

        foreach ($category->products as $product) {
            $response->assertSee($product->name);
        }
    }
}
