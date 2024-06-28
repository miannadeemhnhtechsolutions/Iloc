<form action="/payment" method="POST">
    @csrf
    <!-- Change the amount as needed -->
    <input type="hidden" name="plan_id" value="1">
    <button type="submit">Pay with PayPal</button>
</form>
