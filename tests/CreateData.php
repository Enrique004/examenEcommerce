<?php

namespace Tests;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Image;
use App\Models\Order;
use App\Models\Product;
use App\Models\Size;
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
    public function createSubcategory($category_id,$color,$size)
    {
        $subcategory = Subcategory::factory()->create([
            'category_id' => $category_id,
            'name' => 'Celulares y smartphones',
            'slug' => Str::slug('Celulares y smartphones'),
            'color' => $color,
            'size' => $size
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
    public function createProduct($numProducts,$subcategory_id,$name, $price, $quantity)
    {
        if($numProducts == 1) {
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
        } else {
            $product = Product::factory($numProducts)->create([
                'name' => $name,
                'subcategory_id' => $subcategory_id,
                'price' => $price,
                'quantity' => $quantity
            ]);

            Image::factory(1)->create([
                'imageable_id' => $product->id,
                'imageable_type' => Product::class
            ]);
        }

        return $product;
    }

    public function createColor()
    {
        $color = Color::create([
            'name' => 'rojo'
        ]);

        return $color;
    }

    public function createSize($product,$color_id)
    {
        $product->sizes()->create([
            'name' => 'Talla S'
        ]);

        $sizes = Size::all();

        foreach ($sizes as $size) {
            $size->colors()->attach([
                $color_id => [
                    'quantity' => 10
                ],
            ]);
        }
    }

    public function createOrder($user_id,$phone = '1234',$contact = 'eadf',$shipping_cost = 0,$total = 5,$status = 1, $envio_type = 1,
    $product_name = 'Producto 1', $product_quantity = 1, $product_price = 10)
    {
        $order = Order::create([
            'phone' => $phone,
            'contact' => $contact,
            'shipping_cost' => $shipping_cost,
            'total' => $total,
            'content' => json_encode([
                'product' => $product_name,
                'quantity' => $product_quantity,
                'price' => $product_price,
                'description' => 'Descripción del producto 1'
            ]),
            'user_id' => $user_id,
            'status' => $status,
            'envio_type' => $envio_type
        ]);

        return $order;
    }

    public function create($numProducts = 1,$color=0,$size=0,$name = 'Ejemplo', $price = 10, $quantity = 15)
    {
        $category = $this->createCategory();
        $subcategory = $this->createSubcategory($category->id,$color,$size);
        $product = $this->createProduct($numProducts,$subcategory->id,$name,$price,$quantity);

        return $product;
    }

    public function create2($color=0,$size=0)
    {
        $category = $this->createCategory();
        $subcategory = $this->createSubcategory($category->id,$color,$size);

        return $subcategory;
    }

}
