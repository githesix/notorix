@foreach ($groupes as $groupe)
    <span title="{{ $groupe->description }}">{{ $groupe->nom }}@if (!$loop->last), @endif</span>
@endforeach