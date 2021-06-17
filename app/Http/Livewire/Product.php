<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Product as ProductModel;
use Illuminate\Support\Facades\Storage;

class Product extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $name, $image, $description, $qty, $price, $productID, $search;
    public $title = 'Create Product';
    protected $queryString = ['search'];
    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
            'image' => 'image|max:2048|required',
            'description' => 'required',
            'qty' => 'required',
            'price' => 'required'
        ]);

        $imageName = md5($this->image . microtime() . '.' . $this->image->extension());
        Storage::putFileAs('public/images', $this->image, $imageName);

        ProductModel::create([
            'name' => $this->name,
            'image' => $imageName,
            'description' => $this->description,
            'qty' => $this->qty,
            'price' => $this->price
        ]);

        session()->flash('info', 'Product Created Successfuly');

        $this->name = '';
        $this->image = '';
        $this->description = '';
        $this->qty = '';
        $this->price = '';
    }

    public function getProductToUpdate($id)
    {
        $product = ProductModel::find($id);

        if($product) {
            $this->title = 'Update Product';
            $this->productID = $product->id;

            $this->name = $product->name;
            $this->description = $product->description;
            $this->qty = $product->qty;
            $this->price = $product->price;
        }
    }

    public function updateProduct($id)
    {
        $this->validate([
            'name' => 'required',
            'image' => 'image|max:2048|required',
            'description' => 'required',
            'qty' => 'required',
            'price' => 'required'
        ]);

        $product = ProductModel::find($id);

        if($product) {
            // remove old image
            unlink(public_path('/storage/images/' . $product->image)); 

            // store new image
            $imageName = md5($this->image . microtime() . '.' . $this->image->extension());
            Storage::putFileAs('public/images', $this->image, $imageName);
    
            $product->name = $this->name;
            $product->description = $this->description;
            $product->image = $imageName;
            $product->qty = $this->qty;
            $product->price = $this->price;
            $product->update();
        }

        $this->title = 'Create Product';
        $this->productID = '';
        $this->name = '';
        $this->image = '';
        $this->description = '';
        $this->qty = '';
        $this->price = '';
        session()->flash('info', 'Product updated successfully');
    }

    public function setProductID($id)
    {
        $this->productID = $id;
    }

    public function deleteProduct($id)
    {
        $product = ProductModel::find($id);

        if($product) {
            unlink(public_path('/storage/images/' . $product->image)); 
            $product->delete();
            session()->flash('info', 'Product deleted successfully');
        }
    }

    public function render()
    {
        if($this->search) {
            $product = ProductModel::where('name', 'like', '%' . $this->search . '%')->orderBy('created_at', 'desc')->paginate(10);
        } else {
            $product = ProductModel::orderBy('created_at', 'desc')->paginate(10);
        }

        return view('livewire.product', [
            'products' => $product
        ]);
    }
}
