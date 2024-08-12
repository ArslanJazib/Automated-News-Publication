<?php

namespace StripeSubscriptionManager;

use StripeSubscriptionManager\Models\SubscriptionModel;

class Initializer
{
    public static function activate()
    {
        // Activation code here
        SubscriptionModel::createSubscriptionTable();

    }

    public static function deactivate()
    {
        // Deactivation code here
    }

    /**
     * store full list of classes
     * @return array
     */
    public static function get_classes(): array
    {
        return [
            Controllers\SubscriptionController::class,
            Controllers\PlansController::class,
            Controllers\CustomerRegistrationController::class,
            Controllers\StripeWebhookHandler::class,
        ];
    }


    /**
     * loop through the classes, initialize and call register meth if exist
     * @return void
     */
    public static function register_classes(): void
    {
        foreach (self::get_classes() as $class) {
            $service = self::instantiate($class);
            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }
	/**
	 * Initialize the class
	 *
	 * @param $class
	 *
	 */
	private static function instantiate( $class) {
		return new $class();
	}


}
