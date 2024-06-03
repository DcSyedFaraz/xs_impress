@props(['route'])

<form method="POST" action="{{ $route }}" style="display:inline" onsubmit="return confirm('Are you sure you want to delete this item?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger">
        {{ $slot }}
    </button>
</form>
