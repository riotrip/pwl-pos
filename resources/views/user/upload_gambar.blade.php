<form action="{{ url('/user/upload_gambar') }}" method="POST" id="form-upload-gambar" enctype="multipart/form-data">
    @csrf
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Gambar Profil</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <label for="gambar">Pilih Gambar</label>
                    <input type="file" name="gambar" id="gambar" class="form-control" accept="image/*" required />
                    <small id="error-gambar" class="form-text text-danger"></small>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Gambar</button>
            </div>
        </div>
    </div>
</form>

<script>
    $('#form-upload-gambar').submit(function (e) {
        e.preventDefault();
        $('#error-gambar').text('');

        let form = this;
        let formData = new FormData(form);

        Swal.fire({
            title: 'Mengunggah...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: form.action,
            type: form.method,
            data: formData,
            processData: false,
            contentType: false,
            success: function (res) {
                if (res.success) {
                    $('#modalGambar').modal('hide');
                    Swal.fire('Berhasil', res.message, 'success').then(() => {
                        location.reload(); // Refresh page biar gambar baru muncul
                    });
                }
            },
            error: function (xhr) {
                let err = xhr.responseJSON.errors;
                if (err && err.gambar) {
                    $('#error-gambar').text(err.gambar[0]);
                }
                Swal.fire('Gagal', 'Terjadi kesalahan saat mengunggah.', 'error');
            }
        });
    });
</script>
