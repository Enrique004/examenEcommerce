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
use Tests\CreateData;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase,CreateData;

    /** @test */
    function you_can_search_a_product()
    {
        $category  = $this->createCategory();

        $subcategory = $this->createSubcategory($category->id);

        $product1 = $this->createProduct($subcategory->id,'IPHONE');
        $product2 = $this->createProduct($subcategory->id,'APPLE');


        $this->get('/search?name=IPH')
            ->assertSee($product1->name)
            ->assertDontSee($product2->name);

        $this->get('search?name=')
            ->assertSee($product1->name)
            ->assertSee($product2->name);
    }
}
