@php
    use \App\Http\Controllers\CategoryController;

    $categoryClass = new CategoryController;
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <div style="border-radius: 5px; display: inline-block; width: 85px; height: 85px; background-image: url({{ $category->image? $category->image : asset('assets/images/placeholder.png') }}); background-size: cover"></div>
            <div style="display: inline-block; margin-left: 10px">
                {{ $category->de }}
                <br>
                <small>{{ $category->key }}</small>
                <br>
                <br>
            </div>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="clearfix">
                        <a href="{{ URL::to('category/delete/'.$category->id) }}" class="btn btn-primary btn-sm float-end">Delete Category</a>
                    </div>
                    <h5>EN</h5>
                    <p>{{ $category->en }}</p>
                    <h5>DE</h5>
                    <p>{{ $category->de }}</p>
                    <h5>NL</h5>
                    <p>{{ $category->nl }}</p>
                    <h5>FR</h5>
                    <p>{{ $category->fr }}</p>
                    <form action="{{ URL::to('category/update/'.$category->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mt-4 border p-2">
                            <label>Update Image</label>
                            <br>
                            <input type="file" name="image">
                        </div>
                        <button type="submit" class="btn btn-secondary mt-4">Update Image</button>
                    </form>
                </div>
            </div>
    </div>
</x-app-layout>

