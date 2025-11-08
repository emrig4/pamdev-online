<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Plans - {{ config('app.name') }}</title>
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
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 50px;
        }

        .header h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .header p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }

        .packages-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .package-card {
            background: white;
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .package-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px rgba(0,0,0,0.15);
        }

        .package-card.popular {
            transform: scale(1.05);
            border: 3px solid #667eea;
        }

        .package-card.popular::before {
            content: 'Most Popular';
            position: absolute;
            top: 20px;
            right: -30px;
            background: #667eea;
            color: white;
            padding: 8px 40px;
            font-size: 12px;
            font-weight: bold;
            transform: rotate(45deg);
        }

        .package-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .package-name {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .package-price {
            font-size: 3rem;
            font-weight: 800;
            color: #667eea;
            margin-bottom: 10px;
        }

        .package-price .currency {
            font-size: 1.5rem;
            vertical-align: top;
        }

        .package-description {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        .features-list {
            list-style: none;
            margin-bottom: 40px;
        }

        .features-list li {
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
        }

        .features-list li:last-child {
            border-bottom: none;
        }

        .features-list li::before {
            content: '✓';
            color: #4CAF50;
            font-weight: bold;
            margin-right: 15px;
            font-size: 1.2rem;
        }

        .purchase-btn {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 18px 30px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .purchase-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .purchase-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 40px;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            position: relative;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        }

        .close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 28px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
        }

        .close:hover {
            color: #333;
        }

        .payment-form {
            text-align: center;
        }

        .form-group {
            margin-bottom: 25px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .loading {
            display: none;
            text-align: center;
            color: #666;
        }

        .success-message {
            display: none;
            text-align: center;
            color: #4CAF50;
            font-size: 1.1rem;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }
            
            .packages-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .package-card.popular {
                transform: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Choose Your Plan</h1>
            <p>Select the perfect package for your needs. All plans include our core features with varying levels of access and support.</p>
        </div>

        <div class="packages-grid">
            @foreach($packages as $package)
                <div class="package-card {{ $package['id'] == 'standard' ? 'popular' : '' }}">
                    <div class="package-header">
                        <h3 class="package-name">{{ $package['name'] }}</h3>
                        <div class="package-price">
                            <span class="currency">₦</span>{{ number_format($package['amount'] / 100) }}
                        </div>
                        <p class="package-description">{{ $package['description'] }}</p>
                    </div>

                    <ul class="features-list">
                        @foreach($package['features'] as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    </ul>

                    <button class="purchase-btn" onclick="purchasePackage('{{ $package['id'] }}', {{ $package['amount'] }}, '{{ $package['name'] }}')">
                        Get Started
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <div class="payment-form">
                <h3 id="modalTitle">Complete Your Purchase</h3>
                <p id="modalDescription" style="color: #666; margin-bottom: 30px;"></p>
                
                <form id="paymentForm">
                    <div class="form-group">
                        <label for="customerEmail">Email Address</label>
                        <input type="email" id="customerEmail" value="{{ auth()->user()->email ?? '' }}" required>
                    </div>
                    
                    <button type="submit" class="purchase-btn" id="payBtn">
                        Pay with Paystack
                    </button>
                </form>
                
                <div class="loading" id="loadingMessage">
                    <p>🔄 Processing payment...</p>
                </div>
                
                <div class="success-message" id="successMessage">
                    <p>✅ Payment successful! Your wallet has been credited.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Paystack Inline Script -->
    {!! $paystackScript ?? '' !!}

    <script>
        let currentPackage = null;

        function purchasePackage(packageId, amount, name) {
            currentPackage = { id: packageId, amount: amount, name: name };
            
            // Show modal
            document.getElementById('paymentModal').style.display = 'block';
            document.getElementById('modalTitle').textContent = `Purchase ${name}`;
            document.getElementById('modalDescription').textContent = `You're about to purchase ${name} for ₦${(amount/100).toLocaleString()}`;
            
            // Reset form
            document.getElementById('paymentForm').style.display = 'block';
            document.getElementById('loadingMessage').style.display = 'none';
            document.getElementById('successMessage').style.display = 'none';
            document.getElementById('payBtn').disabled = false;
            document.getElementById('payBtn').textContent = 'Pay with Paystack';
        }

        function closeModal() {
            document.getElementById('paymentModal').style.display = 'none';
            currentPackage = null;
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('paymentModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        // Handle payment form submission
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!currentPackage) return;
            
            const email = document.getElementById('customerEmail').value;
            const payBtn = document.getElementById('payBtn');
            
            // Show loading state
            payBtn.disabled = true;
            payBtn.textContent = 'Initializing...';
            
            // Initialize payment with Paystack
            fetch('{{ route("packages.initialize") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    package_id: currentPackage.id,
                    email: email
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Initialize Paystack inline payment
                    initializePayment(data.payment_data, function(result) {
                        if (result.success) {
                            // Payment completed successfully
                            handlePaymentSuccess(result.reference);
                        } else {
                            // Payment failed
                            handlePaymentError(result.message || 'Payment failed');
                        }
                    });
                } else {
                    throw new Error(data.error || 'Payment initialization failed');
                }
            })
            .catch(error => {
                handlePaymentError(error.message);
            });
        });

        function initializePayment(paymentData, callback) {
            if (typeof PaystackPop === 'undefined') {
                callback({success: false, message: 'Paystack library not loaded'});
                return;
            }

            const handler = PaystackPop.setup({
                key: '{{ config("paystack.publicKey") }}',
                email: paymentData.email,
                amount: paymentData.amount,
                currency: 'NGN',
                reference: paymentData.reference,
                callback: function(response) {
                    // Payment successful
                    callback({success: true, reference: response.reference});
                },
                onClose: function() {
                    // Payment popup closed
                    callback({success: false, message: 'Payment was cancelled'});
                },
                metadata: paymentData.metadata
            });

            // Open payment popup (inline mode)
            handler.openIframe();
            
            // Update button state
            const payBtn = document.getElementById('payBtn');
            payBtn.textContent = 'Paying...';
        }

        function handlePaymentSuccess(reference) {
            document.getElementById('paymentForm').style.display = 'none';
            document.getElementById('successMessage').style.display = 'block';
            
            setTimeout(() => {
                closeModal();
                // Refresh the page to show updated wallet balance
                window.location.reload();
            }, 3000);
        }

        function handlePaymentError(message) {
            const payBtn = document.getElementById('payBtn');
            payBtn.disabled = false;
            payBtn.textContent = 'Pay with Paystack';
            
            alert('Payment Error: ' + message);
        }
    </script>
</body>
</html>