<td class="bg-default text-center" title="Außenlager">AL</td>
<td class="text-{{  $log->change >= 0 ? 'success' : 'danger' }} text-center"><i>{{ $log->change >= 0 ? '+'.$log->change : $log->change }}</i></td>
<td class="text-center">{{ $log->new_quantity }}</td>
@include('components.quantity_log._defaults')