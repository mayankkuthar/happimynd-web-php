<form method="POST" action="https://api.razorpay.com/v1/checkout/embedded" id="orderform">
    <input type="hidden" name="key_id" value="{{ env('RAZORPAY_KEY') }}">
    <input type="hidden" name="order_id" value="{{$order->order_id}}">
    <input type="hidden" name="name" value="Happimynd">
    <input type="hidden" name="description" value="Happimynd Buy plans">
    <input type="hidden" name="prefill[name]" value="{{ $user->username }}">
    <input type="hidden" name="prefill[contact]" value="{{ $user->mobile }}">
    <input type="hidden" name="prefill[email]" value="{{ $user->email }}">
    <input type="hidden" name="callback_url" value="{{ $callback_url }}">
    <input type="hidden" name="cancel_url" value="javascript:history.go(-2)">
</form>

<script>document.getElementById("orderform").submit();</script>
