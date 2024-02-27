<?php

namespace Tests\Feature;

use App\Http\Livewire\AddCartItem;
use App\Http\Livewire\Admin\CreateProduct;
use App\Http\Livewire\Admin\EditProduct;
use App\Http\Livewire\Admin\ShowProducts;
use App\Http\Livewire\CreateOrder;
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
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function a_products_details_page_loads()
    {
        $category = Category::factory()->create([
            'name' => 'Celulares y tablets',
            'slug' => Str::slug('Celulares y tablets'),
            'icon' => '<i class="fas fa-mobile-alt"></i>',
        ]);

        $brand = Brand::factory()->create();

        $category->brands()->attach($brand);

        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Celulares y smartphones',
            'slug' => Str::slug('Celulares y smartphones'),
        ]);

        $product = Product::factory()->create([
            'name' => 'IPHONE 14', 'status' => 1,
            'subcategory_id' => $subcategory->id,
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);

        $this->get('/products/' . $product->slug)
            ->assertOk();
    }

    /** @test */
    function you_can_show_the_details_of_a_product()
    {
        $category = Category::factory()->create([
            'name' => 'Celulares y tablets',
            'slug' => Str::slug('Celulares y tablets'),
            'icon' => '<i class="fas fa-mobile-alt"></i>',
        ]);

        $brand = Brand::factory()->create();

        $category->brands()->attach($brand);

        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Celulares y smartphones',
            'slug' => Str::slug('Celulares y smartphones'),
        ]);

        $product = Product::factory()->create([
            'name' => 'IPHONE 14', 'status' => 1,
            'subcategory_id' => $subcategory->id,
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);

        $this->get('/products/' . $product->slug)
            ->assertSee($product->name)
            ->assertSee($product->description)
            ->assertSee($product->price)
            ->assertSee($product->quantity)
            ->assertSee($product->images()->first()->url)
            ->assertSee('Agregar al carrito de compras')
            ->assertSee('+')
            ->assertSee('-');
    }

    /** @test */
    function shows_the_quantity_of_a_product()
    {
        $category = Category::factory()->create([
            'name' => 'Celulares y tablets',
            'slug' => Str::slug('Celulares y tablets'),
            'icon' => '<i class="fas fa-mobile-alt"></i>',
        ]);

        $brand = Brand::factory()->create();

        $category->brands()->attach($brand);

        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Celulares y smartphones',
            'slug' => Str::slug('Celulares y smartphones'),
        ]);

        $product = Product::factory()->create([
            'name' => 'IPHONE 14', 'status' => 1,
            'subcategory_id' => $subcategory->id,
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);

        $response = $this->get('/products/' . $product->slug);

        $response->assertSee($product->quantity);
    }

    /** @test */
    function shows_the_quantity_of_a_product_that_has_color()
    {
        $category = Category::factory()->create([
            'name' => 'Celulares y tablets',
            'slug' => Str::slug('Celulares y tablets'),
            'icon' => '<i class="fas fa-mobile-alt"></i>',
        ]);

        $brand = Brand::factory()->create();

        $category->brands()->attach($brand);

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

        Color::create([
            'name' => 'Azul'
        ]);

        $product->colors()->attach([
            1 => [
                'quantity' => 5
            ]
        ]);

        $response = $this->get('/products/' . $product->slug);

        $response->assertSee($product->quantity);
    }

    /** @test */
    function shows_the_quantity_of_a_product_that_has_color_and_size()
    {
        $category = Category::factory()->create([
            'name' => 'Celulares y tablets',
            'slug' => Str::slug('Celulares y tablets'),
            'icon' => '<i class="fas fa-mobile-alt"></i>',
        ]);

        $brand = Brand::factory()->create();

        $category->brands()->attach($brand);

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
            $color->id => [
                'quantity' => 5
            ]
        ]);

        $size1 = 'Talla S';

        $product->sizes()->create([
            'name' => $size1,
        ]);

        $sizes = Size::all();

        foreach ($sizes as $size) {
            $size->colors() ->attach([
                $color->id => [
                    'quantity' => 10
                ],
            ]);
        }
        $response = $this->get('/products/' . $product->slug);

        $response->assertSee($product->quantity);
    }

    /** @test */
    function the_quantity_of_a_product_change_in_the_database_when_you_create_a_order()
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

        $this->assertDatabaseHas('products',[
            'quantity' => 1
        ]);
    }

    /** @test */
    function you_can_create_a_product()
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

        Role::create(['name' => 'admin']);

        $user = User::factory()->create()
            ->assignRole('admin');

        $this->actingAs($user)
            ->get('admin/products/create')
            ->assertOk();

        Livewire::test(CreateProduct::class)
            ->set('name', $product->name)
            ->set('slug', $product->slug)
            ->set('description', $product->description)
            ->set('price', $product->price)
            ->set('subcategory_id', $product->subcategory_id)
            ->set('brand_id', $product->brand_id)
            ->call('save');

        $this->assertDatabaseHas('products', [
            'id' => 1
        ]);
    }

    /** @test */
    function the_name_is_required_when_you_create_a_product()
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

        Role::create(['name' => 'admin']);

        $user = User::factory()->create()
            ->assignRole('admin');

        $this->actingAs($user)
            ->get('admin/products/create')
            ->assertOk();

        Livewire::test(CreateProduct::class)
            ->set('slug', $product->slug)
            ->set('description', $product->description)
            ->set('price', $product->price)
            ->set('subcategory_id', $product->subcategory_id)
            ->set('brand_id', $product->brand_id)
            ->call('save')
            ->assertHasErrors('name');
    }

    /** @test */
    function the_slug_is_required_when_you_create_a_product()
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

        Role::create(['name' => 'admin']);

        $user = User::factory()->create()
            ->assignRole('admin');

        $this->actingAs($user)
            ->get('admin/products/create')
            ->assertOk();

        Livewire::test(CreateProduct::class)
            ->set('name', $product->name)
            ->set('description', $product->description)
            ->set('price', $product->price)
            ->set('subcategory_id', $product->subcategory_id)
            ->set('brand_id', $product->brand_id)
            ->call('save')
            ->assertHasErrors('slug');
    }

    /** @test */
    function the_description_is_required_when_you_create_a_product()
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

        Role::create(['name' => 'admin']);

        $user = User::factory()->create()
            ->assignRole('admin');

        $this->actingAs($user)
            ->get('admin/products/create')
            ->assertOk();

        Livewire::test(CreateProduct::class)
            ->set('name', $product->name)
            ->set('slug', $product->slug)
            ->set('price', $product->price)
            ->set('subcategory_id', $product->subcategory_id)
            ->set('brand_id', $product->brand_id)
            ->call('save')
            ->assertHasErrors('description');
    }

    /** @test */
    function the_brand_id_is_required_when_you_create_a_product()
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

        Role::create(['name' => 'admin']);

        $user = User::factory()->create()
            ->assignRole('admin');

        $this->actingAs($user)
            ->get('admin/products/create')
            ->assertOk();

        Livewire::test(CreateProduct::class)
            ->set('name', $product->name)
            ->set('slug', $product->slug)
            ->set('description', $product->description)
            ->set('price', $product->price)
            ->set('subcategory_id', $product->subcategory_id)
            ->call('save')
            ->assertHasErrors('brand_id');
    }

    /** @test */
    function the_price_is_required_when_you_create_a_product()
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

        Role::create(['name' => 'admin']);

        $user = User::factory()->create()
            ->assignRole('admin');

        $this->actingAs($user)
            ->get('admin/products/create')
            ->assertOk();

        Livewire::test(CreateProduct::class)
            ->set('name', $product->name)
            ->set('slug', $product->slug)
            ->set('description', $product->description)
            ->set('brand_id', $product->brand_id)
            ->set('subcategory_id', $product->subcategory_id)
            ->call('save')
            ->assertHasErrors('price');
    }

    /** @test */
    function the_subcategory_id_is_required_when_you_create_a_product()
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

        Role::create(['name' => 'admin']);

        $user = User::factory()->create()
            ->assignRole('admin');

        $this->actingAs($user)
            ->get('admin/products/create')
            ->assertOk();

        Livewire::test(CreateProduct::class)
            ->set('name', $product->name)
            ->set('slug', $product->slug)
            ->set('description', $product->description)
            ->set('price', $product->price)
            ->set('brand_id', $product->brand_id)
            ->call('save')
            ->assertHasErrors('subcategory_id');
    }

    /** @test */
    function you_can_edit_a_product()
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

        Role::create(['name' => 'admin']);

        $user = User::factory()->create()
            ->assignRole('admin');

        $this->actingAs($user)
            ->get('admin/products/' . $product->slug . '/edit')
            ->assertOk();

        Livewire::test(EditProduct::class,['product' => $product])
            ->set('product.name','HOLA')
            ->call('save');

        $this->assertDatabaseHas('products' ,[
            'name' => 'HOLA'
        ]);
    }

    /** @test */
    function the_name_is_required_when_you_edit_a_product()
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

        Role::create(['name' => 'admin']);

        $user = User::factory()->create()
            ->assignRole('admin');

        $this->actingAs($user)
            ->get('admin/products/' . $product->slug . '/edit')
            ->assertOk();

        Livewire::test(EditProduct::class,['product' => $product])
            ->set('product.name','')
            ->call('save')
            ->assertHasErrors('product.name');
    }

    /** @test */
    function the_slug_required_when_you_edit_a_product()
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

        Role::create(['name' => 'admin']);

        $user = User::factory()->create()
            ->assignRole('admin');

        $this->actingAs($user)
            ->get('admin/products/' . $product->slug . '/edit')
            ->assertOk();

        Livewire::test(EditProduct::class,['product' => $product])
            ->set('product.slug','')
            ->call('save')
            ->assertHasErrors('product.slug');
    }

    /** @test */
    function the_description_required_when_you_edit_a_product()
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

        Role::create(['name' => 'admin']);

        $user = User::factory()->create()
            ->assignRole('admin');

        $this->actingAs($user)
            ->get('admin/products/' . $product->slug . '/edit')
            ->assertOk();

        Livewire::test(EditProduct::class,['product' => $product])
            ->set('product.description','')
            ->call('save')
            ->assertHasErrors('product.description');
    }

    /** @test */
    function the_brand_id_required_when_you_edit_a_product()
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

        Role::create(['name' => 'admin']);

        $user = User::factory()->create()
            ->assignRole('admin');

        $this->actingAs($user)
            ->get('admin/products/' . $product->slug . '/edit')
            ->assertOk();

        Livewire::test(EditProduct::class,['product' => $product])
            ->set('product.brand_id','')
            ->call('save')
            ->assertHasErrors('product.brand_id');
    }

    /** @test */
    function the_price_required_when_you_edit_a_product()
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

        Role::create(['name' => 'admin']);

        $user = User::factory()->create()
            ->assignRole('admin');

        $this->actingAs($user)
            ->get('admin/products/' . $product->slug . '/edit')
            ->assertOk();

        Livewire::test(EditProduct::class,['product' => $product])
            ->set('product.price','')
            ->call('save')
            ->assertHasErrors('product.price');
    }

    /** @test */
    function the_quantity_required_when_you_edit_a_product()
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

        Role::create(['name' => 'admin']);

        $user = User::factory()->create()
            ->assignRole('admin');

        $this->actingAs($user)
            ->get('admin/products/' . $product->slug . '/edit')
            ->assertOk();

        Livewire::test(EditProduct::class,['product' => $product])
            ->set('product.quantity','')
            ->call('save')
            ->assertHasErrors('product.quantity');
    }

    /** @test */
    function you_can_search_a_product_in_the_admin_view()
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
            'subcategory_id' => $subcategory->id,
            'quantity' => 2
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product1->id,
            'imageable_type' => Product::class
        ]);

        $product2 = Product::factory()->create([
            'subcategory_id' => $subcategory->id,
            'quantity' => 2
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product2->id,
            'imageable_type' => Product::class
        ]);

        Role::create(['name' => 'admin']);

        $user = User::factory()->create()
            ->assignRole('admin');

        Livewire::test(ShowProducts::class)
            ->set('search', $product1->name)
            ->assertSee($product1->name)
            ->assertDontSee($product2->name);
    }
}
