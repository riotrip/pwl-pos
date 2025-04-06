<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register Pengguna</title>

    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <!-- SweetAlert2 -->
    <link rel="stylesheet"
        href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center"><a href="{{ url('/') }}" class="h1"><b>Admin</b>LTE</a></div>
            <div class="card-body">
                <p class="login-box-msg">Register akun baru</p>

                <form action="{{ route('register') }}" method="POST" id="form-register">
                    @csrf

                    <div class="input-group mb-3">
                        <input type="text" name="username" class="form-control" placeholder="Username">
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-user"></span></div>
                        </div>
                    </div>
                    <small class="text-danger" id="error-username"></small>

                    <div class="input-group mb-3">
                        <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap">
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-id-badge"></span></div>
                        </div>
                    </div>
                    <small class="text-danger" id="error-nama"></small>

                    <div class="input-group mb-3">
                        <select name="level_id" class="form-control">
                            <option value="">-- Pilih Level --</option>
                            @foreach ($levels as $level)
                            <option value="{{ $level->level_id }}">{{ $level->level_nama }} ({{ $level->level_kode }})</option>
                            @endforeach
                        </select>
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-user-tag"></span></div>
                        </div>
                    </div>
                    <small class="text-danger" id="error-level_id"></small>

                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-lock"></span></div>
                        </div>
                    </div>
                    <small class="text-danger" id="error-password"></small>

                    <div class="input-group mb-3">
                        <input type="password" name="password_confirmation" class="form-control"
                            placeholder="Konfirmasi Password">
                        <div class="input-group-append">
                            <div class="input-group-text"><span class="fas fa-check-circle"></span></div>
                        </div>
                    </div>
                    <small class="text-danger" id="error-password_confirmation"></small>

                    <div class="row">
                        <div class="col-8">
                            <a href="{{ route('login') }}">Sudah punya akun?</a>
                        </div>
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Daftar</button>
                        </div>
                    </div>
                </form>
            </div> <!-- /.card-body -->
        </div> <!-- /.card -->
    </div> <!-- /.login-box -->

    <!-- JS -->
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/jquery-validation/additional-methods.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $("#form-register").validate({
                rules: {
                    username: {
                        required: true,
                        minlength: 4,
                        maxlength: 20
                    },
                    nama: {
                        required: true,
                        minlength: 3,
                        maxlength: 100
                    },
                    level_id: {
                        required: true
                    },
                    password: {
                        required: true,
                        minlength: 5
                    },
                    password_confirmation: {
                        equalTo: '[name="password"]'
                    }
                },
                messages: {
                    password_confirmation: {
                        equalTo: "Konfirmasi password tidak cocok"
                    }
                },
                submitHandler: function(form) {
                    form.submit(); // Form tetap pakai POST biasa, bukan AJAX
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.input-group').after(error);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>

    @if ($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            text: '{{ $errors->first() }}'
        });
    </script>
    @endif

</body>

</html>