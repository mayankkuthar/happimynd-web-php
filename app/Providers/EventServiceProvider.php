<?php

namespace App\Providers;

use App\Models\BundleStatus;
use App\Models\VerifyUser;
use App\Models\Assessment;
use App\Models\AssessmentApprove;
use App\Models\Batch;
use App\Models\BatchCategory;
use App\Models\Question;
use App\Models\RaiseQuery;
use App\Models\UserProfile;
use App\Observers\RaiseQueryObserver;
use App\Observers\BundleStatusObserver;
use App\Observers\VerifyUserObserver;
use App\Observers\AssessmentObserver;
use App\Observers\AssessmentApproveObserver;
use App\Observers\BatchCategoryObserver;
use App\Observers\BatchObserver;
use App\Observers\QuestionObserver;
use App\Observers\UserProfileObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        VerifyUser::observe(VerifyUserObserver::class);
        BundleStatus::observe(BundleStatusObserver::class);
        Assessment::observe(AssessmentObserver::class);
        AssessmentApprove::observe(AssessmentApproveObserver::class);
        RaiseQuery::observe(RaiseQueryObserver::class);
        BatchCategory::observe(BatchCategoryObserver::class);
        Batch::observe(BatchObserver::class);
        Question::observe(QuestionObserver::class);
        UserProfile::observe(UserProfileObserver::class);
    }
}
