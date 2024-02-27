<?php

namespace Tests\Browser;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Image;
use App\Models\Product;
use App\Models\Size;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProductsTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    function you_can_increase_the_quantity()
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

        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'quantity' => 2
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products/' . $product->slug)
                ->assertSee('1')
                ->press('+')
                ->pause(1000)
                ->assertSee('2')
                ->assertMissing('#incrementButton');
        });
    }

    /** @test */
    function you_can_decrease_the_quantity()
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

        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'quantity' => 5
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products/' . $product->slug)
                ->assertSee('1')
                ->press('+')
                ->pause(1000)
                ->assertSee('2')
                ->pause(1000)
                ->assertSee('3')
                ->press('-')
                ->pause(1000)
                ->assertSee('2')
                ->press('-')
                ->pause(1000)
                ->assertSee('1')
                ->assertMissing('#decrementButton');
        });
    }

    /** @test */
    function you_can_see_the_dropdown_menus_for_the_colors_of_a_product()
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
            'color' => 1
        ]);

        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'quantity' => 5
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);

        $color = Color::create([
            'name' => 'Azul'
        ]);

        $product->colors()->attach([
            1 => [
                'quantity' => 5
            ]
        ]);

        $this->browse(function (Browser $browser) use ($product,$color) {
            $browser->visit('/products/' . $product->slug)
                ->assertSee('Color')
                ->click('#select-color')
                ->assertSee($color->name);
        });
    }

    /** @test */
    function you_can_see_the_dropdown_menus_for_the_colors_and_sizes_of_a_product()
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
            'color' => 1,
            'size' => 1
        ]);

        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'quantity' => 5
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);

        $color = Color::create([
            'name' => 'Azul'
        ]);

        $size1 = 'Talla S';

        $product->colors()->attach([
            1 => [
                'quantity' => 5
            ]
        ]);

        $product->sizes()->create([
            'name' => $size1,
        ]);

        $sizes = Size::all();

        foreach ($sizes as $size) {
            $size->colors() ->attach([
                1 => ['quantity' => 10],
            ]);
        }

        $this->browse(function (Browser $browser) use ($product,$color,$size1) {
            $browser->visit('/products/' . $product->slug)
                ->assertSee('Color')
                ->assertSee('Talla')
                ->click('@selected-size-1')
                ->assertSee($size1)
                ->click('#select-color')
                ->assertSee($color->name);
        });
    }

    /** @test */
    function you_can_add_to_cart_the_products()
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

        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'quantity' => 5
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products/' . $product->slug)
                ->assertSee('AGREGAR AL CARRITO DE COMPRAS')
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(1000)
                ->visit('/shopping-cart')
                ->assertSee($product->name);
        });
    }

    /** @test */
    function you_can_add_to_cart_the_products_that_have_colors()
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
            'color' => 1
        ]);

        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'quantity' => 5
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);

        $color = Color::create([
            'name' => 'Azul'
        ]);

        $product->colors()->attach([
            1 => [
                'quantity' => 5
            ]
        ]);

        $this->browse(function (Browser $browser) use ($product,$color) {
            $browser->visit('/products/' . $product->slug)
                ->assertSee('AGREGAR AL CARRITO DE COMPRAS')
                ->click('@selected-color-1')
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(1000)
                ->visit('/shopping-cart')
                ->assertSee($product->name);
        });
    }

    /** @test */
    function you_can_add_to_cart_the_products_that_have_colors_and_sizes()
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
            'color' => 1,
            'size' => 1
        ]);

        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'quantity' => 5
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);

        $color = Color::create([
            'name' => 'Azul'
        ]);

        $size1 = 'Talla S';

        $product->colors()->attach([
            1 => [
                'quantity' => 5
            ]
        ]);

        $product->sizes()->create([
            'name' => $size1,
        ]);

        $sizes = Size::all();

        foreach ($sizes as $size) {
            $size->colors() ->attach([
                1 => ['quantity' => 10],
            ]);
        }

        $this->browse(function (Browser $browser) use ($product,$color,$size1) {
            $browser->visit('/products/' . $product->slug)
                ->assertSee('AGREGAR AL CARRITO DE COMPRAS')
                ->click('@selected-size-1')
                ->pause(1000)
                ->click('@selected-color-1')
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(1000)
                ->visit('/shopping-cart')
                ->assertSee($product->name);
        });
    }

    /** @test */
    function can_not_add_more_products_that_are_in_stock_to_cart()
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

        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'quantity' => 2
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products/' . $product->slug)
                ->assertSeeIn('@stock','1')
                ->press('+')
                ->pause(1000)
                ->assertSeeIn('@stock','2')
                ->assertMissing('#incrementButton')
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(1000)
                ->assertMissing('#buttonCart');
        });
    }

    /** @test */
    function the_quantity_change_when_you_add_the_product_to_cart()
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

        $product = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'quantity' => 10
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);

        $this->browse(function (Browser $browser) use ($product) {
            $browser->visit('/products/' . $product->slug)
                ->assertSeeIn('@product-stock','10')
                ->press('AGREGAR AL CARRITO DE COMPRAS')
                ->pause(1000)
                ->assertSeeIn('@product-stock','9');
        });
    }
}
