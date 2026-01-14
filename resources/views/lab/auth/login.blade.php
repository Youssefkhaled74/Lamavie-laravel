@php
    $title = 'Lab Login';
    $roleLabel = 'Lab';
    $action = route('lab.login.post');
    $description = 'Sign in to your lab account';
    $includeFirebase = false;
@endphp

@include('auth.login-layout', compact('title','roleLabel','action','description','includeFirebase'))
