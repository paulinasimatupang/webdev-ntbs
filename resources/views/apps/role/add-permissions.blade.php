@extends('layouts.master')

@section('main-content')
<div class="breadcrumb">
    <h1>Role: {{ $role->name }}</h1>
</div>
<div class="separator-breadcrumb border-top"></div>

@if (session('status'))
<div class="alert alert-success">{{ session('status') }}</div>
@endif

@if ($errors->any())
<ul class="alert alert-warning">
    @foreach ($errors->all() as $error)
    <li>{{ $error }}</li>
    @endforeach
</ul>
@endif

<div class="row">
    <div class="col-md-12">
        <div class="card mb-5">
            <div class="card-body">
                <form action="{{ route('roles.givePermissionToRole', $role->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        @foreach ($groupedPermissions as $parentFeature => $subFeatures)
                            <div class="mb-4">
                                <div class="parent-group">
                                    <label>
                                        <input
                                            type="checkbox"
                                            class="feature-checkbox"
                                            data-feature="{{ $parentFeature }}">
                                        <strong>{{ ucfirst($parentFeature) }}</strong>
                                    </label>
                                </div>

                                @if (is_array($subFeatures) && !isset($subFeatures[0]))
                                    @foreach ($subFeatures as $subFeatureName => $permissions)
                                        <div class="mb-3 ml-4">
                                            <div class="feature-group">
                                                <label>
                                                    <input
                                                        type="checkbox"
                                                        class="sub-feature-checkbox"
                                                        data-feature="{{ $parentFeature }}"
                                                        data-sub-feature="{{ $subFeatureName }}">
                                                    <strong>{{ ucfirst($subFeatureName) }}</strong>
                                                </label>
                                            </div>

                                            <div class="ml-8">
                                                @foreach ($permissions as $permission)
                                                    <div class="permission-item">
                                                        <label>
                                                            <input
                                                                type="checkbox"
                                                                name="permission[]"
                                                                class="permission-checkbox"
                                                                data-feature="{{ $parentFeature }}"
                                                                data-sub-feature="{{ $subFeatureName }}"
                                                                value="{{ $permission->id }}"
                                                                {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                                            {{ ucfirst($permission->name) }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="ml-4">
                                        @foreach ($subFeatures as $permission)
                                            <div class="permission-item">
                                                <label>
                                                    <input
                                                        type="checkbox"
                                                        name="permission[]"
                                                        class="permission-checkbox"
                                                        data-feature="{{ $parentFeature }}"
                                                        value="{{ $permission->id }}"
                                                        {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                                    {{ ucfirst($permission->name) }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-12 text-right">
                            <a href="{{ route('roles.list') }}">
                                <button type="button" class="btn btn-secondary">Kembali</button>
                            </a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('bottom-js')

<script>
function updateFeatureCheckboxes() {
    $('.feature-checkbox').each(function() {
        let feature = $(this).data('feature');
        let allPermissionsChecked = $(`.permission-checkbox[data-feature="${feature}"]`).length === $(`.permission-checkbox[data-feature="${feature}"]`).filter(':checked').length;
        $(this).prop('checked', allPermissionsChecked);

        $('.sub-feature-checkbox[data-feature="' + feature + '"]').each(function() {
            let subFeature = $(this).data('sub-feature');
            let allSubFeaturePermissionsChecked = $(`.permission-checkbox[data-feature="${feature}"][data-sub-feature="${subFeature}"]`).length === $(`.permission-checkbox[data-feature="${feature}"][data-sub-feature="${subFeature}"]`).filter(':checked').length;
            $(this).prop('checked', allSubFeaturePermissionsChecked);
        });
    });
}

$('.feature-checkbox').on('change', function () {
    let feature = $(this).data('feature');
    let checked = $(this).is(':checked');
    $(`.sub-feature-checkbox[data-feature="${feature}"], .permission-checkbox[data-feature="${feature}"]`).prop('checked', checked);
});

$('.sub-feature-checkbox').on('change', function () {
    let feature = $(this).data('feature');
    let subFeature = $(this).data('sub-feature');
    let checked = $(this).is(':checked');
    $(`.permission-checkbox[data-feature="${feature}"][data-sub-feature="${subFeature}"]`).prop('checked', checked);
});

$('.permission-checkbox').on('change', function () {
    let feature = $(this).data('feature');
    let subFeature = $(this).data('sub-feature');
    let permissionName = $(this).parent().text().toLowerCase();

    if (permissionName.includes('edit') || permissionName.includes('delete') || permissionName.includes('create') ||  permissionName.includes('detail')) {
        let viewCheckbox;
        if (subFeature) {
            viewCheckbox = $(`.permission-checkbox[data-feature="${feature}"][data-sub-feature="${subFeature}"]`).filter(function() {
                return $(this).parent().text().toLowerCase().includes('view');
            });
        } else {
            viewCheckbox = $(`.permission-checkbox[data-feature="${feature}"]`).filter(function() {
                return $(this).parent().text().toLowerCase().includes('view');
            });
        }
        viewCheckbox.prop('checked', true);
        viewCheckbox.prop('disabled', true);
        if (!viewCheckbox.next('input[type="hidden"]').length) {
            $('<input>').attr({
                type: 'hidden',
                name: 'permission[]',
                value: viewCheckbox.val()
            }).insertAfter(viewCheckbox);
        }
    }

    let relatedPermissionsUnchecked;
    if (subFeature) {
        relatedPermissionsUnchecked = $(`.permission-checkbox[data-feature="${feature}"][data-sub-feature="${subFeature}"]`).filter(function() {
            return $(this).parent().text().toLowerCase().includes('edit') || 
                   $(this).parent().text().toLowerCase().includes('delete') || 
                   $(this).parent().text().toLowerCase().includes('create');
        }).filter(':checked').length === 0;
    } else {
        relatedPermissionsUnchecked = $(`.permission-checkbox[data-feature="${feature}"]`).filter(function() {
            return $(this).parent().text().toLowerCase().includes('edit') || 
                   $(this).parent().text().toLowerCase().includes('delete') || 
                   $(this).parent().text().toLowerCase().includes('create');
        }).filter(':checked').length === 0;
    }

    if (relatedPermissionsUnchecked) {
        let viewCheckbox;
        if (subFeature) {
            viewCheckbox = $(`.permission-checkbox[data-feature="${feature}"][data-sub-feature="${subFeature}"]`).filter(function() {
                return $(this).parent().text().toLowerCase().includes('view');
            });
        } else {
            viewCheckbox = $(`.permission-checkbox[data-feature="${feature}"]`).filter(function() {
                return $(this).parent().text().toLowerCase().includes('view');
            });
        }
        viewCheckbox.prop('disabled', false);
    }

    let allPermissionsChecked;
    if (subFeature) {
        allPermissionsChecked = $(`.permission-checkbox[data-feature="${feature}"][data-sub-feature="${subFeature}"]`).length === $(`.permission-checkbox[data-feature="${feature}"][data-sub-feature="${subFeature}"]`).filter(':checked').length;
        $(`.sub-feature-checkbox[data-feature="${feature}"][data-sub-feature="${subFeature}"]`).prop('checked', allPermissionsChecked);
    } else {
        allPermissionsChecked = $(`.permission-checkbox[data-feature="${feature}"]`).length === $(`.permission-checkbox[data-feature="${feature}"]`).filter(':checked').length;
        $(`.feature-checkbox[data-feature="${feature}"]`).prop('checked', allPermissionsChecked);
    }

    let allSubFeaturesChecked = $(`.sub-feature-checkbox[data-feature="${feature}"]`).length === $(`.sub-feature-checkbox[data-feature="${feature}"]`).filter(':checked').length;
    $(`.feature-checkbox[data-feature="${feature}"]`).prop('checked', allSubFeaturesChecked);
});


$(document).ready(function() {
    updateFeatureCheckboxes();
});
</script>

@endsection