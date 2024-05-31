<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <div style="display: inline-block; margin-left: 10px">
                {{ $supplier->name }}
                <br>
                <div style="color: #c03"><small>{{ $supplier->supplier_code }}</small></div>
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
                    <form action="{{ URL::to('supplier/update/'.$supplier->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" value="{{ $supplier->name }}">
                        </div>
                        <div class="form-group mt-4">
                            <label>SKU Code</label>
                            <input type="text" class="form-control" name="supplier_code" value="{{ $supplier->supplier_code }}">
                        </div>
                        <div class="form-group mt-4">
                            <label>Telephone</label>
                            <input type="text" class="form-control" name="telephone" value="{{ $supplier->telephone }}">
                        </div>
                        <button type="submit" class="btn btn-primary mt-4">Save</button>
                    </form>
                </div>
            </div>
    </div>
</x-app-layout>

