<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionFeature;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    /**
     * Initialize Stripe API key
     */
    public function __construct()
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
    }

    /**
     * @OA\Post(
     *     path="/stripe/webhook",
     *     summary="Stripe webhook endpoint for subscription sync",
     *     tags={"Stripe Webhooks"},
     *     description="Receives webhook events from Stripe to sync subscription data. This endpoint is called by Stripe, not by your application.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Stripe webhook payload with signature",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="string", example="evt_1ABC123"),
     *             @OA\Property(property="type", type="string", example="customer.subscription.created"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Webhook processed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success")
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid payload or signature")
     * )
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe webhook invalid payload: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'customer.subscription.created':
                $this->handleSubscriptionCreated($event->data->object);
                break;
            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($event->data->object);
                break;
            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;
            case 'invoice.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                break;
            case 'invoice.payment_succeeded':
                $this->handlePaymentSucceeded($event->data->object);
                break;
            default:
                Log::info('Unhandled Stripe webhook event: ' . $event->type);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle subscription created event
     */
    protected function handleSubscriptionCreated($stripeSubscription)
    {
        Log::info('Processing subscription.created', ['subscription_id' => $stripeSubscription->id]);

        // Get customer email from Stripe
        $customer = \Stripe\Customer::retrieve($stripeSubscription->customer);

        // Find or create user
        $user = User::where('email', $customer->email)->first();

        if (!$user) {
            Log::warning('User not found for subscription', [
                'email' => $customer->email,
                'stripe_customer_id' => $stripeSubscription->customer
            ]);
            return;
        }

        // Get product and price details
        $priceId = $stripeSubscription->items->data[0]->price->id;
        $price = \Stripe\Price::retrieve($priceId);
        $product = \Stripe\Product::retrieve($price->product);

        // Determine plan name from product metadata or name
        $planName = strtolower($product->metadata->plan_name ?? $product->name);
        $planName = $this->normalizePlanName($planName);

        // Create subscription record
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'tenant_id' => $user->tenant_id,
            'stripe_id' => $stripeSubscription->id,
            'stripe_customer_id' => $stripeSubscription->customer,
            'stripe_price_id' => $priceId,
            'stripe_product_id' => $price->product,
            'plan_name' => $planName,
            'plan_display_name' => $product->name,
            'plan_price' => $price->unit_amount / 100, // Convert cents to dollars
            'billing_cycle' => $price->recurring->interval === 'year' ? 'annual' : 'monthly',
            'status' => $stripeSubscription->status,
            'trial_ends_at' => $stripeSubscription->trial_end ? date('Y-m-d H:i:s', $stripeSubscription->trial_end) : null,
            'current_period_start' => date('Y-m-d H:i:s', $stripeSubscription->current_period_start),
            'current_period_end' => date('Y-m-d H:i:s', $stripeSubscription->current_period_end),
            'max_deliveries' => $this->getMaxDeliveries($planName),
            'max_users' => $this->getMaxUsers($planName),
            'max_locations' => $this->getMaxLocations($planName),
            'data_retention_days' => $this->getDataRetentionDays($planName),
        ]);

        // Assign features based on plan
        $this->assignPlanFeatures($subscription, $planName);

        Log::info('Subscription created successfully', ['subscription_id' => $subscription->id]);
    }

    /**
     * Handle subscription updated event
     */
    protected function handleSubscriptionUpdated($stripeSubscription)
    {
        Log::info('Processing subscription.updated', ['subscription_id' => $stripeSubscription->id]);

        $subscription = Subscription::where('stripe_id', $stripeSubscription->id)->first();

        if (!$subscription) {
            Log::warning('Subscription not found for update', ['stripe_id' => $stripeSubscription->id]);
            return;
        }

        // Get latest price details in case plan changed
        $priceId = $stripeSubscription->items->data[0]->price->id;
        $price = \Stripe\Price::retrieve($priceId);
        $product = \Stripe\Product::retrieve($price->product);

        $planName = strtolower($product->metadata->plan_name ?? $product->name);
        $planName = $this->normalizePlanName($planName);

        // Update subscription
        $subscription->update([
            'stripe_price_id' => $priceId,
            'stripe_product_id' => $price->product,
            'plan_name' => $planName,
            'plan_display_name' => $product->name,
            'plan_price' => $price->unit_amount / 100,
            'billing_cycle' => $price->recurring->interval === 'year' ? 'annual' : 'monthly',
            'status' => $stripeSubscription->status,
            'trial_ends_at' => $stripeSubscription->trial_end ? date('Y-m-d H:i:s', $stripeSubscription->trial_end) : null,
            'current_period_start' => date('Y-m-d H:i:s', $stripeSubscription->current_period_start),
            'current_period_end' => date('Y-m-d H:i:s', $stripeSubscription->current_period_end),
            'cancelled_at' => $stripeSubscription->canceled_at ? date('Y-m-d H:i:s', $stripeSubscription->canceled_at) : null,
            'ends_at' => $stripeSubscription->cancel_at ? date('Y-m-d H:i:s', $stripeSubscription->cancel_at) : null,
            'max_deliveries' => $this->getMaxDeliveries($planName),
            'max_users' => $this->getMaxUsers($planName),
            'max_locations' => $this->getMaxLocations($planName),
            'data_retention_days' => $this->getDataRetentionDays($planName),
        ]);

        // Update features if plan changed
        if ($subscription->wasChanged('plan_name')) {
            $subscription->features()->delete();
            $this->assignPlanFeatures($subscription, $planName);
        }

        Log::info('Subscription updated successfully', ['subscription_id' => $subscription->id]);
    }

    /**
     * Handle subscription deleted event
     */
    protected function handleSubscriptionDeleted($stripeSubscription)
    {
        Log::info('Processing subscription.deleted', ['subscription_id' => $stripeSubscription->id]);

        $subscription = Subscription::where('stripe_id', $stripeSubscription->id)->first();

        if (!$subscription) {
            Log::warning('Subscription not found for deletion', ['stripe_id' => $stripeSubscription->id]);
            return;
        }

        $subscription->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'ends_at' => now(),
        ]);

        Log::info('Subscription cancelled successfully', ['subscription_id' => $subscription->id]);
    }

    /**
     * Handle payment failed event
     */
    protected function handlePaymentFailed($invoice)
    {
        Log::info('Processing invoice.payment_failed', ['invoice_id' => $invoice->id]);

        if (!$invoice->subscription) {
            return;
        }

        $subscription = Subscription::where('stripe_id', $invoice->subscription)->first();

        if (!$subscription) {
            return;
        }

        $subscription->update([
            'status' => 'past_due',
        ]);

        // TODO: Send email notification to user about payment failure

        Log::info('Subscription marked as past_due', ['subscription_id' => $subscription->id]);
    }

    /**
     * Handle payment succeeded event
     */
    protected function handlePaymentSucceeded($invoice)
    {
        Log::info('Processing invoice.payment_succeeded', ['invoice_id' => $invoice->id]);

        if (!$invoice->subscription) {
            return;
        }

        $subscription = Subscription::where('stripe_id', $invoice->subscription)->first();

        if (!$subscription) {
            return;
        }

        // Update subscription to active if it was past_due
        if ($subscription->status === 'past_due') {
            $subscription->update([
                'status' => 'active',
            ]);

            Log::info('Subscription reactivated after payment', ['subscription_id' => $subscription->id]);
        }
    }

    /**
     * Normalize plan name from Stripe product
     */
    protected function normalizePlanName(string $name): string
    {
        $name = strtolower($name);

        if (str_contains($name, 'starter') || str_contains($name, 'clinic')) {
            return 'starter';
        }

        if (str_contains($name, 'professional') || str_contains($name, 'pro')) {
            return 'professional';
        }

        if (str_contains($name, 'enterprise')) {
            return 'enterprise';
        }

        return 'starter'; // Default fallback
    }

    /**
     * Get max deliveries based on plan
     */
    protected function getMaxDeliveries(string $planName): ?int
    {
        return match($planName) {
            'starter' => 500,
            'professional' => 2500,
            'enterprise' => null, // Unlimited
            default => 500,
        };
    }

    /**
     * Get max users based on plan
     */
    protected function getMaxUsers(string $planName): ?int
    {
        return match($planName) {
            'starter' => 5,
            'professional' => 25,
            'enterprise' => null, // Unlimited
            default => 5,
        };
    }

    /**
     * Get max locations based on plan
     */
    protected function getMaxLocations(string $planName): ?int
    {
        return match($planName) {
            'starter' => 1,
            'professional' => 10,
            'enterprise' => null, // Unlimited
            default => 1,
        };
    }

    /**
     * Get data retention days based on plan
     */
    protected function getDataRetentionDays(string $planName): int
    {
        return match($planName) {
            'starter' => 30,
            'professional' => 365,
            'enterprise' => 730, // 2 years
            default => 30,
        };
    }

    /**
     * Assign features to subscription based on plan
     */
    protected function assignPlanFeatures(Subscription $subscription, string $planName)
    {
        $features = $this->getPlanFeatures($planName);

        foreach ($features as $featureKey => $isEnabled) {
            SubscriptionFeature::create([
                'subscription_id' => $subscription->id,
                'feature_key' => $featureKey,
                'is_enabled' => $isEnabled,
            ]);
        }
    }

    /**
     * Get feature configuration for each plan
     */
    protected function getPlanFeatures(string $planName): array
    {
        $allFeatures = [
            'live_gps' => false,
            'custom_reports' => false,
            'photo_verification' => false,
            'multi_location' => false,
            'api_access' => false,
            'push_notifications' => false,
            'barcode_scanning' => false,
            'temperature_tracking' => false,
            'hipaa_audit_logs' => false,
            'white_label' => false,
            'sso' => false,
            'priority_support' => false,
            'bio_hazard_tracking' => false,
            'automated_compliance' => false,
        ];

        return match($planName) {
            'starter' => array_merge($allFeatures, [
                'push_notifications' => true,
                'photo_verification' => true,
            ]),
            'professional' => array_merge($allFeatures, [
                'live_gps' => true,
                'custom_reports' => true,
                'photo_verification' => true,
                'multi_location' => true,
                'api_access' => true,
                'push_notifications' => true,
                'barcode_scanning' => true,
                'temperature_tracking' => true,
            ]),
            'enterprise' => array_merge($allFeatures, [
                'live_gps' => true,
                'custom_reports' => true,
                'photo_verification' => true,
                'multi_location' => true,
                'api_access' => true,
                'push_notifications' => true,
                'barcode_scanning' => true,
                'temperature_tracking' => true,
                'hipaa_audit_logs' => true,
                'white_label' => true,
                'sso' => true,
                'priority_support' => true,
                'bio_hazard_tracking' => true,
                'automated_compliance' => true,
            ]),
            default => $allFeatures,
        };
    }
}
