<?php

namespace Tests\Feature;

use App\Http\Livewire\AddCartItem;
use App\Http\Livewire\AddCartItemColor;
use App\Http\Livewire\AddCartItemSize;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Image;
use App\Models\Product;
use App\Models\Size;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\CreateData;
use Tests\TestCase;

class CartTest extends TestCase
{
    use DatabaseMigrations, CreateData;

    /** @test */
    function the_cart_is_saved_when_you_log_out_with_the_three_products()
    {
        $product = $this->create(1,0,0,'A',25,5);
        $productWithColor = $this->create(1,1,0,'B',20,10);
        $productWithColorAndSize = $this->create(1,1,1,'C',30,2);
        $color = $this->createColor();
        $this->createSize($productWithColorAndSize,$color->id);

        $productWithColor->colors()->attach([
            $color->id => [
                'quantity' => 5
            ]
        ]);

        $user = User::factory()->create();

        // Compruebo un producto normal
        $this->actingAs($user);

        Livewire::test(AddCartItem::class,['product' => $product])
            ->call('addItem');

        $this->get('shopping-cart')
            ->assertSee($product->name)
            ->assertSee($product->price)
            ->assertSee($product->quantity);

        Auth::logout();

        $this->actingAs($user);

        $this->get('shopping-cart')
            ->assertSee($product->name)
            ->assertSee($product->price)
            ->assertSee($product->quantity);

        // Compruebo un producto con color
        Auth::logout();

        $this->actingAs($user);

        Livewire::test(AddCartItemColor::class,['product' => $productWithColor])
            ->call('addItem');

        $this->get('shopping-cart')
            ->assertSee($productWithColor->name)
            ->assertSee($productWithColor->price)
            ->assertSee($productWithColor->quantity);

        Auth::logout();

        $this->actingAs($user);

        $this->get('shopping-cart')
            ->assertSee($productWithColor->name)
            ->assertSee($productWithColor->price)
            ->assertSee($productWithColor->quantity);

        // Compruebo un producto con color y talla
        Auth::logout();

        $this->actingAs($user);

        Livewire::test(AddCartItemSize::class,['product' => $productWithColorAndSize])
            ->call('addItem');

        $this->get('shopping-cart')
            ->assertSee($productWithColorAndSize->name)
            ->assertSee($productWithColorAndSize->price)
            ->assertSee($productWithColorAndSize->quantity);

        Auth::logout();

        $this->actingAs($user);

        $this->get('shopping-cart')
            ->assertSee($productWithColorAndSize->name)
            ->assertSee($productWithColorAndSize->price)
            ->assertSee($productWithColorAndSize->quantity);
    }
}
