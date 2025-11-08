<script>
function payWithPaystack(packageId) {
    // First, get payment details from backend
    fetch('/packages/initialize-payment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            package_id: packageId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status) {
            const paymentDetails = data.payment_details;
            
            // Initialize Paystack inline payment
            const handler = PaystackPop.setup({
                key: '{{ config("services.paystack.public_key") }}',
                email: paymentDetails.email,
                amount: paymentDetails.amount,
                currency: "NGN",
                ref: paymentDetails.reference,
                metadata: {
                    package_id: packageId,
                    package_name: paymentDetails.package_name,
                    user_id: paymentDetails.user_id,
                    ranc_amount: paymentDetails.ranc_amount
                },
                callback: function(response) {
                    // Payment completed, verify with backend
                    verifyPayment(response.reference);
                },
                onClose: function() {
                    alert('Payment cancelled');
                }
            });
            handler.openIframe();
        } else {
            alert(data.error || 'Payment initialization failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Payment initialization failed');
    });
}

function verifyPayment(reference) {
    fetch('/packages/verify-payment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            reference: reference
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Payment successful - redirect to success page
            window.location.href = '/packages/success?ranc_amount=' + data.ranc_amount + '&package_name=' + encodeURIComponent(data.package_name);
        } else {
            alert(data.error || 'Payment verification failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Payment verification failed');
    });
}
</script>