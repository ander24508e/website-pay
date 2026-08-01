@extends('layouts.admin')

@section('title', 'Editar Producto')

@section('content')
    <livewire:admin.catalog.product-form
        :catalog-item-id="$catalogItem->id"
        :return-url="$returnUrl"
        :return-context="$returnContext"
    />
@endsection
