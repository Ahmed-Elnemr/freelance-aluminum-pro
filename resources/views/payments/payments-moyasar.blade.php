<html>
<link rel="stylesheet" href="https://cdn.moyasar.com/mpf/1.5.6/moyasar.css">
<div class="mysr-form"></div>

<script src="https://cdn.moyasar.com/mpf/1.5.6/moyasar.js"></script>
<script>
    Moyasar.init({
        element: '.mysr-form',
        amount: parseFloat("{{ $finalPrice * 100 }}"),
        currency: 'SAR',
        description: "{{ __('Payment for maintenance #: ') . $maintenance->id }}",
            publishable_api_key: 'pk_test_3XBxAM3Mb6e7qALnJGvKaw4WYyrB9k3YPdXQcwHb',
        callback_url: "{{ route('payment.callback') }}?maintenance_id={{ $maintenance->id }}&user_id={{ request()->user_id }}",
        methods: ['creditcard'],
    });
</script>

</html>

