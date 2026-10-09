@extends('layouts.app')
@section('title', 'Category Details')
@section('content')
<h2>{{ $category->name }}</h2>
<p>{{ $category->description ?: 'No description.' }}</p>
<h5>Products</h5>
<div class="card shadow-sm">
    <div class="card-body">
        <ul class="list-group list-group-flush">
            @forelse($category->products as $product)
                <li class="list-group-item d-flex justify-content-between">
                    <a href="{{ route('products.show', $product) }}">{{ $product->name }}</a>
                    <span>₱{{ number_format($product->price, 2) }}</span>
                </li>
            @empty
                <li class="list-group-item">No products in this category.</li>
            @endforelse
        </ul>
        <a class="btn btn-secondary mt-3" href="{{ route('categories.index') }}">Back</a>
    </div>
</div>