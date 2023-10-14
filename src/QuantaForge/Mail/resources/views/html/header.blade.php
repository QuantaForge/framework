@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'QuantaForge')
<img src="https://quantaforge.com/img/notification-logo.png" class="logo" alt="QuantaForge Logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
