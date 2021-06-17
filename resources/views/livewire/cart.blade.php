<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white pb-0">
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
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse ($products as $product)
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <div class="card-body pb-0">
                                    <img src="{{ asset('storage/images/' . $product->image) }}" alt="{{ $product->name }}" style="width: 100%; height: 175px; object-fit: contain;">
                                    <button wire:click="addItem({{ $product->id }})" type="button" class="btn btn-primary" style="position: absolute; top: 3px; right: 3px;"><i class="fas fa-cart-plus"></i></button>

                                    <h6 class="text-center font-weight-bold">{{ $product->name }}</h6>
                                    <p class="text-center">Rp. {{ number_format($product->price) }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="font-weight-bold text-center">No Products Found</h4>
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>

                {{ $products->links() }}
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white pb-0">
                <h2 class="font-weight-bold mb-3">Cart</h2>
            </div>
            <div class="card-body">
                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <table class="table table-bordered table-striped table-hovered table-sm">
                    <thead class="text-center text-white bg-secondary">
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($carts as $index => $cart)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $cart['name'] }}</td>
                                <td class="text-center">{{ $cart['qty'] }}</td>
                                <td class="text-right font-weight-bold">Rp. {{ number_format($cart['price']) }}</td>
                                <td class="text-center" width="25%">
                                    <button wire:click="increaseItem('{{ $cart['rowId'] }}')" type="button" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i></button>
                                    <button wire:click="decreaseItem('{{ $cart['rowId'] }}')" type="button" class="btn btn-danger btn-sm"><i class="fas fa-minus"></i></button>
                                    <button wire:click="removeItem('{{ $cart['rowId'] }}')" type="button" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5"><h6 class="text-center">Empty Cart</h6></td>
                            </tr>
                        @endforelse        
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mt-2">
            <div class="card-body">
                <h4 class="font-weight-bold">Cart Summary</h4>

                @if (session()->has('tax-enabled'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('tax-enabled') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @elseif (session()->has('tax-disabled'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('tax-disabled') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <form wire:submit.prevent="saveTransaction">
                    <table class="table table-striped table-sm">
                        <tr>
                            <th>Sub Total</th>
                            <td>:</td>
                            <td class="text-right"><strong>Rp. {{ number_format($summary['sub_total']) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Tax</th>
                            <td>:</td>
                            <td class="text-right" id="tax-amount" data-tax="{{ $summary['pajak'] }}"><strong>Rp. {{ number_format($summary['pajak']) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Total</th>
                            <td>:</td>
                            <td class="text-right" id="total-amount" data-total="{{ $summary['total'] }}"><strong>Rp. {{ number_format($summary['total']) }}</strong></td>
                        </tr>
                        @if ($carts)
                            <tr>
                                <th>Payment amount</th>
                                <td>:</td>
                                <td>
                                    <input wire:model="payment" type="number" class="form-control" min="0" id="payment">
                                </td>
                            </tr>   
                            <tr>
                                <th>Change</th>
                                <td>:</td>
                                <td wire:ignore class="text-right font-weight-bold" id="change"></td>
                            </tr>                     
                        @endif
                    </table>

                    @if (session()->has('transaction-success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('transaction-success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @elseif (session()->has('transaction-error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('transaction-error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            @if ($tax == "0%")
                                <button wire:click="enableTax" type="button" id="enable-tax" class="btn btn-primary btn-block"><i class="fas fa-percent fa-sm"></i> Add Tax</button>
                            @else 
                                <button wire:click="disableTax" type="button" id="disable-tax" class="btn btn-danger btn-block"><i class="fas fa-percent fa-sm"></i> Remove Tax</button>
                            @endif
                        </div>

                        <div class="col-md-6">
                            <button wire:ignore type="submit" id="save-transaction" class="btn btn-success btn-block" disabled><i class="fas fa-exchange"></i> Save Transaction</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('custom-script')
    <script>
        const getChange = () => {
            const paymentAmount = document.getElementById('payment').value;
            const change = document.getElementById('change');
            const totalAmount = document.getElementById('total-amount').dataset.total;
            const totalChange = parseInt(paymentAmount) - parseInt(totalAmount);
            const saveTransaction = document.getElementById('save-transaction');

            if(!paymentAmount || totalChange < 0) {
                change.innerHTML = '';
                saveTransaction.setAttribute('disabled', true);
            } else {
                change.innerHTML = `Rp. ${convertToRupiah(totalChange)}`;
                saveTransaction.removeAttribute('disabled');
            }
        }

        // ketika input payment di ketikkan
        payment.oninput = () => getChange();

        const convertToRupiah = (num) => {
            let strNum = num.toString();
            strNum = strNum.split(',');
            const remain = strNum[0].length % 3;
            let rupiah = strNum[0].substr(0, remain);
            const ribuan = strNum[0].substr(remain).match(/\d{1,3}/gi);

            if(ribuan) {
                const separator = remain ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            return strNum[1] != undefined ? `${rupiah},${strNum[1]}` : rupiah;
        }
    </script>
@endpush