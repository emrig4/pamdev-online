<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - RANC Credits</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
rel="stylesheet">
    <style>
        .success-animation {
            animation: successBounce 1s ease-in-out;
        }
        @keyframes successBounce {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
        .success-icon {
            font-size: 5rem;
            color: #28a745;
            animation: successBounce 1s ease-in-out;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <div class="success-animation">
                    <i class="fas fa-check-circle success-icon"></i>
                </div>
                <h1 class="display-4 fw-bold text-success mt-4">Payment Successful!</h1>
                <p class="lead text-muted">Your wallet has been credited successfully</p>
            </div>

            <div class="row g-4">
                <div class="col-md-8 mx-auto">
                    <div class="card border-success">
                        <div class="card-body text-center p-4">
                            <h5 class="card-title text-success">
                                <i class="fas fa-wallet me-2"></i>
                                Wallet Credit Confirmed
                            </h5>
                            
                            <div class="row text-center mt-4">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center 
mb-3">
                                        <span class="text-muted">Credits Added:</span>
                                        <h4 class="text-success mb-0">10,000 RANC</h4>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center 
mb-3">
                                        <span class="text-muted">Payment Status:</span>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Completed
                                        </span>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">Wallet Balance:</span>
                                        <span class="text-primary fw-bold">Updated</span>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-success mt-4 mb-4">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Next Steps:</strong>
                                <ul class="text-start mt-2 mb-0">
                                    <li>Your RANC credits are now available in your wallet</li>
                                    <li>You can use these credits for your research activities</li>
                                    <li>Keep this receipt for your records</li>
                                </ul>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                <a href="{{ url('/') }}" class="btn btn-primary btn-lg me-md-2">
                                    <i class="fas fa-home me-2"></i>Go to Dashboard
                                </a>
                                <a href="{{ route('packages.index') }}" class="btn btn-outline-primary 
btn-lg">
                                    <i class="fas fa-plus me-2"></i>Buy More Credits
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted">
                    <i class="fas fa-shield-alt me-2"></i>
                    Transaction secured by Paystack
                </p>
                <small class="text-muted">
                    Need assistance? Contact support or use the admin credit system for manual 
support.
                </small>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
setTimeout(function() {
    if (confirm('Redirect to dashboard in 5 seconds. Click OK to go now.')) {
        window.location.href = '{{ url('/') }}';
    }
}, 30000);
</script>

</body>
</html>

