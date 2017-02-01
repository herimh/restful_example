@extends('layouts.master')

@section('title').
    Ejemplo de CULR y API RestFull
@stop

@section('content')
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <div class="panel panel-primary">
                <div class="panel-heading">Descarga listado de carros vía CURL</div>
                <div class="panel-body text-center">
                    <a class="btn btn btn-primary" href="{{ route('cars.download') }}">Descargar listado.</a>
                    <a class="btn btn btn-danger" href="{{ route('cars.delete_all') }}">Limpiar Tabla</a>
                </div>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Carro</th><th>Precio</th><th>Año</th><th>No. Puertas</th>
                        <th>KM</th><th>Transmison</th><th>Fotografía</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cars as $car)
                        <tr>
                            <td>{{ $car->name }}</td><td>$ {{ number_format($car->price, 2, '.', ',') }}</td>
                            <td>{{ $car->year }}</td><td>{{ $car->doors }}</td>
                            <td>{{ number_format($car->km, 0, '.', ',') }}</td>
                            <td>{{ $car->transmission }}</td>
                            <td><a href="{{ $car->image_url }}">Imagen</a></td>
                        </tr>
                    @endforeach
                    <tr></tr>
                </tbody>
            </table>
        </div>
    </div>

@stop
