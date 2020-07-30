<table class="dataTable table-condensed multiRow text-sm">
    <thead>
        <tr>
            <th>@lang('wann') / @lang('wer')</th>
            <th>@lang('was')</th>
            <th>@lang('alt')</th>
            <th>@lang('neu')</th>
        </tr>
    </thead>

    @foreach($audits as $audit)
        <tbody>
            @foreach($audit['modified'] as $data)
                @if(!is_array($data['new']) && (!array_key_exists('old', $data) || !is_array($data['old'])))
                <tr>
                    @if($loop->first)
                    <td rowspan="{{ count($audit['modified']) }}" class="whitespace-no-wrap">
                        {{ $audit['timestamp']->format('d.m.Y H:i') }}
                        <br>
                        <span class="text-xs">{{ $audit['user'] }}</span>
                    </td>
                    @endif

                    <td class="whitespace-no-wrap">
                        {{ $data['name'] }}<br>
                        <span class="text-xs">in {{ $audit['name'] }}</span>
                    </td>
                    <td>{!! (array_key_exists('old', $data) && !empty($data['old'])) ? Illuminate\Support\Str::limit($data['old'], 50, ' (...)') : '' !!}</td>
                    <td>{!! !empty($data['new']) ? Illuminate\Support\Str::limit($data['new'], 50, ' (...)') : '' !!}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
    @endforeach
</table>


