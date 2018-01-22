<table class="table table-condensed">
    <tbody>
        @foreach($audits as $audit)
        <tr>
            <td>{{ $audit['timestamp']->format('d.m.Y H:i') }}</td>
            <td>{{ $audit['user'] }}</td>
            <td>
                <ul>
                @foreach($audit['modified'] as $data)
                    <li>
                        <strong>{{ $data['name'] }}:</strong><br/>
                        alt: {{ str_limit($data['old'], 50, ' (...)') }}, neu: {{ str_limit($data['old'], 50, ' (...)') }}
                    </li>
                @endforeach
                </ul>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>


