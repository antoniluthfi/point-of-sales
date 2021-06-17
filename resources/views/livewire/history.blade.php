<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white pb-0">
                <div class="row">
                    <div class="col-md-8">
                        <h2 class="font-weight-bold mb-3">History List</h2>
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
                <table class="table table-bordered table-hovered table-striped">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Invoice Number</th>
                            <th>Seller</th>
                            <th>Total</th>
                            <th>Pay</th>
                            <th>Change</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($histories as $index => $history)
                            <tr class="text-center">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $history->invoice_number }}</td>
                                <td>{{ $history->user->name }}</td>
                                <td class="text-right">Rp. {{ number_format($history->total) }}</td>
                                <td class="text-right">Rp. {{ number_format($history->pay) }}</td>
                                <td class="text-right">Rp. {{ number_format($history->pay - $history->total) }}</td>
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

                {{ $histories->links() }}
            </div>
        </div>
    </div>
</div>