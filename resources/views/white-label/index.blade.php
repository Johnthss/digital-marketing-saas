<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>White-Label Settings</title>
    @if(isset($whiteLabel) && $whiteLabel->custom_css)
        <style nonce="{{ $cspNonce ?? '' }}">{{ $whiteLabel->custom_css }}</style>
    @endif
</head>
<body>
    <div class="container">
        <h1>White-Label Settings</h1>
        <p>Agency ID: {{ $agencyId }}</p>
    </div>
</body>
</html>
