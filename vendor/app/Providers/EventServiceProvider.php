<?php

namespace App\Providers;

use App\Events\AcceptedOrderEvent;
use App\Events\BookingAcceptanceStatusChangeEvent;
use App\Events\BookingCollectionStatusChangeEvent;
use App\Events\ConversationInitializationEvent;
use App\Events\DeliveryStatusUpdatesEvent;
use App\Events\DropOffConfirmationEvent;
use App\Events\FailedBookingEvent;
use App\Events\PendingAcceptanceBookingsRecordingEvent;
use App\Events\PendingPickupBookingsRecordingEvent;
use App\Events\PickUpConfirmationEvent;
use App\Events\SuccessfulBookingEvent;
use App\Events\FailedCollectionEvent;
use App\Events\FailedDeliveryEvent;
use App\Events\IncentiveStatusChangeEvent;
use App\Events\OrderAcceptedEvent;
use App\Events\OrderPaymentInitializationEvent;
use App\Events\ProductListingIncentiveEvent;
use App\Events\ProductRejectionEvent;
use App\Events\ProductUpdatedEvent;
use App\Events\ReferralRewardEvent;
use App\Events\RejectedOrderEvent;
use App\Events\SuccessfulOrderPlacementEvent;
use App\Events\SuccessfulDisbursementEvent;
use App\Events\SuccessfulOrderReversalEvent;
use App\Events\UpdateCategoryPercentageEvent;
use App\Events\VendorPayoutEvent;
use App\Listeners\AcceptedOrderListener;
use App\Listeners\BookingAcceptanceStatusChangeListener;
use App\Listeners\BookingCollectionStatusChangeListener;
use App\Listeners\ConversationInitializationListener;
use App\Listeners\DeliveryStatusUpdatesListener;
use App\Listeners\DropOffConfirmationListener;
use App\Listeners\FailedBookingListener;
use App\Listeners\PendingAcceptanceBookingsRecordingListener;
use App\Listeners\PendingPickupBookingsRecordingListener;
use App\Listeners\PickUpConfirmationListener;
use App\Listeners\SuccessfulBookingListener;
use App\Listeners\FailedCollectionListener;
use App\Listeners\FailedDeliveryListener;
use App\Listeners\IncentiveStatusChangeListener;
use App\Listeners\OrderAcceptedListener;
use App\Listeners\OrderPaymentInitializationListener;
use App\Listeners\ProductListingIncentiveListener;
use App\Listeners\ProductRejectionListener;
use App\Listeners\ProductUpdatedListener;
use App\Listeners\ReferralRewardListener;
use App\Listeners\RejectedOrderListener;
use App\Listeners\SuccessfulOrderPlacementListener;
use App\Listeners\SuccessfulDisbursementListener;
use App\Listeners\SuccessfulOrderReversalListener;
use App\Listeners\UpdateCategoryPercentageListener;
use App\Listeners\VendorPayoutListener;
use App\Models\Booking;
use App\Models\BookingProductExchangeSchedule;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Conversation;
use App\Models\Delivery;
use App\Models\DeliveryFee;
use App\Models\Incentive;
use App\Models\Message;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductAddress;
use App\Models\ProductPhoto;
use App\Models\ProductPrice;
use App\Models\ProductTag;
use App\Models\Report;
use App\Models\Review;
use App\Models\Setting;
use App\Models\Size;
use App\Models\SubCategory;
use App\Models\Vendor;
use App\Models\VendorAvailability;
use App\Models\UserStatistics;
use App\Observers\BookingObserver;
use App\Observers\BookingProductExchangeScheduleObserver;
use App\Observers\CartObserver;
use App\Observers\CategoryObserver;
use App\Observers\ConversationObserver;
use App\Observers\DeliveryFeeObserver;
use App\Observers\DeliveryObserver;
use App\Observers\IncentiveObserver;
use App\Observers\MessageObserver;
use App\Observers\OrderObserver;
use App\Observers\ProductAddressObserver;
use App\Observers\ProductObserver;
use App\Observers\ProductPhotoObserver;
use App\Observers\ProductPriceObserver;
use App\Observers\ProductTagObserver;
use App\Observers\ReportObserver;
use App\Observers\ReviewObserver;
use App\Observers\SettingOberver;
use App\Observers\SizeObserver;
use App\Observers\SubCategoryObserver;
use App\Observers\VendorAvailabilityObserver;
use App\Observers\VendorObserver;
use App\Observers\UserStatisticsObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UpdateCategoryPercentageEvent::class => [
            UpdateCategoryPercentageListener::class,
        ],
        SuccessfulOrderPlacementEvent::class => [
            SuccessfulOrderPlacementListener::class
        ],
        ProductUpdatedEvent::class => [
            ProductUpdatedListener::class
        ],
        VendorPayoutEvent::class => [
            VendorPayoutListener::class
        ],
        FailedDeliveryEvent::class => [
            FailedDeliveryListener::class
        ],
        RejectedOrderEvent::class => [
            RejectedOrderListener::class
        ],
        FailedCollectionEvent::class => [
            FailedCollectionListener::class
        ],
        AcceptedOrderEvent::class => [
            AcceptedOrderListener::class
        ],
        DeliveryStatusUpdatesEvent::class => [
            DeliveryStatusUpdatesListener::class
        ],
        SuccessfulDisbursementEvent::class => [
            SuccessfulDisbursementListener::class
        ],
        ProductRejectionEvent::class => [
            ProductRejectionListener::class
        ],
        ReferralRewardEvent::class => [
            ReferralRewardListener::class
        ],
        SuccessfulOrderReversalEvent::class => [
            SuccessfulOrderReversalListener::class
        ],
        OrderPaymentInitializationEvent::class => [
            OrderPaymentInitializationListener::class
        ],
        ProductListingIncentiveEvent::class => [
            ProductListingIncentiveListener::class
        ],
        IncentiveStatusChangeEvent::class => [
            IncentiveStatusChangeListener::class
        ],
        ConversationInitializationEvent::class => [
            ConversationInitializationListener::class
        ],
        BookingAcceptanceStatusChangeEvent::class => [
            BookingAcceptanceStatusChangeListener::class
        ],
        BookingCollectionStatusChangeEvent::class => [
            BookingCollectionStatusChangeListener::class
        ],
        SuccessfulBookingEvent::class => [
            SuccessfulBookingListener::class
        ],
        PickUpConfirmationEvent::class => [
            PickUpConfirmationListener::class
        ],
        DropOffConfirmationEvent::class => [
            DropOffConfirmationListener::class
        ],
        FailedBookingEvent::class => [
            FailedBookingListener::class
        ],
        PendingPickupBookingsRecordingEvent::class => [
            PendingPickupBookingsRecordingListener::class
        ],
        PendingAcceptanceBookingsRecordingEvent::class => [
            PendingAcceptanceBookingsRecordingListener::class
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Category::observe(CategoryObserver::class);
        Vendor::observe(VendorObserver::class);
        SubCategory::observe(SubCategoryObserver::class);
        Product::observe(ProductObserver::class);
        ProductTag::observe(ProductTagObserver::class);
        ProductPhoto::observe(ProductPhotoObserver::class);
        Cart::observe(CartObserver::class);
        Order::observe(OrderObserver::class);
        VendorAvailability::observe(VendorAvailabilityObserver::class);
        Delivery::observe(DeliveryObserver::class);
        DeliveryFee::observe(DeliveryFeeObserver::class);
        Size::observe(SizeObserver::class);
        Report::observe(ReportObserver::class);
        UserStatistics::observe(UserStatisticsObserver::class);
        Review::observe(ReviewObserver::class);
        Setting::observe(SettingOberver::class);
        Incentive::observe(IncentiveObserver::class);
        ProductPrice::observe(ProductPriceObserver::class);
        Booking::observe(BookingObserver::class);
        Conversation::observe(ConversationObserver::class);
        Message::observe(MessageObserver::class);
        ProductAddress::observe(ProductAddressObserver::class);
        BookingProductExchangeSchedule::observe(BookingProductExchangeScheduleObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
