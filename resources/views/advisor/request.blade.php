@extends('advisor.layout')

@section('content')
    <h1>Solicitudes de soporte</h1>

    <table width="100%" cellpadding="10" style="margin-top:20px; background:white;">
        <tr>
            <th>ID</th>
            <th>Estado</th>
            <th>Acción</th>
        </tr>

        <tr>
            <td>#1</td>
            <td>waiting</td>
            <td>
                <a href="{{ route('advisor.chat', 1) }}">Atender</a>
            </td>
        </tr>
    </table>
@endsection
