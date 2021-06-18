<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Transaction;

class Home extends Component
{
    public $total_users, $total_sales_today, $total_user_sales_today, $total_sales_this_month, $month;

    public function mount()
    {
        if(Auth::check()) {
            // get total users
            $total_users = User::count();
            $this->total_users = $total_users;

            // get total sales today
            $get_total_sales_today = Transaction::whereDay('created_at', date('d'))->get();
            $new_get_total_sales_today = 0;
            foreach ($get_total_sales_today as $val) {
                $new_get_total_sales_today += (int) $val->total;
            }

            // get total user's sales today
            $get_user_sales_today = Transaction::where('user_id', Auth::user()->id)->whereDay('created_at', date('d'))->get();
            $new_get_total_user_sales_today = 0;
            foreach($get_user_sales_today as $val) {
                $new_get_total_user_sales_today += (int) $val->total;
            }

            // get total sales this month
            $get_total_sales_this_month = Transaction::whereMonth('created_at', date('m'))->get();
            $new_get_total_sales_this_month = 0;
            foreach ($get_total_sales_this_month as $val) {
                $new_get_total_sales_this_month += (int) $val->total;
            }

            $this->total_sales_today = $new_get_total_sales_today;
            $this->total_user_sales_today = $new_get_total_user_sales_today;
            $this->total_sales_this_month = $new_get_total_sales_this_month;
            $this->month = date('F, Y');
        }
    }

    public function render()
    {
        return view('livewire.home');
    }
}
