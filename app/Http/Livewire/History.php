<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Transaction;

class History extends Component
{
    use WithPagination;

    public $search, $invoice_number, $ip_address, $printer;
    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function setInvoiceNumber($invoice_number) 
    {
        $this->invoice_number = $invoice_number;
    }

    public function print()
    {
        dd('library belum terinstal');
    }

    public function render()
    {
        if($this->search) {
            $histories = Transaction::where('invoice_number', 'like', '%' . $this->search . '%')->orderBy('invoice_number', 'desc')->paginate(10);
        } else {
            $histories = Transaction::orderBy('invoice_number', 'desc')->paginate(10);
        }

        return view('livewire.history', [
            'histories' => $histories
        ]);
    }
}
