<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KRA PIN Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 600px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">KRA PIN Verification</h1>

        <form id="kra-pin-form" class="border p-4 rounded shadow" method="POST" action="{{ route('verifyKraPin') }}">
            @csrf
            <div class="mb-3">
                <label for="kra_pin" class="form-label">KRA PIN:</label>
                <input type="text" class="form-control" name="kra_pin" id="kra_pin" required maxlength="10" placeholder="Enter your KRA PIN">
            </div>

            <button type="submit" class="btn btn-primary w-100">Verify KRA PIN</button>
        </form>

        @if(isset($response))
            <div class="mt-4">
                <h2 class="text-center">Verification Result:</h2>
                @if(isset($success) && $success)
                    <div class="alert alert-success">
                        <strong>Verification Successful!</strong>
                        <p><strong>Business Name:</strong> {{ $business_name }}</p>
                        <p><strong>ID Type:</strong> {{ $response['IDType'] }}</p>
                        <p><strong>ID Number:</strong> {{ $response['IDNumber'] }}</p>
                        <p><strong>Country:</strong> {{ $response['Country'] }}</p>
                        <!-- Add more fields as necessary -->
                    </div>
                @else
                    <div class="alert alert-danger">
                        <strong>Verification Failed!</strong>
                        <p><strong>Error Message:</strong> {{ $error_message ?? $response['message'] ?? 'Unknown error' }}</p>
                        <p><strong>Error Code:</strong> {{ $error_code ?? 'N/A' }}</p>
                    </div>
                @endif
            </div>
        @endif

        @if(isset($error))
            <div class="mt-4">
                <div class="alert alert-danger">
                    <strong>Error:</strong> {{ $error }}
                </div>
            </div>
        @endif

    </div>
</body>
</html>
