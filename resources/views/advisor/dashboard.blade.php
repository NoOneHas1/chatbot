@extends('advisor.layout')

@section('content')
    <h1>Dashboard</h1>

    <div style="display:grid; grid-template-columns: repeat(3,1fr); gap:20px; margin-top:20px;">
        <div class="card">Solicitudes en espera: <strong>0</strong></div>
        <div class="card">Chats activos: <strong>0</strong></div>
        <div class="card">Asesores activos: <strong>1</strong></div>
    </div>
@endsection
