<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Gambar Profil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .file-input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .preview-image {
            object-fit: cover;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="card-title text-center mb-4">Upload Gambar Profil</h4>
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="border rounded p-2 h-100">
                                    <p class="text-center fw-bold text-muted mb-2">Gambar Saat Ini</p>
                                    @auth
                                    @if(Auth::user()->image)
                                    <img src="{{ asset('uploads/image_profile/' . Auth::user()->image) }}"
                                        class="img-thumbnail preview-image img-fluid w-100">
                                    @else
                                    <div class="d-flex align-items-center justify-content-center bg-light"
                                        style="height: 200px">
                                        <span class="text-muted">Tidak ada gambar</span>
                                    </div>
                                    @endif
                                    @else
                                    <div class="d-flex align-items-center justify-content-center bg-light"
                                        style="height: 200px">
                                        <span class="text-muted">Silakan login</span>
                                    </div>
                                    @endauth
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="border rounded p-2 h-100">
                                    <p class="text-center fw-bold text-muted mb-2">Gambar Baru</p>
                                    <div id="newImagePreview" class=" img-fluid d-flex align-items-center justify-content-center bg-light"
                                        style="height: 200px">
                                        <span class="text-muted">Belum dipilih</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap justify-content-center gap-2 mb-3">
                            <div class="position-relative d-inline-block">
                                <button type="button" class="btn btn-primary">
                                    <i class="fas fa-upload me-2"></i> Pilih Gambar
                                </button>
                                <input type="file" id="imageInput"
                                    class="position-absolute top-0 start-0 w-100 h-100 opacity-0"
                                    accept="image/*"
                                    onchange="previewNewImage(this)">
                            </div>

                            <button type="button" class="btn btn-outline-warning" id="cancelUpload"
                                onclick="cancelUpload()" style="display: none">
                                <i class="fas fa-times me-2"></i> Batal
                            </button>

                            @auth
                            @if(Auth::user()->image)
                            <button type="button" class="btn btn-danger" onclick="deleteImage()" id="deleteBtn">
                                <i class="fas fa-trash me-2"></i> Hapus
                            </button>
                            @endif
                            @endauth
                        </div>

                        <p class="text-center text-muted small mb-4">
                            Format: JPEG, PNG, JPG, GIF (Maksimal: 2MB)
                        </p>

                        <div class="progress mb-3" id="progressBar" style="display: none; height: 20px">
                            <div class="progress-bar progress-bar-striped progress-bar-animated"
                                role="progressbar" style="width: 0%"></div>
                        </div>

                        <div id="statusMessage" class="alert mb-3" style="display: none"></div>

                        <div class="d-flex justify-content-between">
                            <a href="/" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i> Kembali
                            </a>
                            <button type="button" class="btn btn-success" id="confirmUpload"
                                onclick="uploadImage()" style="display: none">
                                <i class="fas fa-check me-2"></i> Konfirmasi Upload
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let selectedFile = null;

        function previewNewImage(input) {
            const file = input.files[0];
            if (!file) return;

            if (file.size > 2048 * 1024) {
                showMessage('Ukuran file maksimal 2MB', 'danger');
                return;
            }

            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                showMessage('Format file tidak didukung', 'danger');
                return;
            }

            selectedFile = file;

            const reader = new FileReader();
            reader.onload = function(e) {
                $('#newImagePreview').html(
                    `<img src="${e.target.result}" class="img-thumbnail preview-image w-100" style="height: 200px">`
                );
                $('#cancelUpload').show();
                $('#confirmUpload').show();
            }
            reader.readAsDataURL(file);
        }

        function cancelUpload() {
            $('#imageInput').val('');
            $('#newImagePreview').html(
                '<div class="d-flex align-items-center justify-content-center bg-light" style="height: 200px">' +
                '<span class="text-muted">Belum dipilih</span></div>'
            );
            $('#cancelUpload').hide();
            $('#confirmUpload').hide();
            selectedFile = null;
            $('#statusMessage').hide();
        }

        function uploadImage() {
            if (!selectedFile) return;

            $('#progressBar').show();
            $('#confirmUpload').prop('disabled', true);

            const formData = new FormData();
            formData.append('image', selectedFile);
            formData.append('_token', '{{ csrf_token() }}');

            $.ajax({
                url: '/user/upload_image',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            const percent = Math.round((e.loaded / e.total) * 100);
                            $('.progress-bar').css('width', percent + '%').text(percent + '%');
                        }
                    });
                    return xhr;
                },
                success: function(response) {
                    if (response.success) {
                        showMessage('Gambar berhasil diupload!', 'success');
                        if (response.filename) {
                            $('.preview-image').first().attr('src', '{{ asset("uploads/image_profile") }}/' + response.filename);
                            if (!$('#deleteBtn').length) {
                                $('.btn-primary').after(
                                    `<button type="button" class="btn btn-danger" onclick="deleteImage()" id="deleteBtn">` +
                                    '<i class="fas fa-trash me-2"></i> Hapus</button>'
                                );
                            }
                        }
                        cancelUpload();
                    } else {
                        showMessage('Gagal mengupload gambar', 'danger');
                    }
                    $('#progressBar').hide();
                    $('#confirmUpload').prop('disabled', false);
                },
                error: function(xhr) {
                    showMessage('Error: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'), 'danger');
                    $('#progressBar').hide();
                    $('#confirmUpload').prop('disabled', false);
                }
            });
        }

        function deleteImage() {
            if (confirm('Apakah Anda yakin ingin menghapus gambar profil saat ini?')) {
                $.ajax({
                    url: '/user/delete_image',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            showMessage('Gambar berhasil dihapus!', 'success');
                            $('.preview-image').first().replaceWith(
                                '<div class="d-flex align-items-center justify-content-center bg-light" style="height: 200px">' +
                                '<span class="text-muted">Tidak ada gambar</span></div>'
                            );
                            $('#deleteBtn').remove();
                        } else {
                            showMessage(response.message || 'Gagal menghapus gambar', 'danger');
                        }
                    },
                    error: function(xhr) {
                        showMessage('Error: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan'), 'danger');
                    }
                });
            }
        }

        function showMessage(text, type) {
            const messageDiv = $('#statusMessage');
            messageDiv.removeClass('alert-success alert-danger')
                .addClass('alert-' + type)
                .text(text)
                .show();
        }
    </script>
</body>

</html>