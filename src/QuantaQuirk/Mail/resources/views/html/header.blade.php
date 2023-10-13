@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'QuantaQuirk')
<img src="https://quantaquirk.com/img/notification-logo.png" class="logo" alt="QuantaQuirk Logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
