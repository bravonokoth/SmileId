KRA PIN Verification Project
This project provides a simple interface for verifying KRA PINs using the Smile ID API. It uses JavaScript (jQuery and CryptoJS) for client-side functionality and connects to the Smile ID API for backend processing.

Features:
User-friendly form for entering KRA PIN and ID/Alien Card Number.
Secure signature generation using HMAC-SHA256.
Integration with Smile ID's KRA PIN verification API endpoint.
Requirements:
API Key and Partner ID from Smile ID.
Smile ID KRA PIN API URL: https://testapi.smileidentity.com/v1/kra-pin/verify.

Usage:
Clone the repository and open the index.html file in a browser.
Enter the KRA PIN and ID/Alien Card Number.
Click Verify to initiate the API request.
View the results in the console.

Notes:
Replace placeholders for API Key and Partner ID in the JavaScript file before deployment.
Ensure secure handling of sensitive data in production.
Technologies:
Frontend: HTML, CSS, JavaScript (jQuery, CryptoJS).



http://127.0.0.1:8000/kra-verification
