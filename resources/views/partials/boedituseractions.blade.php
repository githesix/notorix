<div class="flex">
    <div>@include('partials.boedituserbutton')</div>
    @if (! $trashed && auth()->user()->statut & 8)
        <div>@include('partials.bodeleteuserbutton')</div>
    @endif
    <div>@include('partials.boresetpwuserbutton')</div>
    @if (! $isVerified)
        <div>@include('partials.bosendemailverif')</div>
    @endif
</div>