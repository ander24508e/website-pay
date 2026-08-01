@extends('layouts.admin')

@section('title', 'Editar Producto')

@section('content')
    @livewire('admin.catalog.product-form', [
        'catalogItemId' => $catalogItem->id,
        'returnUrl' => $returnUrl,
        'returnContext' => $returnContext,
    ])
@endsection
