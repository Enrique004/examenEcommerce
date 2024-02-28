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
        $product1 = $this->create(1,0,0,'IPHONE',);
        $product2 = $this->create(1,0,0,'APPLE');


        $this->get('/search?name=IPH')
            ->assertSee($product1->name)
            ->assertDontSee($product2->name);

        $this->get('search?name=')
            ->assertSee($product1->name)
            ->assertSee($product2->name);
    }
}
