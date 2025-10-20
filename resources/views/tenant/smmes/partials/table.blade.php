<table class="table table-hover">
    <thead>
    <tr>
        <th>#</th>
        <th>
            <a href="{{ route('tenant.smmes.index', array_merge(request()->query(), [
                'sort' => 'name',
                'direction' => request('sort') === 'name' && request('direction') === 'asc' ? 'desc' : 'asc'
            ])) }}" class="text-decoration-none text-dark">
                Business Name
                @if(request('sort') === 'name')
                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                @else
                    <i class="fas fa-sort"></i>
                @endif
            </a>
        </th>
        <th>Registration Number</th>
        <th>
            <a href="{{ route('tenant.smmes.index', array_merge(request()->query(), [
                'sort' => 'years_of_experience',
                'direction' => request('sort') === 'years_of_experience' && request('direction') === 'asc' ? 'desc' : 'asc'
            ])) }}" class="text-decoration-none text-dark">
                Experience
                @if(request('sort') === 'years_of_experience')
                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                @else
                    <i class="fas fa-sort"></i>
                @endif
            </a>
        </th>
        <th>Documents</th>
        <th>
            <a href="{{ route('tenant.smmes.index', array_merge(request()->query(), [
                'sort' => 'grade',
                'direction' => request('sort') === 'grade' && request('direction') === 'asc' ? 'desc' : 'asc'
            ])) }}" class="text-decoration-none text-dark">
                Grade
                @if(request('sort') === 'grade')
                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                @else
                    <i class="fas fa-sort"></i>
                @endif
            </a>
        </th>
        <th>
            <a href="{{ route('tenant.smmes.index', array_merge(request()->query(), [
                'sort' => 'status',
                'direction' => request('sort') === 'status' && request('direction') === 'asc' ? 'desc' : 'asc'
            ])) }}" class="text-decoration-none text-dark">
                Status
                @if(request('sort') === 'status')
                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                @else
                    <i class="fas fa-sort"></i>
                @endif
            </a>
        </th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    @foreach($smmes as $smme)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $smme->name }}</td>
            <td>{{ $smme->registration_number }}</td>
            <td>{{ $smme->years_of_experience }} years</td>
            <td class="@if ($smme->documents_verified == 1) text-success @else text-danger @endif">
                {{ $smme->documents_verified ? 'Verified' : 'Not Verified' }}
            </td>
            <td>{{ $smme->grade }}</td>
            <td class="
                @if ($smme->status == 'red') text-danger
                @elseif ($smme->status == 'yellow') text-warning
                @elseif ($smme->status == 'green') text-success
                @endif
                ">
                {{ ucfirst($smme->status) }}
            </td>
            <td>
                <a href="{{ route('tenant.smmes.show', $smme) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                <a href="{{ route('tenant.smmes.edit', $smme) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                <form action="{{ route('tenant.smmes.destroy', $smme) }}" method="POST"
                      style="display: inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger"
                            onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
{{ $smmes->appends(request()->query())->links() }}