@if (session('success'))
    <div class="notification main is-primary is-fixed-top">
        <button class="delete"></button>
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="notification main is-danger is-fixed-top">
        <button class="delete"></button>
        {{ session('error') }}
    </div>
@endif

@if (session('warning'))
    <div class="notification main is-warning is-fixed-top">
        <button class="delete"></button>
        {{ session('warning') }}
    </div>
@endif

@if (session('info'))
    <div class="notification main is-info is-fixed-top">
        <button class="delete"></button>
        {{ session('info') }}
    </div>
@endif
@if($errors->any())
    <div class="notification main is-danger is-fixed-top">
        <button class="delete"></button>
        {{ implode('', $errors->all(':message')) }}
    </div>
@endif
