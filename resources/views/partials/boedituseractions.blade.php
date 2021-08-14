<div class="flex">
    <div>@include('partials.boedituserbutton')</div>
    @if (! $trashed)
        <div>@include('partials.bodeleteuserbutton')</div>
    @endif
    <div>@include('partials.boresetpwuserbutton')</div>
    @if (! $isVerified)
        <div>@include('partials.bosendemailverif')</div>
    @endif
</div>