<x-app-layout>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">

        </section>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <p>{{ $message }}</p>
            </div>
        @endif
        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Permissions List</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-header">
                                <a class="btn btn-success" href="{{ route('permission.create') }}"> Create New
                                    Permission</a>
                                @can('permission-create')
                                @endcan
                            </div>
                            <div class="card-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>permission</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($permissions)
                                            @php
                                                $id = 1;
                                            @endphp
                                            @foreach ($permissions as $key => $permission)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $permission->name }}</td>


                                                    <td>
                                                        @can('permission-edit')
                                                            <a class="btn btn-primary"
                                                                href="{{ route('permission.edit', $permission->id) }}">Edit</a>
                                                        @endcan
                                                        @can('permission-delete')
                                                            {!! Form::open([
                                                                'method' => 'DELETE',
                                                                'route' => ['permission.destroy', $permission->id],
                                                                'style' => 'display:inline',
                                                            ]) !!}
                                                            {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                                                            {!! Form::close() !!}
                                                        @endcan
                                                    </td>



                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Roles</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- /.content -->
    </div>


</x-app-layout>
