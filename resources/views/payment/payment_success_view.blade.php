<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success - {{ config('app.name') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .success-container {
            background: white;
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
        }

        .success-icon {
            font-size: 4rem;
            color: #4CAF50;
            margin-bottom: 30px;
        }

        .success-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
        }

        .success-message {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .wallet-balance {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
            border-left: 5px solid #667eea;
        }

        .wallet-balance h4 {
            color: #333;
            margin-bottom: 10px;
        }

        .balance-amount {
            font-size: 2rem;
            font-weight: 800;
            color: #667eea;
        }

        .action-buttons {
            margin-top: 40px;
        }

        .btn {
            display: inline-block;
            padding: 15px 30px;
            margin: 10px;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #f8f9fa;
            color: #333;
            border: 2px solid #e0e0e0;
        }

        .btn-secondary:hover {
            background: #e0e0e0;
            transform: translateY(-2px);
        }

        .celebration {
            animation: celebration 1s ease-in-out;
        }

        @keyframes celebration {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="success-container celebration">
        <div class="success-icon">✅</div>
        
        <h1 class="success-title">Payment Successful!</h1>
        
        <p class="success-message">
            Your wallet has been successfully credited. Thank you for your purchase!
        </p>
        
        <div class="wallet-balance">
            <h4>Current Wallet Balance</h4>
            <div class="balance-amount">
                ₦{{ number_format($walletBalance ?? 0, 2) }}
            </div>
        </div>
        
        <div class="action-buttons">
            <a href="{{ route('dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
            <a href="{{ route('account.mywallet') }}" class="btn btn-secondary">View Wallet</a>
        </div>
    </div>

    <script>
        // Auto-redirect after 10 seconds
        setTimeout(() => {
            window.location.href = '{{ route("dashboard") }}';
        }, 10000);
    </script>
</body>
</html>