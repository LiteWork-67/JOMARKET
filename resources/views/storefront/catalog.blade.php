@extends('layouts.storefront')

@section('title', 'دليل المنتجات والتسوق — ' . __('messages.app_name'))

@section('content')
    <livewire:product-catalog
        :initialSearch="$initialSearch ?? ''"
        :initialCategory="$initialCategory ?? ''"
        :initialBrand="$initialBrand ?? ''"
    />
@endsection
