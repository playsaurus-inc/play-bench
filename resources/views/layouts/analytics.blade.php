@php
    $config = config('services.statcounter');
@endphp

@if (App::environment('production') && filled($config['project_id']) && filled($config['security']))
    <!-- Statcounter code -->
    <script type="text/javascript">
        var sc_project = {{ $config['project_id'] }};
        var sc_invisible = {{ $config['invisible'] ? '1' : '0' }};
        var sc_security = "{{ $config['security'] }}";
        var sc_remove_link = {{ $config['remove_link'] ? '1' : '0' }};
    </script>
    <script type="text/javascript" src="https://www.statcounter.com/counter/counter.js" async></script>
    <noscript>
        <div class="statcounter">
            <img class="statcounter"
                 src="https://c.statcounter.com/{{ $config['project_id'] }}/0/{{ $config['security'] }}/1/"
                 alt="Web Analytics"
                 referrerPolicy="no-referrer-when-downgrade">
        </div>
    </noscript>
    <!-- End of Statcounter Code -->
@endif
