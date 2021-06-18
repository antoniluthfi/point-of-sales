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
                <table class="table table-bordered table-hovered table-striped table-sm">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Invoice Number</th>
                            <th>Seller</th>
                            <th>Total</th>
                            <th>Pay</th>
                            <th>Change</th>
                            <th>Action</th>
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
                                <td class="text-center" width="5%">
                                    <button wire:click="setInvoiceNumber('{{ $history->invoice_number }}')" type="button" class="btn btn-primary btn-sm">
                                        <i class="fas fa-print"></i>
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

                {{ $histories->links() }}
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white pb-0">
                <h2 class="font-weight-bold mb-3">Print Invoice {{ $invoice_number }}</h2>
            </div>

            <div class="card-body">
                <form wire:submit.prevent="print">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="ip-address">IP Adress Printer</label>
                                <input wire:model="ip_address" type="text" class="form-control" id="ip-address">
                            </div>
                        </div>

                        <div class="col">
                            <div class="form-group">
                                <label for="printer">Nama Printer</label>
                                <input wire:model="printer" type="text" class="form-control" id="printer">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="button" class="btn btn-primary btn-block" @if (!$invoice_number || !$ip_address || !$printer) disabled @endif>Print</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>