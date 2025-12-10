@extends('layouts.sidebar')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Receptionist Management</h4>
        <a href="{{ route('admin.resepsionis.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Receptionist
        </a>
    </div>

    <div class="card shadow-sm rounded-3">
        <div class="card-header">
            <strong>Receptionist List</strong>
        </div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th width="150">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($resepsionis as $r)
                        <tr>
                            <td><strong>{{ $r->name }}</strong></td>
                            <td>
                                <a href="mailto:{{ $r->email }}">{{ $r->email }}</a>
                            </td>
                            <td>
                                <a href="{{ route('admin.resepsionis.edit', $r->id) }}" class="text-primary">Edit</a> |
                                <form action="{{ route('admin.resepsionis.destroy', $r->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus resepsionis ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger p-0 m-0">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Belum ada akun resepsionis</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $resepsionis->links('vendor.pagination.custom') }}
    </div>
</div>
@endsection
