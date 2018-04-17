<label @isset($name)for="{{ $name }}"@endif @foreach($attributes as $key => $value){!! $key ? $key . '="' : '' !!}{{ $value }}{!! $key ? '"' : '' !!}@endforeach>
	{{ $contents or '' }}
<abbr title="Required Field" data-toggle="tooltip" class="text-danger">*</abbr>
</label>