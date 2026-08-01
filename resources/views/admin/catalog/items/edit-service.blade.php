@extends('layouts.admin')

@section('title', 'Editar Servicio')

@section('content')
    @livewire('admin.catalog.service-form', [
        'catalogItemId' => $catalogItem->id,
        'returnUrl' => $returnUrl ?? route('admin.catalog-items.show', $catalogItem),
    ])
@endsection
