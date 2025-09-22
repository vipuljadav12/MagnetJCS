    @if(isset($eligibility->template_id) && $eligibility->template_id != 0)
        @include("SetEligibility::view.".$eligibilityTemplate->content_html."_configuration")
    @else
        @include("SetEligibility::view.template2")
    @endif


