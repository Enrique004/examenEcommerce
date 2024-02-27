<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class UsersTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    function you_can_show_yours_orders()
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

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/')
                ->pause(1000)
                ->click('@user')
                ->pause(1000)
                ->assertSee('Perfil')
                ->pause(1000)
                ->click('@orders')
                ->pause(1000)
                ->assertPathIs('/orders')
                ->assertSee('PENDIENTE');
        });
    }
}
