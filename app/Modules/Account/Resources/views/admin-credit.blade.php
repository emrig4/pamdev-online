<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Manual Credit System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .user-card {
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            cursor: pointer;
            border-radius: 10px;
            height: 100%;
        }
        .user-card:hover {
            border-color: #007bff;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .user-card.selected {
            border-color: #28a745;
            background-color: #f8fff9;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .balance-large {
            font-size: 1.8rem;
            font-weight: bold;
            margin: 10px 0;
        }
        .quick-buttons button {
            margin: 2px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            box-shadow: 0 0 0 2px rgba(255,255,255,0.8);
        }
        .status-active { 
            background: linear-gradient(45deg, #28a745, #20c997);
            animation: pulse 2s infinite;
        }
        .status-inactive { background-color: #dc3545; }
        .status-zero { background-color: #6c757d; }
        
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(40, 167, 69, 0); }
            100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
        }

        .credit-form {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 15px;
            text-align: center;
            padding: 20px;
            margin-bottom: 15px;
        }
        
        .user-list-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .amount-preset {
            border-radius: 20px;
            margin: 2px;
            padding: 8px 12px;
            font-size: 0.85rem;
        }
        
        .loading-spinner {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 9999;
            background: rgba(255,255,255,0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body class="bg-light">
    <!-- Loading Spinner -->
    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 mb-0">Processing...</p>
    </div>

    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <div class="row align-items-center">
                            <div class="col">
                                <h1 class="mb-0">
                                    <i class="bi bi-shield-check"></i> 
                                    Admin Manual Credit System
                                </h1>
                                <p class="mb-0 mt-2">
                                    <i class="bi bi-info-circle"></i>
                                    Based on the proven increment method that successfully credited 500 RANC
                                </p>
                            </div>
                            <div class="col-auto">
                                <span class="badge bg-light text-dark fs-6">
                                    <i class="bi bi-people-fill"></i> {{ $users->count() }} Users
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill"></i>
                        {!! session('success') !!}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>
        @endif
        
        @if(session('error'))
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <!-- Users List -->
            <div class="col-lg-8">
                <div class="user-list-header">
                    <h4><i class="bi bi-person-lines-fill"></i> 👥 Current Users & RANC Balances</h4>
                    <p class="mb-0">Click on any user card to select them for manual crediting</p>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            @foreach($users as $user)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card user-card h-100" 
                                     data-user-id="{{ $user->id }}" 
                                     data-user-email="{{ $user->email }}"
                                     data-user-name="{{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-0">
                                                @if($user->ranc > 0)
                                                    <span class="status-indicator status-active"></span>
                                                @elseif($user->ranc == 0)
                                                    <span class="status-indicator status-zero"></span>
                                                @else
                                                    <span class="status-indicator status-inactive"></span>
                                                @endif
                                                {{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}
                                                @if(empty($user->first_name) && empty($user->last_name))
                                                    <small class="text-muted">({{ $user->username ?? 'No name' }})</small>
                                                @endif
                                            </h6>
                                            @if($user->active == 1)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </div>
                                        
                                        <p class="text-muted small mb-2">{{ $user->email }}</p>
                                        
                                        <div class="balance-large text-center mb-2">
                                            @if($user->ranc > 0)
                                                <span class="text-success">
                                                    <i class="bi bi-coin"></i> {{ number_format($user->ranc, 2) }} RANC
                                                </span>
                                            @else
                                                <span class="text-muted">
                                                    <i class="bi bi-coin"></i> {{ number_format($user->ranc, 2) }} RANC
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Quick Action Buttons -->
                                        <div class="quick-buttons">
                                            <button class="btn btn-outline-success btn-sm quick-credit" 
                                                    data-user-id="{{ $user->id }}" 
                                                    data-amount="100">
                                                <i class="bi bi-plus-circle"></i> +100 RANC
                                            </button>
                                            <button class="btn btn-outline-success btn-sm quick-credit" 
                                                    data-user-id="{{ $user->id }}" 
                                                    data-amount="500">
                                                <i class="bi bi-plus-circle-fill"></i> +500 RANC
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Credit Form -->
            <div class="col-lg-4">
                <!-- Credit Form Card -->
                <div class="credit-form">
                    <h5><i class="bi bi-credit-card-fill"></i> Manual Credit Selected User</h5>
                    <form id="creditForm" method="POST" action="{{ route('admin.credit.process') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label"><strong>Selected User:</strong></label>
                            <div id="selectedUser" class="form-control-plaintext text-white">
                                <em>Click on a user card to select...</em>
                            </div>
                            <input type="hidden" name="user_id" id="user_id">
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label"><strong>RANC Amount:</strong></label>
                            <div class="input-group">
                                <input type="number" name="amount" id="amount" 
                                       class="form-control" step="0.01" min="0.01" required>
                                <span class="input-group-text bg-light text-dark">RANC</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label"><strong>Reason:</strong></label>
                            <textarea name="reason" id="reason" class="form-control" rows="3" 
                                      placeholder="e.g., Manual payment credit" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100" id="submitBtn" disabled>
                            <i class="bi bi-coins"></i> 💰 Credit RANC Wallet
                        </button>
                    </form>

                    <!-- Quick Presets -->
                    <div class="mt-3">
                        <small class="text-white-50"><strong>Quick Amount Presets:</strong></small><br>
                        <div class="btn-group mt-2" role="group">
                            <button type="button" class="btn btn-light btn-sm amount-preset" onclick="setAmount(50)">₦50</button>
                            <button type="button" class="btn btn-light btn-sm amount-preset" onclick="setAmount(100)">₦100</button>
                            <button type="button" class="btn btn-light btn-sm amount-preset" onclick="setAmount(250)">₦250</button>
                            <button type="button" class="btn btn-light btn-sm amount-preset" onclick="setAmount(500)">₦500</button>
                            <button type="button" class="btn btn-light btn-sm amount-preset" onclick="setAmount(1000)">₦1000</button>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="stats-card">
                    <h6><i class="bi bi-bar-chart-fill"></i> Quick Statistics</h6>
                    <div class="row text-center">
                        <div class="col-4">
                            <h3 class="mb-0">{{ $users->count() }}</h3>
                            <small>Total Users</small>
                        </div>
                        <div class="col-4">
                            <h3 class="mb-0">{{ $users->where('ranc', '>', 0)->count() }}</h3>
                            <small>Active Wallets</small>
                        </div>
                        <div class="col-4">
                            <h3 class="mb-0">{{ number_format($users->sum('ranc'), 0) }}</h3>
                            <small>Total RANC</small>
                        </div>
                    </div>
                </div>

                <!-- Quick Reference -->
                <div class="card">
                    <div class="card-header">
                        <h6><i class="bi bi-lightbulb"></i> Quick Reference</h6>
                    </div>
                    <div class="card-body">
                        <p class="small mb-2">
                            <strong>🔵 Method Used:</strong><br>
                            Uses the exact proven increment method that successfully credited 500 RANC
                        </p>
                        <p class="small mb-0">
                            <strong>⚡ Status Indicators:</strong><br>
                            🟢 Active wallets, ⚪ Zero balance, 🔴 Inactive
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let selectedUser = null;

        // User card selection
        document.querySelectorAll('.user-card').forEach(card => {
            card.addEventListener('click', function() {
                // Remove previous selections
                document.querySelectorAll('.user-card').forEach(c => c.classList.remove('selected'));
                
                // Select this card
                this.classList.add('selected');
                
                // Store user info
                selectedUser = {
                    id: this.dataset.userId,
                    email: this.dataset.userEmail,
                    name: this.dataset.userName.trim()
                };
                
                // Update form
                updateForm();
            });
        });

        // Update form with selected user
        function updateForm() {
            if (selectedUser) {
                document.getElementById('selectedUser').innerHTML = 
                    `<strong>${selectedUser.name}</strong><br><small>${selectedUser.email}</small>`;
                document.getElementById('user_id').value = selectedUser.id;
                document.getElementById('submitBtn').disabled = false;
                document.getElementById('reason').placeholder = `Manual credit for ${selectedUser.email}`;
            } else {
                document.getElementById('selectedUser').innerHTML = 
                    '<em>Click on a user card to select...</em>';
                document.getElementById('user_id').value = '';
                document.getElementById('submitBtn').disabled = true;
                document.getElementById('reason').placeholder = 'e.g., Manual payment credit';
            }
        }

        // Quick amount buttons
        function setAmount(amount) {
            document.getElementById('amount').value = amount;
        }

        // Quick credit buttons
        document.querySelectorAll('.quick-credit').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent user card selection
                
                const userId = this.dataset.userId;
                const amount = this.dataset.amount;
                
                if (confirm(`Quick credit ${amount} RANC to this user?`)) {
                    showLoading();
                    
                    // Send AJAX request
                    fetch('{{ route("admin.credit.quick") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            user_id: userId,
                            amount: amount,
                            reason: 'Quick Credit'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        hideLoading();
                        if (data.success) {
                            alert(`✅ Success! \n${data.message}\nNew balance: ${data.new_balance} RANC\nUser: ${data.user_email}`);
                            location.reload();
                        } else {
                            alert('❌ Error: ' + data.error);
                        }
                    })
                    .catch(error => {
                        hideLoading();
                        alert('❌ Error: ' + error.message);
                    });
                }
            });
        });

        // Form submission
        document.getElementById('creditForm').addEventListener('submit', function(e) {
            if (!selectedUser) {
                e.preventDefault();
                alert('Please select a user first!');
                return false;
            }
            
            // Show loading
            showLoading();
        });

        // Loading functions
        function showLoading() {
            document.getElementById('loadingSpinner').style.display = 'block';
        }

        function hideLoading() {
            document.getElementById('loadingSpinner').style.display = 'none';
        }
    </script>
</body>
</html>