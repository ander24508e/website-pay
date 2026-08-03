@extends('layouts.admin')

@section('title', 'Editar Servicio')

@push('styles')
    @vite('resources/scss/Catalogo/catalogo-services.scss')
@endpush

@section('content')
    @livewire('admin.catalog.service-form', [
        'catalogItemId' => $catalogItem->id,
        'returnUrl' => $returnUrl ?? route('admin.catalog-items.show', $catalogItem),
    ])
@endsection
