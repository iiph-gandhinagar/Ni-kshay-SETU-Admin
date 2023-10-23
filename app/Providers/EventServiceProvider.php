<?php

namespace App\Providers;

use App\Models\Assessment;
use App\Models\FlashNews;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Observers\SubscriberActivityObserver;
use App\Models\SubscriberActivity;
use App\Observers\FlashNewsObserver;
use App\Observers\LbSubModuleUsageObserver;
use App\Models\LbSubModuleUsage;
use App\Observers\AssessmentObserver;


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
        LbSubModuleUsage::observe(LbSubModuleUsageObserver::class);
        SubscriberActivity::observe(SubscriberActivityObserver::class);
        Assessment::observe(AssessmentObserver::class);
        // DynamicAlgoMaster::observe(DynamicAlgoMasterObserver::class);
        FlashNews::observe(FlashNewsObserver::class);
        // ResourceMaterial::observe(ResourceMaterialObserver::class);
        // SurveyMaster::observe(SurveyMasterObserver::class);
        // CaseDefinition::observe(CaseDefinitionObserver::class);
        // CgcInterventionsAlgorithm::observe(CgcInterventionsAlgorithmObserver::class);
        // DiagnosesAlgorithm::observe(DiagnosesAlgorithmObserver::class);
        // DifferentialCareAlgorithm::observe(DifferentialCareAlgorithmObserver::class);
        // GuidanceOnAdverseDrugReaction::observe(GuidanceOnAdverseDrugReactionObserver::class);
        // LatentTbInfection::observe(LatentTbInfectionObserver::class);
        // TreatmentAlgorithm::observe(TreatmentAlgorithmObserver::class);
    }
}
