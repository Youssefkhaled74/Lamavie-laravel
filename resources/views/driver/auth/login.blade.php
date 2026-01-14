@php
    $title = 'Driver Login';
    $roleLabel = 'Driver';
    $action = route('driver.login.post');
    $description = 'Sign in to your driver account';
    $includeFirebase = false;
@endphp

@include('auth.login-layout', compact('title','roleLabel','action','description','includeFirebase'))
