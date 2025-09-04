@isset($label)
    <label class="form-label">{{ $label }}</label>
@endisset
<div class="input-group">
    @if (defaultCurrency()->position == 1)
        <span class="input-group-text px-4 bg-white">{{ defaultCurrency()->symbol }}</span>
    @endif
    <input {{ isset($id) ? 'id=' . $id : '' }} type="number" {{ isset($name) ? 'name=' . $name : '' }}
        class="form-control form-control-md input-numeric" step="any"
        placeholder="{{ isset($placeholder) ? $placeholder : 0 }}"
        value="{{ isset($value) ? $value : (isset($name) ? old($name) : '') }}" {{ isset($min) ? 'min=' . $min : '' }}
        {{ isset($max) ? 'max=' . $max : '' }} @disabled($disabled ?? false) @required($required ?? false)>
    @if (defaultCurrency()->position == 2)
        <span class="input-group-text px-4 bg-white">{{ defaultCurrency()->symbol }}</span>
    @endif
</div>
