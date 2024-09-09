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
                        @foreach ($permissionsGroupedByParent as $parent => $features)
                        <div class="mb-4">
                            <div class="parent-group">
                                <label>
                                    <strong>{{ ucfirst($parent) }}</strong>
                                </label>
                            </div>

                            <div class="ml-4">
                                @foreach ($features as $feature => $permissions)
                                <div class="mb-3">
                                    <div class="feature-group">
                                        <label>
                                            <input
                                                type="checkbox"
                                                class="feature-checkbox"
                                                data-parent="{{ $parent }}"
                                                data-feature="{{ $feature }}">
                                            <strong>{{ ucfirst($feature) }}</strong>
                                        </label>
                                    </div>

                                    <div class="ml-4">
                                        @foreach ($permissions as $permission)
                                        <div class="permission-item">
                                            <label>
                                                <input
                                                    type="checkbox"
                                                    name="permission[]"
                                                    class="permission-checkbox"
                                                    data-feature="{{ $feature }}"
                                                    value="{{ $permission->id }}"
                                                    {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                                {{ ucfirst($permission->name) }}
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-12 text-right">
                            <a href="{{ route('roles.list') }}">
                                <button type="button" class="btn btn-danger">Back</button>
                            </a>
                            <button type="submit" class="btn btn-primary">Update</button>
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
// Function to update feature checkboxes based on permissions
function updateFeatureCheckboxes() {
    $('.feature-checkbox').each(function() {
        let feature = $(this).data('feature');
        let allPermissionsChecked = $(`.permission-checkbox[data-feature="${feature}"]`).length === $(`.permission-checkbox[data-feature="${feature}"]`).filter(':checked').length;
        $(this).prop('checked', allPermissionsChecked);
    });
}

// Handle feature checkbox toggle
$('.feature-checkbox').on('change', function () {
    let feature = $(this).data('feature');
    let checked = $(this).is(':checked');
    $(`.permission-checkbox[data-feature="${feature}"]`).prop('checked', checked);
});

// Handle permission checkbox toggle
$('.permission-checkbox').on('change', function () {
    let feature = $(this).data('feature');
    let permissionName = $(this).parent().text().toLowerCase();
    
    // Automatically check "view" permission if "edit", "delete", "create", or "detail" is checked
    if (permissionName.includes('edit') || permissionName.includes('delete') || permissionName.includes('create') || permissionName.includes('detail')) {
        let viewCheckbox = $(`.permission-checkbox[data-feature="${feature}"]`).filter(function() {
            return $(this).parent().text().toLowerCase().includes('view');
        });
        viewCheckbox.prop('checked', true);
        viewCheckbox.prop('disabled', true); // Disable the "view" checkbox
    }

    // Check if all edit/create/delete/detail are unchecked, then enable the view checkbox
    let relatedPermissionsUnchecked = $(`.permission-checkbox[data-feature="${feature}"]`).filter(function() {
        return $(this).parent().text().toLowerCase().includes('edit') || $(this).parent().text().toLowerCase().includes('delete') || $(this).parent().text().toLowerCase().includes('create') || $(this).parent().text().toLowerCase().includes('detail');
    }).filter(':checked').length === 0;
    
    if (relatedPermissionsUnchecked) {
        let viewCheckbox = $(`.permission-checkbox[data-feature="${feature}"]`).filter(function() {
            return $(this).parent().text().toLowerCase().includes('view');
        });
        viewCheckbox.prop('disabled', false); // Enable the "view" checkbox
    }

    // Update the feature checkbox based on individual permissions
    let allPermissionsChecked = $(`.permission-checkbox[data-feature="${feature}"]`).length === $(`.permission-checkbox[data-feature="${feature}"]`).filter(':checked').length;
    $(`.feature-checkbox[data-feature="${feature}"]`).prop('checked', allPermissionsChecked);
});

// Initialize feature checkboxes on page load
$(document).ready(function() {
    updateFeatureCheckboxes();
});
</script>

@endsection
