<?php
$dynamicAlgo = App\Models\DynamicAlgoMaster::with(['app_config'])->get();
$dynamicAlgoForCaseFindings = collect($dynamicAlgo)->filter(function ($item) {
    return $item['section'] == 'Learn About Case Findings';
});
$dynamicAlgoForPMT = collect($dynamicAlgo)->filter(function ($item) {
    return $item['section'] == 'Patient Management Tool';
});
?>
<div class="sidebar">
    <nav class="sidebar-nav">
        <ul class="nav">
            <li class="nav-title">{{ trans('brackets/admin-ui::admin.sidebar.content') }}</li>
            @canany(['admin.cadre', 'admin.state', 'admin.district', 'admin.block', 'admin.symptom',
                'admin.health-facility', 'admin.module-mapping-to-name', 'admin.assessment-certificate'])
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon icon-layers"></i>Master
                        Tables</a>
                    <ul class="nav-dropdown-items">
                        @can('admin.cadre')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/cadres') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.cadre.title') }}</a></li>
                        @endcan
                        @can('admin.country')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/countries') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.country.title') }}</a></li>
                        @endcan
                        @can('admin.state')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/states') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.state.title') }}</a></li>
                        @endcan
                        @can('admin.district')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/districts') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.district.title') }}</a></li>
                        @endcan
                        @can('admin.block')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/blocks') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.block.title') }}</a></li>
                        @endcan
                        @can('admin.symptom')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/symptoms') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.symptom.title') }}</a></li>
                        @endcan
                        @can('admin.health-facility')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/health-facilities') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.health-facility.title') }}</a>
                            </li>
                        @endcan
                        @can('admin.module-mapping-to-name')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/module-mapping-to-names') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.module-mapping-to-name.title') }}</a></li>
                        @endcan
                        @can('admin.dynamic-algo-master')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/dynamic-algo-masters') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.dynamic-algo-master.short-title') }}</a></li>
                        @endcan
                        @can('admin.assessment-certificate')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/assessment-certificates') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.assessment-certificate.title') }}</a></li>
                        @endcan
                    </ul>
                </li>
            @endcan
            <li class="nav-item nav-dropdown">
                <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon icon-calendar"></i>Assessment
                    Creation</a>
                <ul class="nav-dropdown-items">
                    @can('admin.assessment')
                        <li class="nav-item"><a class="nav-link" href="{{ url('admin/assessments') }}"><i
                                    class="nav-icon icon-arrow-right"></i> {{ trans('admin.assessment.title') }}</a></li>
                    @endcan
                    @can('admin.assessment-question')
                        <li class="nav-item"><a class="nav-link" href="{{ url('admin/assessment-questions') }}"><i
                                    class="nav-icon icon-arrow-right"></i>
                                {{ trans('admin.assessment-question.short-title') }}</a></li>
                    @endcan
                    @can('admin.assessment-enrollment')
                        <li class="nav-item"><a class="nav-link" href="{{ url('admin/assessment-enrollments') }}"><i
                                    class="nav-icon icon-arrow-right"></i>
                                {{ trans('admin.assessment-enrollment.title') }}</a></li>
                    @endcan
                </ul>
            </li>
            @canany(['admin.resource-material', 'admin.cgc-intervention'])
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon icon-docs"></i>Materials</a>
                    <ul class="nav-dropdown-items">
                        @can('admin.resource-material')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/resource-materials?master=0') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.resource-material.title') }}</a>
                            </li>
                        @endcan
                        @can('admin.cgc-intervention')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/cgc-interventions') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.cgc-intervention.title') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @canany(['admin.app-config', 'admin.master-cm', 'admin.user-notification', 'admin.message-notification',
                'admin.automatic-notification'])
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#"><i
                            class="nav-icon icon-bell"></i>Cms/Notfication</a>
                    <ul class="nav-dropdown-items">
                        @can('admin.app-config')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/app-configs') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.app-config.title') }}</a></li>
                        @endcan
                        @can('admin.app-management-flag')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/app-management-flags') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.app-management-flag.short-title') }}</a></li>
                        @endcan
                        @can('admin.master-cm')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/master-cms') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.master-cm.title') }}</a></li>
                        @endcan
                        {{-- @can('admin.patient-assessment')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/patient-assessments') }}"><i class="nav-icon icon-bulb"></i> {{ trans('admin.patient-assessment.title') }}</a></li>
                        @endcan --}}
                        @can('admin.user-notification')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/user-notifications') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.user-notification.title') }}</a>
                            </li>
                        @endcan
                        @can('admin.message-notification')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/message-notifications') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.message-notification.title') }}</a></li>
                        @endcan

                        @can('admin.automatic-notification')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/automatic-notifications') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.automatic-notification.title') }}</a></li>
                        @endcan
                    </ul>
                </li>
            @endcan

            {{-- <li class="nav-item"><a class="nav-link" href="{{ url('admin/case-definitions') }}"><i class="nav-icon icon-graduation"></i> {{ trans('admin.case-definition.title') }}</a></li> --}}
            {{-- <li class="nav-item"><a class="nav-link" href="{{ url('admin/diagnoses-algorithms') }}"><i class="nav-icon icon-book-open"></i> {{ trans('admin.diagnoses-algorithm.title') }}</a></li> --}}
            {{-- <li class="nav-item"><a class="nav-link" href="{{ url('admin/treatment-algorithms') }}"><i class="nav-icon icon-diamond"></i> {{ trans('admin.treatment-algorithm.title') }}</a></li> --}}
            {{-- <li class="nav-item"><a class="nav-link" href="{{ url('admin/guidance-on-adverse-drug-reactions') }}"><i class="nav-icon icon-star"></i> {{ trans('admin.guidance-on-adverse-drug-reaction.title') }}</a></li> --}}
            {{-- <li class="nav-item"><a class="nav-link" href="{{ url('admin/latent-tb-infections') }}"><i class="nav-icon icon-plane"></i> {{ trans('admin.latent-tb-infection.title') }}</a></li> --}}
            @canany(['admin.tour', 'admin.tour-slide'])
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon icon-rocket"></i>Tours</a>
                    <ul class="nav-dropdown-items">
                        @can('admin.tour')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/tours') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.tour.title') }}</a></li>
                        @endcan
                        @can('admin.tour-slide')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/tour-slides') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.tour-slide.title') }}</a></li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @canany(['admin.static-blog', 'admin.static-faq', 'admin.static-app-config', 'admin.key-feature',
                'admin.static-testimonial', 'admin.static-release', 'admin.static-enquiry', 'admin.static-what-we-do',
                'admin.static-module', 'admin.static-resource-material'])
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon icon-note"></i>Static Web
                        Content</a>
                    <ul class="nav-dropdown-items">
                        @can('admin.static-blog')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/static-blogs') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.static-blog.title') }}</a></li>
                        @endcan
                        @can('admin.static-faq')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/static-faqs') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.static-faq.title') }}</a></li>
                        @endcan
                        @can('admin.static-app-config')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/static-app-configs') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.static-app-config.title') }}</a></li>
                        @endcan
                        @can('admin.key-feature')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/key-features') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.key-feature.title') }}</a></li>
                        @endcan
                        @can('admin.static-testimonial')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/static-testimonials') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.static-testimonial.title') }}</a></li>
                        @endcan
                        @can('admin.static-release')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/static-releases') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.static-release.title') }}</a>
                            </li>
                        @endcan
                        @can('admin.static-enquiry')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/static-enquiries') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.static-enquiry.title') }}</a>
                            </li>
                        @endcan
                        @can('admin.static-what-we-do')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/static-what-we-dos') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.static-what-we-do.title') }}</a></li>
                        @endcan
                        @can('admin.static-module')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/static-modules') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.static-module.title') }}</a>
                            </li>
                        @endcan
                        @can('admin.static-resource-material')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/static-resource-materials') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.static-resource-material.title') }}</a></li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @canany(['admin.lb-level', 'admin.lb-badge', 'admin.lb-task-list', 'admin.lb-subscriber-ranking',
                'admin.lb-subscriber-ranking-history', 'admin.lb-sub-module-usage'])
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon icon-badge"></i>Leaderboard
                        Content</a>
                    <ul class="nav-dropdown-items">
                        @can('admin.lb-level')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/lb-levels') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.lb-level.title') }}</a></li>
                        @endcan
                        @can('admin.lb-badge')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/lb-badges') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.lb-badge.title') }}</a></li>
                        @endcan
                        @can('admin.lb-task-list')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/lb-task-lists') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.lb-task-list.title') }}</a>
                            </li>
                        @endcan
                        @can('admin.lb-subscriber-ranking')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/lb-subscriber-rankings') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.lb-subscriber-ranking.title') }}</a></li>
                        @endcan
                        @can('admin.lb-subscriber-ranking-history')
                            <li class="nav-item"><a class="nav-link"
                                    href="{{ url('admin/lb-subscriber-ranking-histories') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.lb-subscriber-ranking-history.title') }}</a></li>
                        @endcan
                        @can('admin.lb-sub-module-usage')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/lb-sub-module-usages') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.lb-sub-module-usage.title') }}</a></li>
                        @endcan
                    </ul>
                </li>
            @endcan


            @canany(['admin.user-feedback-question', 'admin.user-feedback-detail', 'admin.user-feedback-history'])
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon icon-star"></i>Feedback
                        Content</a>
                    <ul class="nav-dropdown-items">
                        @can('admin.user-feedback-question')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/user-feedback-questions') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.user-feedback-question.title') }}</a></li>
                        @endcan
                        @can('admin.user-feedback-detail')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/user-feedback-details') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.user-feedback-detail.title') }}</a></li>
                        @endcan
                        @can('admin.user-feedback-history')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/user-feedback-histories') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.user-feedback-history.title') }}</a></li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @canany(['admin.flash-news', 'flash-similar-app'])
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon icon-envelope"></i>Flash
                        News</a>
                    <ul class="nav-dropdown-items">
                        @can('admin.flash-news')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/flash-news') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.flash-news.title') }}</a></li>
                        @endcan
                        {{-- @can('admin.user-feedback-detail')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/user-feedback-details') }}"><i class="nav-icon icon-arrow-right"></i> {{ trans('admin.user-feedback-detail.title') }}</a></li>
                        @endcan
                        @can('admin.user-feedback-history')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/user-feedback-histories') }}"><i class="nav-icon icon-arrow-right"></i> {{ trans('admin.user-feedback-history.title') }}</a></li>
                        @endcan --}}
                        @can('admin.flash-similar-app')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/flash-similar-apps') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.flash-similar-app.title') }}</a></li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @canany(['admin.survey-master', 'survey-master-question', 'survey-master-history'])
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon icon-question"></i>Survey
                        Forms</a>
                    <ul class="nav-dropdown-items">
                        @can('admin.survey-master')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/survey-masters') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.survey-master.title') }}</a>
                            </li>
                        @endcan
                        @can('admin.survey-master-question')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/survey-master-questions') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.survey-master-question.title') }}</a></li>
                        @endcan
                        @can('admin.survey-master-history')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/survey-master-histories') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.survey-master-history.title') }}</a></li>
                        @endcan
                    </ul>
                </li>
            @endcan

            {{-- Do not delete me :) I'm used for auto-generation menu items --}}
            @canany(['admin.case-definition'])
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon icon-book-open"></i>Learn
                        Case Findings</a>
                    <ul class="nav-dropdown-items">
                        @can('admin.case-definition')
                            <li class="nav-item"><a class="nav-link"
                                    href="{{ url('admin/case-definitions/master-nodes') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.case-definition.title') }}</a>
                            </li>
                        @endcan
                        @can('admin.dynamic-algorithm')
                            @foreach ($dynamicAlgoForCaseFindings as $item)
                                {{-- <li class="nav-item"><a class="nav-link" href="{{ url('admin/dynamic-algorithms/master-nodes?key=').$item->id }}"><i class="nav-icon icon-arrow-right"></i> {{ $item->app_config->value_json }}</a></li> --}}
                            @endforeach
                        @endcan
                    </ul>
                </li>
            @endcan
            @canany(['admin.diagnoses-algorithm', 'admin.treatment-algorithm',
                'admin.guidance-on-adverse-drug-reaction', 'admin.latent-tb-infection', 'admin.differential-care-algorithm',
                'admin.dynamic-algorithm'])
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon icon-pin"></i>Patient
                        Management</a>
                    <ul class="nav-dropdown-items">
                        @can('admin.diagnoses-algorithm')
                            <li class="nav-item"><a class="nav-link"
                                    href="{{ url('admin/diagnoses-algorithms/master-nodes') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.diagnoses-algorithm.title') }}</a></li>
                        @endcan
                        @can('admin.treatment-algorithm')
                            <li class="nav-item"><a class="nav-link"
                                    href="{{ url('admin/treatment-algorithms/master-nodes') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.treatment-algorithm.title') }}</a></li>
                        @endcan
                        @can('admin.guidance-on-adverse-drug-reaction')
                            <li class="nav-item"><a class="nav-link"
                                    href="{{ url('admin/guidance-on-adverse-drug-reactions/master-nodes') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.guidance-on-adverse-drug-reaction.nav-title') }}</a></li>
                        @endcan
                        @can('admin.latent-tb-infection')
                            <li class="nav-item"><a class="nav-link"
                                    href="{{ url('admin/latent-tb-infections/master-nodes') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.latent-tb-infection.title') }}</a></li>
                        @endcan
                        @can('admin.differential-care-algorithm')
                            <li class="nav-item"><a class="nav-link"
                                    href="{{ url('admin/differential-care-algorithms/master-nodes') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.differential-care-algorithm.title') }}</a></li>
                        @endcan
                        @can('admin.dynamic-algorithm')
                            @foreach ($dynamicAlgoForPMT as $item)
                                <li class="nav-item"><a class="nav-link"
                                        href="{{ url('admin/dynamic-algorithms/master-nodes?key=') . $item->id }}"><i
                                            class="nav-icon icon-arrow-right"></i> {{ $item->app_config->value_json }}</a>
                                </li>
                            @endforeach
                        @endcan
                    </ul>
                </li>
            @endcan
            @can('admin.cgc-interventions-algorithm')
                <li class="nav-item"><a class="nav-link"
                        href="{{ url('admin/cgc-interventions-algorithms/master-nodes') }}"><i
                            class="nav-icon icon-support"></i> {{ trans('admin.cgc-interventions-algorithm.title') }}</a>
                </li>
            @endcan

            @canany(['admin.chat-keyword', 'admin.chat-question'])
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon icon-bubbles"></i>Chat
                        Module</a>
                    <ul class="nav-dropdown-items">
                        @can('admin.chat-keyword')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/chat-keywords') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.chat-keyword.title') }}</a>
                            </li>
                        @endcan
                        @can('admin.chat-question')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/chat-questions') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.chat-question.title') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @canany(['admin.t-module-master', 'admin.t-sub-module-master', 'admin.t-training-tag'])
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#"><i
                            class="nav-icon icon-pie-chart"></i>Training Module</a>
                    <ul class="nav-dropdown-items">
                        @can('admin.t-module-master')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/t-module-masters') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.t-module-master.title') }}</a>
                            </li>
                        @endcan
                        @can('admin.t-sub-module-master')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/t-sub-module-masters') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.t-sub-module-master.title') }}</a></li>
                        @endcan
                        @can('admin.t-training-tag')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/t-training-tags') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.t-training-tag.title') }}</a>
                            </li>
                        @endcan
                        <li class="nav-item"><a class="nav-link"
                                href="{{ url('admin/chat-questions/chat-question-vs-tag') }}"><i
                                    class="nav-icon icon-arrow-right"></i> Chat Question Vs Tag</a></li>
                        {{-- <li class="nav-item"><a class="nav-link" href="{{ url('admin/t-training-tags/tagcount') }}"><i class="nav-icon icon-arrow-right"></i> Training Tag Count</a></li> --}}
                    </ul>
                </li>
            @endcan

            @canany(['admin.user-assessment', 'admin.subscriber', 'admin.chat-question-hit', 'admin.enquiry',
                'admin.chat-keyword-hit', 'admin.subscriber-activity', 'admin.chatbot-activity'])
                <li class="nav-item nav-dropdown">
                    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon icon-grid"></i>Reports</a>
                    <ul class="nav-dropdown-items">
                        @can('admin.user-assessment')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/user-assessments') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.user-assessment.title') }}</a>
                            </li>
                        @endcan
                        @can('admin.subscriber')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/subscribers') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.subscriber.title') }}</a></li>
                        @endcan
                        @can('admin.chat-question-hit')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/chat-question-hits') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.chat-question-hit.title') }}</a></li>
                        @endcan
                        @can('admin.enquiry')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/enquiries') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.enquiry.title') }}</a></li>
                        @endcan
                        @can('admin.chat-keyword-hit')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/chat-keyword-hits') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.chat-keyword-hit.title') }}</a></li>
                        @endcan
                        @can('admin.subscriber-activity')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/subscriber-activities') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.subscriber-activity.title') }}</a></li>
                        @endcan
                        @can('admin.chatbot-activity')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/chatbot-activities') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.chatbot-activity.title') }}</a></li>
                        @endcan
                        @can('admin.user-app-version')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/user-app-versions') }}"><i
                                        class="nav-icon icon-arrow-right"></i>
                                    {{ trans('admin.user-app-version.title') }}</a></li>
                        @endcan
                        @can('admin.user-app-version')
                            <li class="nav-item"><a class="nav-link"
                                    href="{{ url('admin/user-app-versions/overall-app-version') }}"><i
                                        class="nav-icon icon-arrow-right"></i> Overall App Version</a></li>
                        @endcan
                        @can('admin.activity-log')
                            <li class="nav-item"><a class="nav-link" href="{{ url('admin/activity-logs') }}"><i
                                        class="nav-icon icon-arrow-right"></i> {{ trans('admin.activity-log.title') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
            @canany(['admin.admin-user', 'admin.translation', 'admin.role', 'admin.permission',
                'admin.role-has-permission'])
                <li class="nav-title">{{ trans('brackets/admin-ui::admin.sidebar.settings') }}</li>
                @can('admin.admin-user')
                    <li class="nav-item"><a class="nav-link" href="{{ url('admin/admin-users') }}"><i
                                class="nav-icon icon-user"></i> {{ __('Manage access') }}</a></li>
                @endcan
                @can('admin.translation.index')
                    <li class="nav-item"><a class="nav-link" href="{{ url('admin/translations') }}"><i
                                class="nav-icon icon-globe"></i> {{ __('Translations') }}</a></li>
                @endcan
                @can('admin.role')
                    <li class="nav-item"><a class="nav-link" href="{{ url('admin/roles') }}"><i
                                class="nav-icon icon-settings"></i> {{ trans('admin.role.title') }}</a></li>
                @endcan
                @can('admin.permission')
                    <li class="nav-item"><a class="nav-link" href="{{ url('admin/permissions') }}"><i
                                class="nav-icon icon-key"></i> {{ trans('admin.permission.title') }}</a></li>
                @endcan
                @can('admin.role-has-permission')
                    <li class="nav-item"><a class="nav-link" href="{{ url('admin/role-has-permissions') }}"><i
                                class="nav-icon icon-lock"></i> {{ trans('admin.role-has-permission.title') }}</a></li>
                @endcan
            @endcan
            {{-- Do not delete me :) I'm also used for auto-generation menu items --}}
            {{-- <li class="nav-item"><a class="nav-link" href="{{ url('admin/configuration') }}"><i class="nav-icon icon-settings"></i> {{ __('Configuration') }}</a></li> --}}
        </ul>
    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button" style="color:white">Version V.0.1</button>
</div>
