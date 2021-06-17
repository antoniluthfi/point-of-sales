<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Livewire\WithPagination;
use Carbon\Carbon;
use DB;

use App\Models\Product as ProductModel;
use App\Models\Transaction;
use App\Models\ProductTransaction;

class Cart extends Component
{
    use WithPagination;

    public $tax = "0%";
    public $payment = 0;
    public $search;
    protected $queryString = ['search'];
    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function addItem($id)
    {
        $rowId = 'Cart' . $id;
        $cart = \Cart::session(Auth()->id())->getContent();
        $cekItemId = $cart->whereIn('id', $rowId);

        $product = ProductModel::findOrFail($id);

        if($cekItemId->isNotEmpty()) {
            if($product->qty == $cekItemId[$rowId]->quantity) {
                session()->flash('error', 'jumlah item ' . $product->name . ' tersedia hanya ' . $product->qty);
            } else {
                \Cart::session(Auth()->id())->update($rowId, [
                    'quantity' => [
                        'relative' => true,
                        'value' => 1
                    ]
                ]);
            }
        } else {
            \Cart::session(Auth()->id())->add([
                'id' => 'Cart' . $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1, 
                'attributes' => [
                    'added_at' => Carbon::now(),
                ]
            ]);
        }
    }

    public function enableTax()
    {
        $this->tax = "+10%";
        session()->flash('tax-enabled', 'Tax added succesfully');
    }

    public function disableTax()
    {
        $this->tax = "0%";
        session()->flash('tax-disabled', 'Tax removed succesfully');
    }

    public function increaseItem($rowId)
    {
        $id = explode('Cart', $rowId);
        $id = end($id);

        $product = ProductModel::find($id);
        $cart = \Cart::session(Auth()->id())->getContent();
        $checkItem = $cart->whereIn('id', $rowId);

        if($product->qty == $checkItem[$rowId]->quantity) {
            session()->flash('error', 'jumlah item ' . $product->name . ' yang tersedia hanya ' . $product->qty);
        } else {
            $cart = \Cart::session(Auth()->id())->update($rowId, [
                'quantity' => [
                    'relative' => true,
                    'value' => 1
                ]
            ]);
        }
    }

    public function decreaseItem($rowId)
    {
        $cart = \Cart::session(Auth()->id())->getContent();
        $checkItem = $cart->whereIn('id', $rowId);

        if($checkItem[$rowId]->quantity == 1) {
            $this->removeItem($rowId);
        } else {
            $cart = \Cart::session(Auth()->id())->update($rowId, [
                'quantity' => [
                    'relative' => true,
                    'value' => -1
                ]
            ]);
        }
    }

    public function removeItem($rowId)
    {
        \Cart::session(Auth()->id())->remove($rowId);
    }

    public function saveTransaction()
    {
        $cartTotal = \Cart::session(Auth()->id())->getTotal();
        $kembalian = (int) $this->payment - (int) $cartTotal;
        
        if($kembalian >= 0) {
            DB::beginTransaction();

            try {
                $allCart = \Cart::session(Auth()->id())->getContent();
                $filterCart = $allCart->map(function($item) {
                    return [
                        'id' => preg_replace('/[^0-9.]+/', '', $item->id),
                        'quantity' => $item->quantity
                    ];
                });

                $invoice_number = IdGenerator::generate([
                    'table' => 'transactions',
                    'length' => 10,
                    'prefix' => 'INV-',
                    'field' => 'invoice_number'
                ]);

                foreach($filterCart as $cart) {
                    $product = ProductModel::find($cart['id']);

                    if($product->qty === 0) {
                        return session()->flash('transaction-error', 'Jumlah produk ' . $product->name . ' kurang!');
                    }

                    $product->decrement('qty', $cart['quantity']);

                    ProductTransaction::create([
                        'product_id' => $cart['id'],
                        'invoice_number' => $invoice_number,
                        'qty' => $cart['quantity']
                    ]);
                }

                Transaction::create([
                    'invoice_number' => $invoice_number,
                    'user_id' => Auth()->id(),
                    'pay' => $this->payment,
                    'total' => $cartTotal
                ]);

                \Cart::session(Auth()->id())->clear();
                $this->payment = 0;

                DB::commit();
                session()->flash('transaction-success', 'Transaksi berhasil disimpan');
            } catch (\Throwable $th) {
                DB::rollback();
                session()->flash('transaction-error', $th);
            }
        }
    }

    public function render()
    {
        if($this->search) {
            $product = ProductModel::where('name', 'like', '%' . $this->search . '%')->orderBy('created_at', 'desc')->paginate(10);
        } else {
            $product = ProductModel::orderBy('created_at', 'desc')->paginate(10);
        }

        $condition = new \Darryldecode\Cart\CartCondition([
            'name' => 'pajak',
            'type' => 'tax',
            'target' => 'total',
            'value' => $this->tax,
            'order' => 1
        ]);

        \Cart::session(Auth()->id())->condition($condition);
        $items = \Cart::session(Auth()->id())->getContent()->sortBy(function($cart) {
            return $cart->attributes->get('added_at');
        }); 

        if(\Cart::isEmpty()) {
            $cartData = [];
        } else {
            foreach ($items as $item) {
                $cart[] = [
                    'rowId' => $item->id,
                    'name' => $item->name,
                    'qty' => $item->quantity,
                    'pricesingle' => $item->price,
                    'price' => $item->getPriceSum()
                ];
            }

            $cartData = collect($cart);
        }

        $sub_total = \Cart::session(Auth()->id())->getSubTotal();
        $total = \Cart::session(Auth()->id())->getTotal();

        $newCondition = \Cart::session(Auth()->id())->getCondition('pajak');
        $pajak = $newCondition->getCalculatedValue($sub_total);

        $summary = [
            'sub_total' => $sub_total,
            'pajak' => $pajak,
            'total' => $total
        ];

        return view('livewire.cart', [
            'products' => $product,
            'carts' => $cartData,
            'summary' => $summary 
        ]);
    }
}
