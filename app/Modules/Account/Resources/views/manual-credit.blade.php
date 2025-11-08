<!DOCTYPE html>



    Manual Wallet Credit
    


    

        

            

                

                    

                        
💰 Manual Wallet Credit System

                    

                

            <!-- Messages -->
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="row">
                <!-- Credit Form -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Credit User Wallet</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('manual.credit.process') }}">
                                @csrf
                                <div class="mb-3">
                                    <label>User Email:</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>RANC Amount:</label>
                                    <input type="number" name="amount" class="form-control" min="0.01" step="0.01" required>
                                </div>
                                <div class="mb-3">
                                    <label>Reason:</label>
                                    <textarea name="reason" class="form-control" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-success w-100">💰 Credit RANC Wallet</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- User List -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Current Users & Balances</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>RANC Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr onclick="fillEmail('{{ $user->email }}')" style="cursor: pointer;">
                                        <td>{{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td><strong>{{ number_format($user->ranc ?? 0, 2) }}</strong> RANC</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h4>Total Users</h4>
                            <h2 class="text-primary">{{ $users->count() }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h4>Total RANC</h4>
                            <h2 class="text-success">{{ number_format($users->sum('ranc'), 2) }}</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h4>Active Wallets</h4>
                            <h2 class="text-warning">{{ $users->where('ranc', '>', 0)->count() }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function fillEmail(email) {
        document.querySelector('input[name="email"]').value = email;
    }
</script>


```
