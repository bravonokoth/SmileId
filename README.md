# SmileId KYC & KRA PIN Verification

A Laravel-based application for identity verification and KRA PIN validation using the Smile Identity API.

## Features

- **Basic KYC Verification**: Verify identity information using the Smile Identity Basic KYC API
- **KRA PIN Verification**: Validate KRA (Kenya Revenue Authority) PIN numbers through secure API integration
- **Secure Signature Generation**: HMAC-SHA256 signature generation for API authentication
- **User-Friendly Interface**: Clean Blade templates for easy identity verification workflows
- **Modern Stack**: Built with Laravel 11, Vite, and Bootstrap

## Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and npm
- Smile Identity API credentials (Partner ID and API Key)
- SQLite or another database (configured in `.env`)

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd SmileId
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Set up environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Update API credentials in controllers**
   - Edit `app/Http/Controllers/KycController.php`
   - Edit `app/Http/Controllers/KraPinVerificationController.php`
   - Replace the `$partnerId` and `$apiKey` with your Smile Identity credentials

6. **Run database migrations**
   ```bash
   php artisan migrate
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

8. **In another terminal, compile frontend assets**
   ```bash
   npm run dev
   ```

The application will be available at `http://127.0.0.1:8000`

## Usage

### Basic KYC Verification

Navigate to the home page (`/`) to access the Basic KYC form. Enter an ID number and submit to verify the identity.

**Endpoint**: `POST /basic-kyc`

### KRA PIN Verification

Visit `/kra-verification` to access the KRA PIN verification form. Enter a valid KRA PIN and ID number, then click Verify.

**Endpoint**: `POST /verify-kra-pin`

## API Integration

Both verification flows follow the same authentication pattern:

1. Generate a timestamp in ISO 8601 format
2. Create a signature using HMAC-SHA256 with your API key
3. Send the request to Smile Identity API with the signature and timestamp

The application handles all signature generation and API communication server-side for security.

## Project Structure

```
SmileId/
├── app/Http/Controllers/
│   ├── KycController.php                    # Basic KYC verification logic
│   └── KraPinVerificationController.php     # KRA PIN verification logic
├── resources/views/
│   ├── basic_kyc.blade.php                  # KYC form template
│   └── kra_verification.blade.php           # KRA PIN form template
├── routes/
│   └── web.php                              # Application routes
├── config/                                  # Configuration files
├── database/                                # Migrations and seeders
└── public/                                  # Static assets
```

## Configuration

API credentials should be securely stored in environment variables rather than hardcoded. Update your `.env` file with:

```env
SMILE_PARTNER_ID=your_partner_id
SMILE_API_KEY=your_api_key
```

Then modify the controllers to use `env()` helper:
```php
$this->partnerId = env('SMILE_PARTNER_ID');
$this->apiKey = env('SMILE_API_KEY');
```

## Testing

Run the test suite with:

```bash
php artisan test
```

For specific tests:
```bash
php artisan test tests/Feature/ExampleTest.php
```

## Deployment

Before deploying to production:

- Ensure API credentials are stored in environment variables
- Update the API endpoints if moving from test to production
- Set `APP_DEBUG=false` in your `.env`
- Run `php artisan config:cache`
- Compile assets with `npm run build`

## Support

For issues or questions:
- Check the [Smile Identity Documentation](https://docs.smileidentity.com/)
- Review the `app/Http/Controllers/` for implementation details
- Check application logs in `storage/logs/`

## License

This project is licensed under the MIT License. See the LICENSE file for details.

## Contributing

Contributions are welcome! Please feel free to submit a pull request with improvements or bug fixes.
