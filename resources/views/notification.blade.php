@php
    use \App\Http\Controllers\CategoryController;

    $categoryClass = new CategoryController;

    $filterKey = request()->has('key')? request()->input('key') : '';
    $filterDe = request()->has('de')? request()->input('de') : '';
    $filterEn = request()->has('en')? request()->input('en') : '';
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Categories') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="">
                        @if(count($notifications))
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>Notification</th>
                                </tr>
                                </thead>
                                <tbody aria-relevant="all" aria-live="polite">
                                @foreach($notifications as $notification)
                                    <tr>
                                        <td>
                                            {{ $notification->content }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createCategory" tabindex="-1" role="dialog" aria-labelledby="createCategory" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create new category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ URL::to('category/new') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Category (Key)</label>
                            <input type="text" name="category" class="form-control" >
                        </div>
                        <div class="form-group mt-4">
                            <label>DE</label>
                            <input type="text" name="de" class="form-control">
                        </div>
                        <div class="form-group mt-4">
                            <label>EN</label>
                            <input type="text" name="en" class="form-control">
                        </div>
                        <div class="form-group mt-4">
                            <label>NL</label>
                            <input type="text" name="nl" class="form-control">
                        </div>
                        <div class="form-group mt-4">
                            <label>FR</label>
                            <input type="text" name="fr" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-secondary mt-4">Save</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

