@extends('admin.layouts.grid')
@section('section', translate('Navigation'))
@section('title', translate('Navbar Top Menu'))
@section('create', route('admin.navigation.navbar-tops.create'))
@section('container', 'container-max-lg')
@section('content')
    @if ($navbarTops->count() > 0)
        <div class="card border-0">
            <div class="dd nestable">
                <ol class="dd-list">
                    @foreach ($navbarTops as $navbarTop)
                        <li class="dd-item" data-id="{{ $navbarTop->id }}">
                            <div class="dd-handle">
                                <span class="drag-indicator"></span>
                                <span class="dd-title">{{ $navbarTop->name }}</span>
                                <div class="dd-nodrag ms-auto">
                                    <a href="{{ route('admin.navigation.navbar-tops.edit', $navbarTop->id) }}"
                                        class="btn btn-sm btn-secondary me-2"><i
                                            class="fa-regular fa-pen-to-square"></i></a>
                                    <form class="d-inline"
                                        action="{{ route('admin.navigation.navbar-tops.destroy', $navbarTop->id) }}"
                                        method="POST">
                                        @method('DELETE')
                                        @csrf
                                        <button class="action-confirm btn btn-sm btn-danger"><i
                                                class="far fa-trash-alt"></i></button>
                                    </form>
                                </div>
                            </div>
                            @if (count($navbarTop->children))
                                <ol class="dd-list">
                                    @foreach ($navbarTop->children as $child)
                                        <li class="dd-item" data-id="{{ $child->id }}">
                                            <div class="dd-handle">
                                                <span class="drag-indicator"></span>
                                                <span class="dd-title">{{ $child->name }}</span>
                                                <div class="dd-nodrag ms-auto">
                                                    <a href="{{ route('admin.navigation.navbar-tops.edit', $child->id) }}"
                                                        class="btn btn-sm btn-secondary me-2"><i
                                                            class="fa-regular fa-pen-to-square"></i></a>
                                                    <form class="d-inline"
                                                        action="{{ route('admin.navigation.navbar-tops.destroy', $child->id) }}"
                                                        method="POST">
                                                        @method('DELETE')
                                                        @csrf
                                                        <button class="action-confirm btn btn-sm btn-danger"><i
                                                                class="far fa-trash-alt"></i></button>
                                                    </form>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ol>
                            @endif
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                @include('admin.partials.empty', ['empty_classes' => 'empty-lg'])
            </div>
        </div>
    @endif
    @push('top_scripts')
        <script>
            const sortingRoute = "{{ route('admin.navigation.navbar-tops.nestable') }}";
            const nestableMaxDepth = 2;
        </script>
    @endpush
    @push('styles_libs')
        <link rel="stylesheet" href="{{ asset('vendor/libs/jquery/nestable/jquery.nestable.min.css') }}">
    @endpush
    @push('scripts_libs')
        <script src="{{ asset('vendor/libs/jquery/nestable/jquery.nestable.min.js') }}"></script>
    @endpush
@endsection
