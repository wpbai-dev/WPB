@extends('admin.layouts.grid')
@section('section', translate('Faker'))
@section('title', translate('Fake Item Comments'))
@section('container', 'container-max-lg')
@section('back', route('admin.faker.tools.index'))
@section('content')
    <div class="alert alert-primary">
        <h5>{{ translate('Notes!') }}</h5>
        <ol class="m-0">
            <li>
                {!! translate('You must have an active users to use this tool or you can use :tool to generate them.', [
                    'tool' => '<a href="' . route('admin.faker.tools.tool', 'users') . '">' . translate('fake users tool') . '</a>',
                ]) !!}
            </li>
        </ol>
    </div>
    <div class="card mb-3">
        <div class="card-body p-4">
            <form action="{{ route('admin.faker.tools.generate', $tool) }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">{{ translate('Item') }}</label>
                        <select name="item" class="form-select form-select-md selectpicker" title="--"
                            data-live-search="true" required>
                            @foreach (\App\Models\Item::all() as $item)
                                <option value="{{ $item->id }}" @selected(old('item') == $item->id)>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ translate('Number of comments') }}</label>
                        <input type="number" name="comments_number" class="form-control form-control-md"
                            value="{{ old('comments_number') ?? '10' }}" required>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ translate('Date from') }}</label>
                        <input type="date" name="date_from" class="form-control form-control-md"
                            value="{{ old('date_from') }}" required>
                    </div>
                    <div class="col-lg-6">
                        <label class="form-label">{{ translate('Date to') }}</label>
                        <input type="date" name="date_to" class="form-control form-control-md"
                            value="{{ old('date_to') }}" required>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="with_admin_reply" id="adminReply"
                                @checked(old('with_admin_reply'))>
                            <label class="form-check-label" for="adminReply">{{ translate('With Admin Reply') }}</label>
                        </div>
                    </div>
                    <div class="col-12">
                        <button class="btn btn-primary btn-md"><i
                                class="fa-solid fa-rotate me-2"></i>{{ translate('Generate') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @push('styles_libs')
        <link rel="stylesheet" href="{{ asset('vendor/libs/bootstrap/select/bootstrap-select.min.css') }}">
    @endpush
    @push('scripts_libs')
        <script src="{{ asset('vendor/libs/bootstrap/select/bootstrap-select.min.js') }}"></script>
    @endpush
@endsection
