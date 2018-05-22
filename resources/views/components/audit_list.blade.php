<table class="table table-condensed">
    <thead>
        <tr>
            <th>Zeitpunkt</th>
            <th>Benutzer</th>
            <th>Feld</th>
            <th>alt</th>
            <th>neu</th>
        </tr>
    </thead>
    <tbody>
        @foreach($audits as $audit)
            @foreach($audit['modified'] as $data)
                @if(!is_array($data['new']) && (!array_key_exists('old', $data) || !is_array($data['old'])))
                <tr>
                    @if($loop->first)
                    <td rowspan="{{ count($audit['modified']) }}">{{ $audit['timestamp']->format('d.m.Y H:i') }}</td>
                    <td rowspan="{{ count($audit['modified']) }}">{{ $audit['user'] }}</td>
                    @endif

                    <td>{{ $data['name'] }}</td>
                    <td>{{ (array_key_exists('old', $data) && !empty($data['old'])) ? str_limit($data['old'], 50, ' (...)') : '' }}</td>
                    <td>{{ !empty($data['new']) ? str_limit($data['new'], 50, ' (...)') : '' }}</td>
                </tr>
                @endif
            @endforeach
        @endforeach
    </tbody>
</table>


