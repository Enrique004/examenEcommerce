<?php

namespace Tests\Browser;

use App\Models\Brand;
use App\Models\Category;
use App\Models\City;
use App\Models\Department;
use App\Models\District;
use App\Models\Image;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class OrdersTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    function you_dont_see_the_form_data_dropdown()
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

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'password')
                ->press('Iniciar sesión')
                ->assertPathIs('/')
                ->visit('orders/create')
                ->assertDontSee('Referencia');
        });
    }

    /** @test */
    function the_selectors_of_the_form_data_dropdown_work_right()
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

        $department = Department::factory()->create();

        $city = City::factory()->create([
            'department_id' => $department->id
        ]);

        $district = District::factory()->create([
            'city_id' => $city->id
        ]);

        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user,$department,$city,$district) {
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'password')
                ->press('Iniciar sesión')
                ->assertPathIs('/')
                ->visit('orders/create')
                ->click('@type2')
                ->assertSee('Referencia')
                ->pause(1000)
                ->click('@departments')
                ->pause(1000)
                ->assertSee($department->name)
                ->pause(1000)
                ->click('@cities')
                ->pause(1000)
                ->assertDontSee($city->name)
                ->pause(1000)
                ->click('@districts')
                ->pause(1000)
                ->assertDontSee($district->name)
                ->pause(1000)
                ->click('@selected-department-1')
                ->pause(1000)
                ->click('@cities')
                ->pause(1000)
                ->assertSee($city->name)
                ->pause(1000)
                ->click('@selected-city-1')
                ->pause(1000)
                ->click('@districts')
                ->pause(1000)
                ->assertSee($district->name);
        });
    }
}
