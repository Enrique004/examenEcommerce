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

    public $search;
    public $orden = 'name';

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
        } else if ($this->orden == 'totalQuantity') {
            $products = Product::where('name', 'LIKE', "%{$this->search}%")
                ->get();

            $products = $products->sortByDesc('totalQuantity');
        } else if ($this->orden == 'totalReserves') {
            $products = Product::where('name', 'LIKE', "%{$this->search}%")
                ->get();

            $products = $products->sortByDesc('totalReserves');
        }

        return view('livewire.admin.show-products', compact('products'))
            ->layout('layouts.admin');
    }
}
