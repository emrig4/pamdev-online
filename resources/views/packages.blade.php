<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy RANC Credits - Payment Packages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
rel="stylesheet">
    <style>
        .package-card {
            transition: transform 0.3s ease;
            border: 2px solid #e9ecef;
        }
        .package-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .popular-badge {
            background: linear-gradient(45deg, #ff6b6b, #ee5a24);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .price-highlight {
            color: #28a745;
            font-size: 2.5rem;
            font-weight: bold;
        }
        .feature-list {
            list-style: none;
            padding: 0;
        }
        .feature-list li {
            padding: 8px 0;
            border-bottom: 1px solid #f8f9fa;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-dark mb-3">
                    <i class="fas fa-coins text-warning me-3"></i>
                    Buy RANC Credits
                </h1>
                <p class="lead text-muted">Choose your perfect package and get instant wallet 
credits</p>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4 mb-5">
        @foreach($packages as $package)
        <div class="col-lg-4 col-md-6">
            <div class="card package-card h-100">
                @if($package['popular'])
                <div class="text-center">
                    <span class="popular-badge">
                        <i class="fas fa-star me-1"></i>MOST POPULAR
                    </span>
                </div>
                @endif
                
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="fas fa-{{ $package['id'] == 'basic' ? 'leaf' : ($package['id'] == 
'standard' ? 'star' : 'crown') }} text-primary" style="font-size: 3rem;"></i>
                    </div>
                    
                    <h3 class="card-title text-{{ $package['popular'] ? 'success' : 'primary' }} 
fw-bold">
                        {{ $package['name'] }}
                    </h3>
                    
                    <div class="my-4">
                        <div class="price-highlight">₦{{ number_format($package['price']) }}</div>
                        <p class="text-muted mb-0">{{ $package['description'] }}</p>
                    </div>

                    <ul class="feature-list text-start mb-4">
                        <li><i class="fas fa-check text-success me-2"></i>{{ 
number_format($package['ranc_amount']) }} RANC Credits</li>
                        <li><i class="fas fa-check text-success me-2"></i>Instant Wallet Credit</li>
                        <li><i class="fas fa-check text-success me-2"></i>Secure Paystack Payment</li>
                        <li><i class="fas fa-check text-success me-2"></i>24/7 Support</li>
                        <li><i class="fas fa-check text-success me-2"></i>No Hidden Fees</li>
                    </ul>

                    <button class="btn btn-{{ $package['popular'] ? 'success' : 'primary' }} btn-lg 
w-100 select-package" 
                            data-package="{{ $package['id'] }}"
                            data-ranc="{{ $package['ranc_amount'] }}"
                            data-price="{{ $package['price'] }}">
                        <i class="fas fa-shopping-cart me-2"></i>
                        Buy Now
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-credit-card me-2"></i>Complete Your Purchase
                    </h5>
                    <button type="button" class="btn-close btn-close-white" 
data-bs-dismiss="modal"></button>
                </div>
                <form id="paymentForm" method="POST" action="{{ route('paystack.initialize') }}">
                    @csrf
                    <div class="modal-body">
                        <div id="selectedPackage" class="mb-3"></div>
                        
                       <div class="mb-3">
    <label for="email" class="form-label">
        <i class="fas fa-envelope me-2"></i>Email Address
    </label>
    <input 
        type="email" 
        class="form-control" 
        id="email" 
        name="email" 
        required 
        value="{{ Auth::check() ? Auth::user()->email : '' }}" 
        placeholder="your@email.com"
        {{ Auth::check() ? 'readonly' : '' }}
    >
    <div class="form-text">Your wallet will be credited after payment</div>
</div>


                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Secure Payment:</strong> Powered by Paystack with 256-bit SSL 
encryption
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" 
data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success btn-lg" id="payButton">
                            <i class="fas fa-lock me-2"></i>Pay Securely
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="text-center text-muted mt-5 pt-4">
        <p>
            <i class="fas fa-shield-alt me-2"></i>
            Secure payments powered by Paystack
        </p>
        <p class="small">
            Need help? Contact support or use the admin credit system for manual assistance.
        </p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectButtons = document.querySelectorAll('.select-package');
    const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
    const selectedPackageDiv = document.getElementById('selectedPackage');
    
    selectButtons.forEach(button => {
        button.addEventListener('click', function() {
            const packageId = this.dataset.package;
            const rancAmount = this.dataset.ranc;
            const price = this.dataset.price;
            
            selectedPackageDiv.innerHTML = `
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <h6 class="card-title text-uppercase text-primary">Selected Package</h6>
                        <h4 class="text-success mb-2">₦${parseInt(price).toLocaleString()}</h4>
                        <p class="text-muted mb-0">${parseInt(rancAmount).toLocaleString()} RANC 
Credits</p>
                        <input type="hidden" name="package_id" value="${packageId}">
                    </div>
                </div>
            `;
            
            paymentModal.show();
        });
    });

    const paymentForm = document.getElementById('paymentForm');
    const payButton = document.getElementById('payButton');
    
    paymentForm.addEventListener('submit', function() {
        payButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        payButton.disabled = true;
    });
});
</script>

</body>
</html>

