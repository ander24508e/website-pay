@extends('layouts.admin')

@section('title', 'Editar Producto')

@push('styles')
    @vite('resources/scss/Catalogo/catalogo-products.scss')
@endpush

@section('content')
    @livewire('admin.catalog.product-form', [
        'catalogItemId' => $catalogItem->id,
        'returnUrl' => $returnUrl,
        'returnContext' => $returnContext,
    ])
@endsection
