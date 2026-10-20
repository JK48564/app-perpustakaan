{{-- File: resources/views/profile/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Profil Pengguna - Perpustakaan Digital Kampus')

@section('content')
    <h1>Profil Pengguna</h1>

    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 16px 20px; margin-bottom: 30px;">
        <table style="border: none; margin-top: 0; width: 100%; max-width: 500px;">
            <tr style="border: none;">
                <td style="border: none; padding: 6px 0; font-weight: bold; width: 120px;">Nama:</td>
                <td style="border: none; padding: 6px 0;">{{ auth()->user()->name }}</td>
            </tr>
            <tr style="border: none;">
                <td style="border: none; padding: 6px 0; font-weight: bold;">Email:</td>
                <td style="border: none; padding: 6px 0;">{{ auth()->user()->email }}</td>
            </tr>
            <tr style="border: none;">
                <td style="border: none; padding: 6px 0; font-weight: bold;">Role:</td>
                <td style="border: none; padding: 6px 0;">
                    <span class="badge {{ auth()->user()->role === 'admin' ? 'badge-dikembalikan' : 'badge-dipinjam' }}">
                        {{ ucfirst(auth()->user()->role) }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <h2>Ganti Password</h2>

    <form action="{{ route('profile.password') }}" method="POST" style="max-width: 500px;">
        @csrf
        @method('PUT')

        <div>
            <label for="current_password">Password Lama</label>
            <input type="password" name="current_password" id="current_password">
            @error('current_password')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="password">Password Baru</label>
            <input type="password" name="password" id="password">
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="password_confirmation">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" id="password_confirmation">
            @error('password_confirmation')
                <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn">Perbarui Password</button>
    </form>
@endsection
