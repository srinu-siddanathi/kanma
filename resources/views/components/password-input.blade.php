@props([
    'name',
    'id' => null,
    'label' => null,
    'required' => false,
    'class' => '',
    'placeholder' => '',
    'value' => '',
    'error' => null
])

@php
    $id = $id ?? $name;
    $inputClass = 'form-control ' . $class;
    if ($error) {
        $inputClass .= ' is-invalid';
    }
@endphp

<div class="mb-3">
    @if($label)
        <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    @endif
    
    <div class="input-group">
        <input 
            type="password" 
            class="{{ $inputClass }}" 
            id="{{ $id }}" 
            name="{{ $name }}" 
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
        >
        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('{{ $id }}')">
            <svg width="16" height="16" class="password-toggle-icon">
                <use xlink:href="#eye"></use>
            </svg>
        </button>
    </div>
    
    @if($error)
        <div class="invalid-feedback">
            {{ $error }}
        </div>
    @endif
</div>

<script>
function togglePassword(inputId) {
    const passwordInput = document.getElementById(inputId);
    const toggleBtn = passwordInput.nextElementSibling;
    const icon = toggleBtn.querySelector('use');
    
    const type = passwordInput.type === 'password' ? 'text' : 'password';
    passwordInput.type = type;
    
    // Update icon
    icon.setAttribute('xlink:href', type === 'password' ? '#eye' : '#eye-slash');
}
</script>
