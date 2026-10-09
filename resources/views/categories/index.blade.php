@extends('layouts.app')
@section('title', 'Categories')
@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h2>Categories</h2>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">Add Category</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Products</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->description ?: '—' }}</td>
                            <td>{{ $category->products_count }}</td>
                            <td class="text-nowrap">
                                <a class="btn btn-sm btn-outline-info" href="{{ route('categories.show', $category) }}">View</a>
                                <a class="btn btn-sm btn-outline-primary"
                                    href="{{ route('categories.edit', $category) }}">Edit</a>
                                <form class="d-inline" method="POST" action="{{ route('categories.destroy', $category) }}"
                                    onsubmit="return confirm('Delete this category?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{ $categories->links() }}
        </div>
    </div>
@endsection