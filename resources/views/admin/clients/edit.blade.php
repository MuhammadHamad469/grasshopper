@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Client</h2>

        <form action="{{ route('admin.clients.update', $client) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <h3>Client Information</h3>
                    <input type="text" name="name" class="form-control" placeholder="Client Name" value="{{ $client->name }}" required>
                    <input type="text" name="db_host" class="form-control" placeholder="Db Host" value="{{ $client->db_host }}" required>
                    <input type="text" name="db_port" class="form-control" placeholder="Db Port" value="{{ $client->db_port }}" required>
                    <input type="text" name="db_name" class="form-control" placeholder="Db Name" value="{{ $client->db_name }}" required>
                    <input type="text" name="db_username" class="form-control" placeholder="Db Username" value="{{ $client->db_username }}" required>
                    <input type="password" name="db_password" class="form-control" placeholder="Db Password" value="{{ $plainPassword }}" required>
                    <input type="text" name="is_active" class="form-control" placeholder="Is active" value="{{ $client->is_active }}" required>
                </div>
            </div>
            @include('partials.save-button')
            @include('partials.icon_button', ['href' => route('admin.clients.index'), 'type' => 'danger', 'icon' => 'fa-arrow-left', 'slot' => 'Back'])
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endsection