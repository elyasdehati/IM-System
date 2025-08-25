@extends('admin.admin_master')
@section('admin')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<style>
    .form-check-label{
        text-transform: capitalize;
    }
</style>

<div class="content">

                    <!-- Start Content-->
                    <div class="container-xxl">

                        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-18 fw-semibold m-0">Edit Role in Permission</h4>
                            </div>
            
                            <div class="text-end">
                                <ol class="breadcrumb m-0 py-0">
                                   
                                    <li class="breadcrumb-item active">Edit Role in Permission</li>
                                </ol>
                            </div>
                        </div>

                        <!-- Form Validation -->
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Edit Role in Permission</h5>
                                    </div><!-- end card header -->
        
        <div class="card-body">
            <form action="{{ route('admin.roles.update', $role->id) }}" method="post" class="row g-3" enctype="multipart/form-data">
                @csrf

                <div class="col-md-6">
                    <label for="validationDefault01" class="form-label">Role Name</label>
                    <h4>{{ $role->name }}</h4>
                </div>

                <div class="form-check mb-2">
                    <input type="checkbox" class="form-check-input" id="formCheck1">
                    <label for="formCheck1" class="form-check-label">Permission All</label>
                </div><hr>

                @foreach ($permission_group as $group)
                    <div class="row">
                        <div class="col-3">

                            @php
                                $permissions = App\Models\User::getpermissionByGroupName($group->group_name)
                            @endphp

                            <div class="form-check mb-2">
                                <input type="checkbox" class="form-check-input" id="flexCheckDefault" {{ App\Models\User::roleHasPermissions($role, $permissions) ? 'checked' : '' }}>
                                <label for="flexCheckDefault" class="form-check-label">{{ $group->group_name }}</label>
                            </div>
                        </div>

                        <div class="col-9">

                            @foreach ($permissions as $permission)
                                <div class="form-check mb-2">
                                    <input type="checkbox" name="permission[]" value="{{ $permission->id }}" class="form-check-input" id="flexCheckDefault{{ $permission->id }}" {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                    <label for="flexCheckDefault{{ $permission->id }}" class="form-check-label">{{ $permission->name }}</label>
                                </div>
                            @endforeach
                            <br>

                        </div>
                    </div>
                    {{-- // End Row --}}
                @endforeach

                <div class="col-12">
                    <button class="btn btn-primary" type="submit">Save Change</button>
                </div>
            </form>
        </div> <!-- end card-body -->
                                </div> <!-- end card-->
                            </div> <!-- end col -->

                        </div>

                    </div> <!-- container-fluid -->

                </div>

<script>
    $('#formCheck1').click(function(){
        if($(this).is(':checked')){
            $('input[type=checkbox]').prop('checked', true)
        }else{
            $('input[type=checkbox]').prop('checked', false)
        }
    })
</script>

@endsection