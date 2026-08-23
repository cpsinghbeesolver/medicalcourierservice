<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // This seeder provides reference data for the subscription plans
        // Actual subscriptions will be created via Stripe webhooks

        $plans = [
            [
                'name' => 'starter',
                'display_name' => 'Founder',
                'price_monthly' => 99.00,
                'price_annual' => 0, // 20% discount
                'min_drivers' => 0,
                'max_drivers' => 2,
                'max_deliveries' => 0,
                'max_users' => 0,
                'max_locations' => 0,
                'data_retention_days' => 2190,
                'features' => [
                    'admin_dashboard',
                    'live_driver_tracking',
                    'chain_of_custody',
                    'hippa_audit_logs',
                    '6_year_complaince',
                    'pdf_exports'
                ],
            ],
            [
                'name' => 'professional',
                'display_name' => 'Professional',
                'price_monthly' => 149.00,
                'price_annual' => 0, // 20% discount
                'max_deliveries' => 0,
                'max_users' => 0,
                'max_locations' => 0,
                'min_drivers' => 3,
                'max_drivers' => 15,
                'data_retention_days' => 2190,
                'features' => [
                    'advanced_medical_suite',
                    'live_client_notificatons',
                    'smart_dispatching',
                    'audit_vault',
                    'priority_support',
                    'hipaa_security'
                ],
            ],
            [
                'name' => 'enterprise',
                'display_name' => 'Enterprise',
                'price_monthly' => null, // Custom pricing
                'price_annual' => null,
                'max_deliveries' => null, // Unlimited
                'max_users' => null, // Unlimited
                'max_locations' => null, // Unlimited
                'data_retention_days' => 2190, // 6 years
                'min_drivers' => 0,
                'max_drivers' => 0,
                'features' => [
                    'white_label_portal',
                    'hospital_client_access',
                    'api_access',
                    'data_retrieval',
                    'account_manager',
                    'custom_wiorkflows'
                ],
            ],
        ];

        // Insert plan reference data
        DB::table('subscription_plans_reference')->delete();
        DB::table('subscription_plans_reference')->insert(array_map(function ($plan) {
            $features = $plan['features'];
            unset($plan['features']);
            $plan['features_json'] = json_encode($features);
            $plan['created_at'] = now();
            $plan['updated_at'] = now();
            return $plan;
        }, $plans));

        $this->command->info('Subscription plans reference data seeded successfully!');
        $this->command->info('Note: Create matching products in Stripe dashboard with metadata.plan_name matching these plan names.');
    }
}
