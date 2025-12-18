@extends('advisor.layout')

@section('content')
    <h1>Chat con usuario</h1>

    <div style="background:white; height:60vh; padding:20px; overflow-y:auto;">
        <p><strong>Usuario:</strong> Hola</p>
        <p><strong>Asesor:</strong> Buenas tardes</p>
    </div>

    <form style="margin-top:15px;">
        <input type="text" placeholder="Escribe tu mensaje..." style="width:80%">
        <button>Enviar</button>
    </form>
@endsection
