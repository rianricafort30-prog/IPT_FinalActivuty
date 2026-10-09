@extends('layouts.app')
@section('title', 'Add Category')
@section('content')
    <h2>Add Category</h2>
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('categories.store') }}">
                @csrf
                @include('categories._form')
            </form>
        </div>
    </div>
@endsection