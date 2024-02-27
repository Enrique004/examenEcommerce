<?php

namespace Tests\Browser;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class NavigationTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    function it_shows_the_differents_categories()
    {
        $category = Category::factory()->create([
            'name' => 'Celulares y tablets',
            'slug' => Str::slug('Celulares y tablets'),
            'icon' => '<i class="fas fa-mobile-alt"></i>'
        ]);

        $this->browse(function (Browser $browser) use ($category) {
            $browser->visit('/')
                ->clickLink('Categorias') // Clickas en un enlace <a>
                ->assertVisible('i.fas.fa-mobile-alt') // Que sea visible un elemento visual (imagen)
                ->assertSee($category->name);
        });
    }

    /** @test */
    function it_shows_the_differents_subcategories()
    {
        $category = Category::factory()->create([
            'name' => 'Celulares y tablets',
            'slug' => Str::slug('Celulares y tablets'),
            'icon' => '<i class="fas fa-mobile-alt"></i>'
        ]);

        $subcategory = Subcategory::factory()->create([
            'category_id' => $category->id,
            'name' => 'Celulares y smartphones',
            'slug' => Str::slug('Celulares y smartphones'),
            'color' => true
        ]);

        $this->browse(function (Browser $browser) use ($category,$subcategory) {
            $browser->visit('/')
                ->clickLink('Categorias')
                ->mouseover('.navigation-link')
                ->assertSee($subcategory->name);
        });
    }
}
