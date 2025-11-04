@"
<?php
require_once __DIR__ . '/../vendor/stripe-php-14.5.0/init.php';

// Lấy từ biến môi trường, không hardcode key
\Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));

define('STRIPE_PUBLIC_KEY', getenv('STRIPE_PUBLIC_KEY'));

class StripeManager {
    public static function createCheckoutSession($user_id, $plan = 'premium') {
        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => 'EduMatch Premium - ' . ucfirst($plan),
                        ],
                        'unit_amount' => $plan == 'premium' ? 990 : 490,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => 'http://localhost/EduMatch/premium_success.php?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => 'http://localhost/EduMatch/premium_cancel.php',
                'metadata' => [
                    'user_id' => $user_id,
                    'plan' => $plan
                ]
            ]);
            return $session->url;
        } catch (Exception $e) {
            error_log('Stripe Error: ' . $e->getMessage());
            return false;
        }
    }
}
?>
"@ | Out-File -Encoding UTF8 config/stripe.php
