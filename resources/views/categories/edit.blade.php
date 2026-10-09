@extends('layouts.app')
@section('title', 'Edit Category')
@section('content')
    <h2>Edit Category</h2>
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('categories.update', $category) }}">
                @csrf @method('PUT')
                @include('categories._form')
            </form>
        </div>
    </div>
@endsection