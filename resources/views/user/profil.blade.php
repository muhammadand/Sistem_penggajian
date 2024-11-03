<!-- resources/views/profile/show.blade.php -->

@extends('layout.template')

@section('content')
<h1>User Profile</h1>
<p>Name: {{($name)}}</p>
<p>Username: {{ $username }}</p>
<img src="" alt="Profile Image">

<!-- Tombol untuk memperbarui profil -->
<a href="">Edit Profile</a>

<!-- Tombol untuk menambahkan profil baru -->
@endsection
