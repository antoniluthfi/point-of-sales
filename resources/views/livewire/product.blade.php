<div>
    <div class="row">
        <div class="col-md-8">
            @if (session()->has('info'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('info') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h2 class="font-weight-bold mb-3">Product List</h2>
                        </div>
    
                        <div class="col-md-4">
                            <div class="input-group mb-2 mr-sm-2">
                                <input wire:model="search" type="text" class="form-control" id="inlineFormInputGroupUsername2" placeholder="Search product here">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
    
                    <table class="table table-bordered table-hovered table-striped">
                        <thead>
                            <tr class="text-center">
                                <th>No</th>
                                <th>Name</th>
                                <th>Image</th>
                                <th>Description</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th width="11%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $index => $product)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td width="20%">
                                        <img src="{{ asset('storage/images/' . $product->image) }}" alt="product image" class="img-fluid">
                                    </td>
                                    <td>{{ $product->description }}</td>
                                    <td class="text-center">{{ $product->qty }}</td>
                                    <td class="text-right">Rp. {{ number_format($product->price) }}</td>
                                    <td class="text-center">
                                        <button wire:click="getProductToUpdate({{ $product->id }})" type="button" class="btn btn-primary"><i class="fas fa-pencil"></i></button>
                                        <button wire:click="setProductID({{ $product->id }})" type="button" class="btn btn-danger" data-toggle="modal" data-target="#delete-product">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <h5><strong>Tidak ada produk</strong></h5>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{ $products->links() }}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h2 class="font-weight-bold mb-3">{{ $title }}</h2>

                    <form wire:submit.prevent="{{ $title == 'Create Product' ? 'store' : 'updateProduct(' . $productID . ')' }}">
                        <div class="form-group">
                            <label for="name">Product Name</label>
                            <input wire:model="name" type="text" class="form-control" id="name">
                            @error('name')
                                <span class="error text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="image">Product Image</label>
                            <div class="custom-file">
                                <input wire:model="image" type="file" class="custom-file-input" id="image">
                                <label for="image" class="custom-file-label">Choose Image</label>
                                @error('image')
                                    <span class="error text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            @if ($image)
                                <label for="" class="mt-2">Image Preview</label>
                                <img src="{{ $image->temporaryUrl() }}" alt="Image Preview" class="img-fluid">
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea wire:model="description" id="description" cols="30" rows="3" class="form-control"></textarea>
                            @error('description')
                                <span class="error text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="qty">Quantity</label>
                            <input type="number" wire:model="qty" id="qty" min="1" class="form-control">
                            @error('qty')
                                <span class="error text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="price">Price</label>
                            <input type="number" wire:model="price" id="price" min="1" class="form-control">
                            @error('price')
                                <span class="error text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">{{ $title }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- modal --}}
        <div class="modal fade" id="delete-product" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Warning</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <h5>This product will be deleted permanently. Are you sure?</h5>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button wire:click="deleteProduct({{ $productID }})" type="button" class="btn btn-danger" data-dismiss="modal">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
