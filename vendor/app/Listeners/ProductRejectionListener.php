<?php

namespace App\Listeners;

use App\Events\ProductRejectionEvent;
use App\Http\Services\GetAuthUserService;
use App\Http\Services\NotificationService;
use App\Models\Product;
use App\Models\ProductRejection;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ProductRejectionListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProductRejectionEvent $event): void
    {
        $product = $event->product;
        logger('### PRODUCT REJECTION EVENT TRIGGERED ###', [$product->status]);
        try {
            self::notifyVendor($product);
        } catch (Exception $exception) {
            report($exception);
        }
    }

    private static function notifyVendor(Product $product): void
    {
        $userExternalId = $product->vendor->external_id;

        if (!is_null($message = self::getMessage($product))) {
            (new NotificationService([
                'userExternalId' => $userExternalId,
                'title' => 'Product Rejection',
                'body' => $message,
                'data' => '',
                'notificationAction' => 'product'
            ]))->sendPush();

            (new NotificationService([
                'userExternalId' => $userExternalId,
                'message' => $message
            ]))->sendSms();
        }
    }

    private static function getMessage(Product $product): ?string
    {
        $reason = $product->rejectionReasons()?->latest()?->first()?->reason;
        $vendor = $product->vendor;

        return match ($reason) {
            ProductRejection::REASONS['EXPLICIT_CONTENT'] => "Hi $vendor->display_name, your product $product->name, has been delisted as it contains explicit content. Kindly ensure all product listings are in line with our content standards and guidelines.",
            ProductRejection::REASONS['INAPPROPRIATE_IMAGE'] => "Hi $vendor->display_name, your product $product->name, has been delisted due to image standards not met.",
            ProductRejection::REASONS['INCORRECT_CATEGORY'] => "Hi $vendor->display_name! Your product listing on Aider couldn't be approved because it was placed in the wrong category. Please select the appropriate category and resubmit.",
            ProductRejection::REASONS['INCORRECT_COLOR_CHOICE'] => "Hi $vendor->display_name! Your product listing was rejected due to an incorrect color choice. Please double-check and resubmit for approval.",
            ProductRejection::REASONS['INCORRECT_CONDITION'] => "Hi $vendor->display_name, Your product listing didn't meet our standards due to an incorrect product condition. Kindly review and update for approval.",
            ProductRejection::REASONS['INCORRECT_DESCRIPTION'] => "Hi $vendor->display_name! Your product description does not match the product listed. Please revise and re-upload for approval.",
            ProductRejection::REASONS['INCORRECT_NAME'] => "Hi $vendor->display_name! Your product listing on Aider could not be approved as the product name provided is incorrect. Kindly review and resubmit.",
            ProductRejection::REASONS['INCORRECT_SIZE'] => "Hi $vendor->display_name! Your product listing on Aider couldn't be approved as the size provided is incorrect. Please verify and resubmit for review.",
            default => null
        };
    }

}
