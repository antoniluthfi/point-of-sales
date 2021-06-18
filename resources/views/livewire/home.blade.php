<div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="font-weight-bold">Total Users</h5>
                    <h5>{{ $total_users }} {{ $total_users > 1 ? 'Users' : 'User' }}</h5>
                </div>
            </div>
        </div>
    
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="font-weight-bold">Total Sales Today</h5>
                    <h5>Rp. {{ number_format($total_sales_today) }}</h5>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="font-weight-bold">Total {{Auth::user()->name}}'s Sales Today</h5>
                    <h5>Rp. {{ number_format($total_user_sales_today) }}</h5>
                </div>
            </div>
        </div>
    
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="font-weight-bold">Total Sales on {{ $month }}</h5>
                    <h5>Rp. {{ number_format($total_sales_this_month) }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>
