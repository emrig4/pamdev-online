<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Authoran')
<img src="https://dev.pamdev.online/themes/airdgereaders/images/logo.png" class="logo" alt="Authoran Logo" style="height: 50px; width: 120px;">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
