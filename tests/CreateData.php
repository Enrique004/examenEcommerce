<?php

namespace Tests;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Support\Str;

trait CreateData
{
    /**
     * Crea una categoria
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function createCategory()
    {
        $category = Category::factory()->create([
            'name' => 'Celulares y tablets',
            'slug' => Str::slug('Celulares y tablets'),
            'icon' => '<i class="fas fa-mobile-alt"></i>',
        ]);

        $category->brands()->attach(Brand::factory()->create());

        return $category;
    }

    /**
     * Crea una subcategoria
     * @param $category_id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function createSubcategory($category_id)
    {
        $subcategory = Subcategory::factory()->create([
            'category_id' => $category_id,
            'name' => 'Celulares y smartphones',
            'slug' => Str::slug('Celulares y smartphones'),
        ]);

        return $subcategory;
    }

    /**
     * Crea un producto
     * @param $subcategory_id
     * @param $name
     * @param $price
     * @param $quantity
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function createProduct($subcategory_id, $name = 'Ejemplo', $price = 10, $quantity = 15)
    {
        $product = Product::factory()->create([
            'name' => $name,
            'subcategory_id' => $subcategory_id,
            'price' => $price,
            'quantity' => $quantity
        ]);

        Image::factory(1)->create([
            'imageable_id' => $product->id,
            'imageable_type' => Product::class
        ]);

        return $product;
    }
}
