@extends('admin.layouts.app')

@section('title', 'Manual Wallet Credit')

@section('styles')

.container { max-width: 1200px; margin: 0 auto; padding: 20px; }

.header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 12px; margin-bottom: 30px; text-align: center; }

.header h1 { margin: 0; font-size: 2.5rem; }

.card { background: white; border-radius: 12px; padding: 25px; margin-bottom: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }

.form-group { margin-bottom: 20px; }

.form-label { display: block; font-weight: 600; color: #374151; margin-bottom: 8px; }

.form-input { width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 1rem; }

.form-input:focus { border-color: #667eea; outline: none; }

.btn { display: inline-block; padding: 12px 24px; background: #667eea; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; }

.btn:hover { background: #5a6fd8; }

.btn:disabled { background: #9ca3af; cursor: not-allowed; }

.alert { padding: 16px; border-radius: 8px; margin-bottom: 20px; }

.alert-success { background: #d1fae5; color: #065f46; }

.alert-error { background: #fee2e2; color: #991b1b; }

.search-box { position: relative; }

.search-results { position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #e5e7eb; border-radius: 8px; max-height: 300px; overflow-y: auto; z-index: 1000; }

.search-item { padding: 12px; border-bottom: 1px solid #e5e7eb; cursor: pointer; }

.search-item:hover { background: #f3f4f6; }

.search-item:last-child { border-bottom: none; }

.user-details { background: #f8fafc; border: 2px solid #e5e7eb; border-radius: 8px; padding: 20px; margin-top: 15px; display: none; }

.detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e5e7eb; }

.detail-row:last-child { border-bottom: none; }

.detail-label { font-weight: 600; color: #6b7280; }

.detail-value { color: #1e293b; font-weight: 500; }

.empty-state { text-align: center; padding: 40px 20px; color: #64748b; }


@endsection

@section('content')
<div class="container">
    <!-- Header -->
    <div class="header">
        <h1>🎯 Manual Wallet Credit System</h1>
        <p>Using Proven Tinker Method</p>
    </div>

    <!-- Messages -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <!-- Search Users -->
    <div class="card">
        <h2>🔍 Search and Select User</h2>
        
        <div class="form-group search-box">
            <label class="form-label">Search Users (Name, Email, Username)</label>
            <input type="text" id="userSearch" class="form-input" placeholder="Start typing..." autocomplete="off">
            <div id="searchResults" class="search-results" style="display: none;"></div>
        </div>
    </div>

    <!-- User Details -->
    <div class="card">
        <h2>👤 User Information</h2>
        
        <div id="emptyState" class="empty-state">
            <p>Please search and select a user to view details</p>
        </div>

        <div id="userDetails" class="user-details">
            <div class="detail-row">
                <span class="detail-label">User ID:</span>
                <span class="detail-value" id="userId">-</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Name:</span>
                <span class="detail-value" id="userName">-</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value" id="userEmail">-</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Current RANC Balance:</span>
                <span class="detail-value" id="userBalance">0.00</span>
            </div>

            <!-- Credit Form -->
            <div style="margin-top: 20px; background: #f8fafc; padding: 20px; border-radius: 8px;">
                <h3 style="margin-top: 0;">💰 Credit Amount</h3>
                
                <form id="creditForm">
                    <input type="hidden" id="selectedUserId">
                    
                    <div class="form-group">
                        <label class="form-label">RANC Amount to Credit</label>
                        <input type="number" id="creditAmount" class="form-input" step="0.01" min="0.01" placeholder="Enter amount">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Credit Type</label>
                        <select id="creditType" class="form-input">
                            <option value="manual">Manual Credit</option>
                            <option value="earning">Earning</option>
                            <option value="bonus">Bonus</option>
                            <option value="refund">Refund</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Reason</label>
                        <textarea id="creditReason" class="form-input" rows="3" placeholder="Reason for credit..."></textarea>
                    </div>

                    
```Process Credit```

                </form>
            </div>
        </div>
    </div>

    <!-- Recent Credits -->
    @if(isset($recentCredits) && $recentCredits->count() > 0)
    <div class="card">
        <h2>📋 Recent Credits</h2>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8fafc;">
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb;">Date</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb;">User</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb;">Amount</th>
                        <th style="padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb;">Reason</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentCredits as $credit)
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #e5e7eb;">{{ $credit->created_at->format('M j, Y g:i A') }}</td>
                        <td style="padding: 12px; border-bottom: 1px solid #e5e7eb;">{{ $credit->user->name ?? 'N/A' }}</td>
                        <td style="padding: 12px; border-bottom: 1px solid #e5e7eb; color: #059669; font-weight: 600;">+{{ number_format($credit->ranc ?? $credit->amount ?? 0, 2) }}</td>
                        <td style="padding: 12px; border-bottom: 1px solid #e5e7eb;">{{ $credit->remark ?? 'Manual Credit' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

@section('scripts')

let selectedUser = null;

// Search users

document.getElementById('userSearch').addEventListener('input', function() {

const query = this.value.trim();

if (query.length < 2) {

document.getElementById('searchResults').style.display = 'none';

return;

}

fetch(`{{ route('admin.wallets.search-users') }}?q=${encodeURIComponent(query)}`)
    .then(response => response.json())
    .then(data => {
        const results = document.getElementById('searchResults');
        if (data.users && data.users.length > 0) {
            results.innerHTML = data.users.map(user => `
                <div class="search-item" onclick="selectUser(${JSON.stringify(user).replace(/"/g, '&quot;')})">
                    <strong>${user.name}</strong><br>
                    ${user.email} • ID: ${user.id}<br>
                    <small style="color: #059669;">Balance: ${user.subscription_wallet ? parseFloat(user.subscription_wallet.ranc).toFixed(2) : '0.00'} RANC</small>
                </div>
            `).join('');
        } else {
            results.innerHTML = '<div class="search-item">No users found</div>';
        }
        results.style.display = 'block';
    });
});

// Select user

window.selectUser = function(user) {

selectedUser = user;

document.getElementById('userSearch').value = '';

document.getElementById('searchResults').style.display = 'none';

document.getElementById('userId').textContent = user.id;
document.getElementById('userName').textContent = user.name;
document.getElementById('userEmail').textContent = user.email;
document.getElementById('userBalance').textContent = user.subscription_wallet ? 
    parseFloat(user.subscription_wallet.ranc).toFixed(2) : '0.00';
document.getElementById('selectedUserId').value = user.id;

document.getElementById('emptyState').style.display = 'none';
document.getElementById('userDetails').style.display = 'block';
};

// Process credit

document.getElementById('creditForm').addEventListener('submit', async function(e) {

e.preventDefault();

if (!selectedUser) {
    alert('Please select a user first');
    return;
}

const formData = new FormData();
formData.append('user_id', selectedUser.id);
formData.append('ranc_amount', document.getElementById('creditAmount').value);
formData.append('credit_type', document.getElementById('creditType').value);
formData.append('credit_reason', document.getElementById('creditReason').value);
formData.append('_token', '{{ csrf_token() }}');

const btn = document.getElementById('creditBtn');
btn.disabled = true;
btn.textContent = 'Processing...';

try {
    const response = await fetch(`{{ route('admin.wallets.process-credit') }}`, {
        method: 'POST',
        body: formData
    });

    const data = await response.json();

    if (data.success) {
        alert(data.message);
        location.reload();
    } else {
        alert('Error: ' + data.message);
    }
} catch (error) {
    alert('Network error: ' + error.message);
} finally {
    btn.disabled = false;
    btn.textContent = 'Process Credit';
}
});


@endsection
