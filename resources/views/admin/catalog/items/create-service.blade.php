@extends('layouts.admin')

@section('title', 'Nuevo Servicio')

@push('styles')
    @vite('resources/scss/Catalogo/catalogo-services.scss')
@endpush

@section('content')
    @php
        $returnUrl = ($returnToCategory && $selectedCategoryId > 0)
            ? route('admin.catalog-types.show', $selectedTypeId)
            : (($returnToType && $selectedTypeId > 0)
                ? route('admin.catalog-types.show', $selectedTypeId)
                : ($selectedTypeId > 0
                    ? route('admin.catalog-types.show', $selectedTypeId)
                    : route('admin.catalog.index')));
    @endphp

    @livewire('admin.catalog.service-form', [
        'selectedTypeId' => (int) $selectedTypeId,
        'selectedCategoryId' => $selectedCategoryId ? (int) $selectedCategoryId : null,
        'returnUrl' => $returnUrl,
    ])
@endsection
