<?php

namespace Tests\Feature;

use App\Http\Livewire\AddCartItem;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Order;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function you_can_show_the_links_of_login_if_you_are_not_logged_in()
    {
        $category = Category::factory()->create([
            'name' => 'Celulares y tablets',
            'slug' => Str::slug('Celulares y tablets'),
            'icon' => '<i class="fas fa-mobile-alt"></i>'
        ]);

        Subcategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Celulares y smartphones',
            'slug' => Str::slug('Celulares y smartphones'),
        ]);

        $this->assertGuest(); // Simula que el user no esta autenticado

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Iniciar sesión')
            ->assertSee('Registrarse');
    }

    /** @test */
    public function you_can_show_the_config_of_your_perfil_if_you_are_logged_in()
    {
        $category = Category::factory()->create([
            'name' => 'Celulares y tablets',
            'slug' => Str::slug('Celulares y tablets'),
            'icon' => '<i class="fas fa-mobile-alt"></i>'
        ]);

        Subcategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Celulares y smartphones',
            'slug' => Str::slug('Celulares y smartphones'),
        ]);

        $user = User::factory()->create();

        $this->actingAs($user); // Autentica al usuario

        $response = $this->get('/');

        $response->assertDontSee('Iniciar sesión')
            ->assertDontSee('Registrarse');

        $response->assertSee('Administrar cuenta')
            ->assertSee('Perfil')
            ->assertSee('Finalizar sesión');
    }

    /** @test */
    function you_can_create_a_order_if_you_are_logged_in()
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

        $this->get('/orders/create')
            ->assertOk();
    }

    /** @test */
    function you_can_not_create_a_order_if_you_are_not_logged_in()
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

        $this->get('/orders/create')
            ->assertStatus(302);
    }


    /** @test */
    function the_cart_is_saved_when_you_log_out()
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

        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(AddCartItem::class,['product' => $product])
            ->call('addItem');

        $this->get('shopping-cart')
            ->assertSee($product->name);

        Auth::logout();

        $this->actingAs($user);

        $this->get('shopping-cart')
            ->assertSee($product->name);
    }

    /** @test */
    function you_can_not_show_yours_orders_if_you_are_not_logged_in()
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

        $this->get('orders')
            ->assertStatus(302);
    }

    /** @test */
    function you_can_not_show_the_summary_of_a_order_if_you_are_not_logged_in()
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

        $order = Order::create([
            'phone' => '1234',
            'contact' => 'eefa',
            'shipping_cost' => 0,
            'total' => 5,
            'content' => json_encode([
                'product' => 'Producto 1',
                'quantity' => 2,
                'price' => 25.00,
                'description' => 'Descripción del producto 1'
            ]),
            'user_id' => $user->id,
            'status' => 1,
            'envio_type' => 1
        ]);

        $this->get('orders/' . $order->id)
            ->assertStatus(302);
    }

    /** @test */
    function you_can_not_show_the_view_of_payment_a_order_if_you_are_not_logged_in()
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

        $order = Order::create([
            'phone' => '1234',
            'contact' => 'eefa',
            'shipping_cost' => 0,
            'total' => 5,
            'content' => json_encode([
                'product' => 'Producto 1',
                'quantity' => 2,
                'price' => 25.00,
                'description' => 'Descripción del producto 1'
            ]),
            'user_id' => $user->id,
            'status' => 1,
            'envio_type' => 1
        ]);

        $this->get('orders/' . $order->id . '/payment')
            ->assertStatus(302);
    }

    /** @test */
    function the_vision_policy_works_correctly()
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

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $order1 = Order::create([
            'phone' => '1234',
            'contact' => 'eefa',
            'shipping_cost' => 0,
            'total' => 5,
            'content' => json_encode([
                'product' => 'Producto 1',
                'quantity' => 2,
                'price' => 25.00,
                'description' => 'Descripción del producto 1'
            ]),
            'user_id' => $user1->id,
            'status' => 1,
            'envio_type' => 1
        ]);

        $order2 = Order::create([
            'phone' => '5678',
            'contact' => 'john',
            'shipping_cost' => 0,
            'total' => 10,
            'content' => json_encode([
                'product' => 'Producto 2',
                'quantity' => 1,
                'price' => 10.00,
                'description' => 'Descripción del producto 2'
            ]),
            'user_id' => $user2->id,
            'status' => 1,
            'envio_type' => 1
        ]);

        $this->actingAs($user1)
            ->get('orders/' . $order2->id . '/payment')
            ->assertStatus(403);
    }
}
