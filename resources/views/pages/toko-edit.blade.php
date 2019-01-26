@extends('layouts.main')
@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">Home</li>
        <li class="breadcrumb-item active">Toko-edit</li>
    </ol>
    <div class="container-fluid">
        <div class="animated fadeIn">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <form action="{{url('toko/edit/' . $toko->id)}}" method="post" class="form-horizontal">
                            <div class="card-header">
                                <i class="fa fa-align-justify"></i> Edit Data Toko
                            </div>
                            <div class="card-block">
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        {{csrf_field()}}
                                        <div class="form-group row">
                                            <label class="col-sm-3 form-control-label" for="nama_toko">Nama Toko</label>
                                            <div class="col-sm-6">
                                                <input type="text" id="nama_toko" name="nama" class="form-control input-sm" value="{{$toko->nama}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-dot-circle-o"></i> Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection