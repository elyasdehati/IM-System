@extends('admin.admin_master')
@section('admin')

<div class="content">

                    <!-- Start Content-->
                    <div class="container-xxl">

                        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-18 fw-semibold m-0">All Purchase</h4>
                            </div>
            
                            <div class="text-end">
                                <ol class="breadcrumb m-0 py-0">
                                    <a href="{{ route('add.purchase') }}" class="btn btn-secondary">Add Purchase</a>
                                </ol>
                            </div>
                        </div>

                        <!-- Datatables  -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">

                                    <div class="card-header">
                                        
                                    </div><!-- end card header -->

            <div class="card-body">
                <table id="datatable" class="table table-bordered dt-responsive table-responsive nowrap">
                    <thead>
                    <tr>
                        <th>SI</th>
                        <th>WareHouse</th>
                        <th>Status</th>
                        <th>Grand Total</th>
                        <th>Payment</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
@foreach ($allData as $key=> $item)
    <tr>
        <td>{{ $key+1 }}</td>
        <td>{{ $item['warehouse']['name'] }}</td>
        <td>{{ $item->status }}</td>
        <td>${{ $item->grand_total }}</td>
        <td>Cash</td>
        <td>{{  \Carbon\Carbon::parse($item->created_at)->format('Y-m-ds') }}</td>
        <td>
            <a title="Details" href="{{ route('details.product', $item->id) }}" class="btn btn-info btn-sm"><span class="mdi mdi-eye-circle mdi-18px"></span></a>

            <a title="PDF Invoice" href="{{ route('details.product', $item->id) }}" class="btn btn-primary btn-sm"><span class="mdi mdi-download-circle mdi-18px"></span></a>

            <a title="Edit" href="{{ route('edit.product', $item->id) }}" class="btn btn-success btn-sm"><span class="mdi mdi-book-edit mdi-18px"></span></a>

            <a title="Delete" href="{{ route('delete.product', $item->id) }}" id="delete" class="btn btn-danger btn-sm"><span class="mdi mdi-delete-circle mdi-18px"></span></a>
        </td>
    </tr>
@endforeach
                    </tbody>
                </table>
            </div>

                                </div>
                            </div>
                        </div>


                    </div> <!-- container-fluid -->

                </div> <!-- content -->

@endsection