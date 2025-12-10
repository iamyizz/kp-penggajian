@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-3">Edit Parameter</h3>

    <form method="POST" action="{{ route('parameter.update', $param->id_param) }}">
        @csrf
        @method('PUT')
        @include('parameter.form')
        <button class="btn btn-warning">Update</button>
    </form>

</div>
@endsection
