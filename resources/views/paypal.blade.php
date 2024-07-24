<form action="/payment" method="POST">
    @csrf
    <!-- Change the amount as needed -->
    <input type="hidden" name="plan_id" value="1">
    <input type="hidden" name="email" value="mnadeem00064@gmail.com">
    <input type="hidden" name="name" value="muhammad">
    <input type="hidden" name="city" value="punslk">
    <input type="hidden" name="state" value="punjab hgh">
    <input type="hidden" name="address" value="abcsialkot hbhhj">
    <button type="submit">Pay with PayPal</button>
</form>
