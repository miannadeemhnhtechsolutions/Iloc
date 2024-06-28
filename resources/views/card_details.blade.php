<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Payment Form</title>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body>
<form id="payment-form">
    <div id="card-element"><!-- Stripe.js injects the Card Element --></div>
    <button id="submit">
        <span id="button-text">Pay Now</span>
    </button>
    <div id="payment-result"></div>
</form>

<script>
    var stripe = Stripe('{{ env("STRIPE_KEY") }}');
    var elements = stripe.elements();
    var cardElement = elements.create('card');
    cardElement.mount('#card-element');

    var form = document.getElementById('payment-form');
    var resultContainer = document.getElementById('payment-result');

    form.addEventListener('submit', function(event) {
        event.preventDefault();

        document.getElementById('submit').disabled = true;
        document.getElementById('button-text').innerText = 'Processing...';


        stripe.createPaymentMethod('card', cardElement).then(function(result) {
            if (result.error) {

                resultContainer.textContent = result.error.message;

                document.getElementById('submit').disabled = false;
                document.getElementById('button-text').innerText = 'Pay Now';
            } else {

                var paymentMethodId = result.paymentMethod.id;


                fetch('/subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        payment_method: paymentMethodId,
                        plan_id: 1,
                        first_name: "muhammad",
                        last_name: 'nadeem',
                        email: "mnadeem00064@gmail.com",
                        address: "abcxyzSialkot"
                    })
                })
                    .then(response => response.json())
                    .then(data => {

                    })
                    .catch(error => {
                        console.error('Error:', error);
                        resultContainer.textContent = 'An error occurred. Please try again.';
                    })
                    .finally(() => {

                        document.getElementById('submit').disabled = false;
                        document.getElementById('button-text').innerText = 'Pay Now';
                    });
            }
        });
    });
</script>
</body>
</html>
