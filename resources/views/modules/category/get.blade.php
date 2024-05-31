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
            @if(session()->has('message'))
                <div class="alert alert-success">
                    {{ session()->get('message') }}
                </div>
            @endif
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="">
                        <div class="filters">
                            <h5>Filters</h5>
                            <form action="" method="GET">
                                <div class="row">
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <label>Key</label>
                                            <input type="text" class="form-control" name="key" list="catkey" value="{{ $filterKey }}">
                                            <datalist id="catkey">
                                                @foreach($categoriesForFilterInDe as $categoriesForFilterInDeVal)
                                                    <option value="{{ $categoriesForFilterInDeVal->key }}">
                                                @endforeach
                                            </datalist>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <label>DE</label>
                                            <input type="text" class="form-control" name="de" list="catde" value="{{ $filterDe }}" autocomplete="off">
                                            <datalist id="catde">
                                                @foreach($categoriesForFilterInDe as $categoriesForFilterInDeVal)
                                                    <option value="{{ $categoriesForFilterInDeVal->de }}">
                                                @endforeach
                                            </datalist>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="form-group">
                                            <label>EN</label>
                                            <input type="text" class="form-control" name="en" list="caten" value="{{ $filterEn }}">
                                            <datalist id="caten">
                                                @foreach($categoriesForFilterInDe as $categoriesForFilterInDeVal)
                                                    <option value="{{ $categoriesForFilterInDeVal->en }}">
                                                @endforeach
                                            </datalist>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm mt-3">Apply Filter</button>
                                @if($filterKey || $filterEn || $filterDe)
                                    <a href="{{ URL::to('category') }}" class="btn btn-light btn-sm mt-3">Clear Filter</a>
                                @endif
                            </form>
                        </div>
                        <div class="clearfix mt-4">
                            <div class="float-end">
                                <form action="{{ URL::to('product/delete/bulk') }}" method="POST" style="display: inline-block; margin-right: 15px">
                                    @csrf
                                    <input type="hidden" id="selected-category-ids" name="category_ids" value="">
                                    <button href="{{ URL::to('product') }}" class="btn btn-danger btn-sm btn-delete-category" disabled>Delete Categories</button>
                                </form>
                                <button type="button" class="btn btn-primary btn-sm float-end trigger-click" data-toggle="modal" data-target="#createCategory"  style="display: inline-block">
                                    Create New Category
                                </button>
                            </div>
                            <div class="float-start">
                                Total no of categories: <b>{{ $categoryClass->getCollectionSize($filterKey, $filterDe, $filterEn) }}</b>
                            </div>
                        </div>
                        @if(count($categories))
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" class="parent-checkbox" name="category_ids[]">
                                    </th>
                                    <th>Key</th>
                                    <th></th>
                                    <th>DE</th>
                                    <th>EN</th>
                                    <th></th>
                                    {{--                    <th></th>--}}
                                </tr>
                                </thead>
                                <tbody aria-relevant="all" aria-live="polite">
                                @foreach($categories as $category)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="child-checkbox" name="category_ids[]" value="{{ $category->id }}">
                                        </td>
                                        <td>
                                            {{ $category->key }}
                                        </td>
                                        <td style="width: 125px">
                                            <div style="width: 85px; height: 85px; background-image: url({{ $category->image? $category->image : asset('assets/images/placeholder.png') }}); background-size: cover"></div>
                                        </td>
                                        <td>{{ $category->de }}</td>
                                        <td>{{ $category->en }}</td>
                                        <td class="table-action" style="text-align: end">
                                            <a href="{{ URL::to('/category/edit/'.$category->id) }}">
                                                <svg height="1.5em" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="Edit / Edit_Pencil_Line_02"> <path id="Vector" d="M4 20.0001H20M4 20.0001V16.0001L14.8686 5.13146L14.8704 5.12976C15.2652 4.73488 15.463 4.53709 15.691 4.46301C15.8919 4.39775 16.1082 4.39775 16.3091 4.46301C16.5369 4.53704 16.7345 4.7346 17.1288 5.12892L18.8686 6.86872C19.2646 7.26474 19.4627 7.46284 19.5369 7.69117C19.6022 7.89201 19.6021 8.10835 19.5369 8.3092C19.4628 8.53736 19.265 8.73516 18.8695 9.13061L18.8686 9.13146L8 20.0001L4 20.0001Z" stroke="#B9853E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g> </g></svg>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                            <div class="d-flex mt-5">
                                <div class="mx-auto"> {{ $categories->links() }}</div>
                            </div>
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
                    @if(isset($_GET['add']))
                        <div class="alert alert-danger">
                            Please fill in all required fields
                        </div>
                    @endif
                    <form action="{{ URL::to('category/new') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label>Category (Key) *</label>
                            <input type="text" name="category" class="form-control @if(isset($_GET['add'])) is-invalid @endif" >
                        </div>
                        <div class="form-group mt-4">
                            <label>DE *</label>
                            <input type="text" name="de" class="form-control @if(isset($_GET['add'])) is-invalid @endif">
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
                        <div class="form-group mt-4">
                            <label>Image</label>
                            <input type="file" name="image">
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
    <script>
        var categoryIds = [];

        $('.parent-checkbox').on('click', function(){
            if(!$(this).is(':checked')){
                $('.child-checkbox').prop("checked", false);
                $('.btn-delete-category').prop('disabled', true);
                categoryIds = [];
                $('#selected-category-ids').val(categoryIds.join());
                return;
            }

            if($('.child-checkbox:checked').length < 20){
                $('.child-checkbox').prop("checked", false);
            }

            $.each($('.child-checkbox'), function(i, v){
                $('.btn-delete-category').prop('disabled', false);
                $(v).prop("checked", true);

                categoryIds.push($(v).val());
            });

            $('#selected-category-ids').val(categoryIds.join());
        });

        $('.child-checkbox').on('click', function() {
            if ($('.child-checkbox:checked').length) {
                $('.btn-delete-category').prop('disabled', false);
            } else {
                $('.btn-delete-category').prop('disabled', true);
            }

            var index = $.inArray($(this).val(), categoryIds);

            if(index !== -1){
                categoryIds.splice(index, 1);
            }else{
                categoryIds.push($(this).val());
            }

            $('#selected-category-ids').val(categoryIds.join());
        });
    </script>
    @if(isset($_GET['add']))
    <script>
        $(document).ready(function(){
            $('.trigger-click').trigger('click');
        });
    </script>
    @endif
</x-app-layout>

