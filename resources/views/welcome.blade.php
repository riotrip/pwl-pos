@extends('layouts.template')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Halo!, apakabar!!!</h3>
        <div class="card-tools d-flex justify-content-end">
            <button class="btn btn-sm btn-info mt-1" onclick="modalAction('{{ url('user/gambar') }}')">Upload Gambar</button>
        </div>
        </div>
        <div class="card-body">
            Selamat datang semua, ini adalah halaman utama dari aplikasi ini.
        </div>
    </div>
@endsection @push('css') @endpush @push('js')
<div id="modalGambar" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div>
<script>
    function modalAction(url = ''){ $('#modalGambar').load(url,function(){ $('#modalGambar').modal('show'); }); }
</script>
@endpush