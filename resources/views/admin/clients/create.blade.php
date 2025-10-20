@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create Client</h2>

        <form action="{{ route('admin.clients.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <h3>Client Information</h3>
                    <input type="text" name="name" class="form-control" placeholder="Client Name" required>
                    <input type="text" name="db_host" class="form-control" placeholder="Db Host" required>
                    <input type="text" name="db_port" class="form-control" placeholder="Db Port" required>
                    <input type="text" name="db_name" class="form-control" placeholder="Db Name" required>
                    <input type="text" name="db_username" class="form-control" placeholder="Db Username" required>
                    <input type="text" name="db_password" class="form-control" placeholder="Db Password" required>
                    <input type="text" name="is_active" class="form-control" placeholder="Is active" required>
                </div>
            </div>

            <br>
            <button type="submit" class="btn btn-success">Create Client</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection