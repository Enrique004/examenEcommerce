<?php

namespace Tests\Feature;

use App\Http\Livewire\AddCartItem;
use App\Http\Livewire\CreateOrder;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class OrdersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function you_see_the_form_data_dropdown()
    {
        $category = Category::factory()->create([
            'name' => 'Celulares y tablets',
            'slug' => Str::slug('Celulares y tablets'),
            'icon' => '<i class="fas fa-mobile-alt"></i>',
        ]);

        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Celulares y smartphones',
            'slug' => Str::slug('Celulares y smartphones'),
        ]);

        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(CreateOrder::class)
            ->set('envio_type', 2)
            ->assertSee('Referencia');
    }

    /** @test */
    function when_you_create_a_order_the_cart_id_deleted()
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

        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(AddCartItem::class,['product' => $product])
            ->call('addItem');

        $this->get('shopping-cart')
            ->assertSee($product->name);

        Livewire::test(CreateOrder::class)
            ->set('contact','A')
            ->set('phone','123456')
            ->set('envio_type', 1)
            ->call('create_order')
            ->assertRedirect('orders/'. $user->orders()->first()->id . '/payment');

        $this->assertDatabaseHas('orders',[
            'id' => 1
        ]);

        $this->get('shopping-cart')
            ->assertDontSee($product->name);
    }
}
