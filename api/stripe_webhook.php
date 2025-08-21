<?php
// This webhook endpoint is deprecated as client-side finalization is now used.
// All payment finalization logic has been moved to PaymentController::clientSideFinalizePayment.

http_response_code(200);
echo "Webhook deprecated.";