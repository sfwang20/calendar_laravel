<div id="calendar" data-year="{{ date('Y') }}" data-month="{{ date('m') }}">
    <div id="header">
        <div id='date'>{{ date('Y') }}/{{ date('m') }}</div>
    </div>

    <div id="days" class="clearfix">
        <div class="day">SUN</div>
        <div class="day">MON</div>
        <div class="day">TUE</div>
        <div class="day">WED</div>
        <div class="day">THR</div>
        <div class="day">FRI</div>
        <div class="day">SAT</div>
    </div>

    <div id="dates" class="clearfix">
        @foreach ($dates as $key => $date)
            <div class="date-block {{(is_null($date))? 'empty': '' }}" data-date="{{$date}}">
                <div class="date">{{$date}}</div>
                <div class="events">
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    var events = @json($events);
</script>
