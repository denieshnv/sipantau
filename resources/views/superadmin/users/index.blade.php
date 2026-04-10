@extends('layouts.app')
@section('title', 'Kelola User')
@section('page-title', 'Kelola User')
@section('breadcrumb')
    <li class="breadcrumb-item">Manajemen</li>
    <li class="breadcrumb-item active">User</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <p class="text-muted mb-0" style="font-size: 0.85rem;">Daftar pengguna yang terdaftar dalam sistem.</p>
    <a href="{{ route('superadmin.users.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Tambah User
    </a>
</div>

<div class="table-container">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th style="width: 50px">#</th>
                    <th>Nama</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Terdaftar</th>
                    <th style="width: 150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $i => $user)
                <tr>
                    <td>{{ $users->firstItem() + $i }}</td>
                    <td class="fw-semibold">{{ $user->name }}</td>
                    <td><code>{{ $user->username }}</code></td>
                    <td>
                        @php
                            $badgeClass = match($user->role) {
                                'superadmin'  => 'badge-superadmin',
                                'kasubag_pk'  => 'badge-superadmin',
                                'pptk'        => 'badge-admin',
                                'camat'       => 'badge-admin',
                                default       => 'badge-admin',
                            };
                        @endphp
                        <span class="badge-role {{ $badgeClass }}">
                            {{ $user->role_label }}
                        </span>
                    </td>
                    <td style="font-size: 0.82rem;">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('superadmin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        @if($user->id !== auth()->id())
                        <form action="{{ route('superadmin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus user ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data user.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())<div class="p-3 border-top">{{ $users->links() }}</div>@endif
</div>
@endsection
