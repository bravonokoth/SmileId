<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Basic KYC Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Basic KYC Form</h1>

        <form action="{{ url('/basic-kyc') }}" method="POST" class="border p-4 rounded shadow">
            @csrf
            <div class="mb-3">
                <label for="id_number" class="form-label">ID Number:</label>
                <input type="text" class="form-control" name="id_number" required>
            </div>



            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        @if(isset($response))
            <div class="mt-4">
                <h2 class="text-center">Response:</h2>
                <pre class="bg-light p-3 rounded">{{ json_encode($response, JSON_PRETTY_PRINT) }}</pre>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
