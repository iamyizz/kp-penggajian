@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-3">Tambah Parameter</h3>

    <form method="POST" action="{{ route('parameter.store') }}">
        @csrf
        @include('parameter.form')
        <button class="btn btn-primary">Simpan</button>
    </form>

</div>
@endsection
