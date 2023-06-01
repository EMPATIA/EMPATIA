@extends('backend.layouts.master')

@section('title', 'Dashboard')

@section('content')
{{--TODO: Incluir aqui a funcionalidade de ir buscar a dashboard específica do projeto (Feito pela Isabel)--}}
    <h1 class="d-flex align-items-center justify-content-center text-primary fw-bolder" style="font-size: 4rem">{{ config('app.name', 'Empatia') }}</h1>
@endsection
