@extends('admin.layouts.app')

@section('title', 'Thùng rác điểm đến')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between mb-3">
        <h3>Thùng rác điểm đến</h3>

        <a href="{{ route('admin.destinations.index') }}" class="btn btn-primary">
            Quay lại
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên điểm đến</th>
                <th>Ngày xóa</th>
                <th width="220">Hành động</th>
            </tr>
        </thead>

        <tbody>
            @forelse($destinations as $destination)
                <tr>
                    <td>{{ $destination->id }}</td>
                    <td>{{ $destination->name }}</td>
                    <td>{{ $destination->deleted_at }}</td>
                    <td>
                        <form action="{{ route('admin.destinations.restore', $destination->id) }}"
                              method="POST"
                              class="d-inline">
                            @csrf
                            <button class="btn btn-success btn-sm">Khôi phục</button>
                        </form>

                        <form action="{{ route('admin.destinations.forceDelete', $destination->id) }}"
                              method="POST"
                              class="d-inline"
                              onsubmit="return confirm('Xóa vĩnh viễn điểm đến này?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm">Xóa vĩnh viễn</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Không có điểm đến nào trong thùng rác.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $destinations->links() }}

</div>
@endsection
