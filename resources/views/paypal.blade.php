<form action="/payment" method="POST">
    @csrf
    <!-- Change the amount as needed -->
    <input type="hidden" name="plan_id" value="1">
    <input type="hidden" name="email" value="mnadeem00064@gmail.com">
    <input type="hidden" name="first_name" value="muhammad">
    <input type="hidden" name="last_name" value="nadeem">
    <input type="hidden" name="address" value="abcsialkot">
    <button type="submit">Pay with PayPal</button>
</form>
