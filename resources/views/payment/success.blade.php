<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success - Credit Purchase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .success-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            background: #fff;
        }
        .success-icon {
            color: #28a745;
            font-size: 4rem;
            margin-bottom: 20px;
        }
        .wallet-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="success-container text-center">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h2 class="text-success mb-3">Payment Successful!</h2>
            <p class="lead">Your wallet has been credited successfully.</p>
            
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check"></i> {{ session('success') }}
                </div>
            @endif
            
            <div class="wallet-info">
                <h5><i class="fas fa-wallet"></i> Your Wallet Balance</h5>
                <p class="h3 text-primary">{{ number_format($wallet->ranc ?? 0) }} RANC Credits</p>
            </div>
            
            <div class="d-grid gap-2 d-md-block">
                <a href="{{ route('pricings.index') }}" class="btn btn-primary me-2">
                    <i class="fas fa-shopping-cart"></i> Buy More Credits
                </a>
                <a href="{{ route('ebooks.index') }}" class="btn btn-success">
                    <i class="fas fa-book"></i> Browse Ebooks
                </a>
            </div>
            
            <hr class="my-4">
            
            <div class="text-muted">
                <small>
                    <i class="fas fa-info-circle"></i>
                    Payment Reference: {{ $paymentReference ?? 'N/A' }}<br>
                    <i class="fas fa-clock"></i>
                    Transaction Date: {{ now()->format('Y-m-d H:i:s') }}
                </small>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>