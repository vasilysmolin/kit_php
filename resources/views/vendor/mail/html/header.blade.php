<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
{{--        <img src="https://api.tapigo.ru/img/logo.svg" alt="">--}}
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
