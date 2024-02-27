<?php

namespace App\Http\Livewire\Admin;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Livewire\WithPagination;

class ShowProducts extends Component
{
    use WithPagination;

    public $search,$orders;
    public $orden = 'name';

    public function mount()
    {
        $this->orders = Order::all();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        if(Schema::hasColumn('products',$this->orden)) {
            $products = Product::where('name', 'LIKE', "%{$this->search}%")
                ->orderBy($this->orden)
                ->paginate(10);
        } elseif ($this->orden == 'totalQuantity') {
            $products = Product::where('name', 'LIKE', "%{$this->search}%")
                ->paginate(10);

            foreach ($products as $product) {
                $product->orderBy($product->totalQuantity);
            }
            $products->sortBy($this->orden);
        } elseif ($this->orden == 'totalReserves') {
            $products = Product::where('name', 'LIKE', "%{$this->search}%")
                ->paginate(10);

            $products->sortBy('totalReserves');
        }

        return view('livewire.admin.show-products', compact('products'))
            ->layout('layouts.admin');
    }
}
