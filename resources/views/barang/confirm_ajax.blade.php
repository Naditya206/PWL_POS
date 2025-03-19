@if (empty($level))
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">Kesalahan</h5>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger">
                Data level tidak ditemukan.
            </div>
        </div>
    </div>
</div>
@else
<form id="form-delete-level" method="POST">
    @csrf
    @method('DELETE')
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Konfirmasi Hapus Level</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="alert alert-warning">
                    <p>Apakah Anda yakin ingin menghapus level berikut?</p>
                </div>

                <table class="table table-bordered">
                    <tr>
                        <th>Kode Level</th>
                        <td>{{ $level->level_kode }}</td>
                    </tr>
                    <tr>
                        <th>Nama Level</th>
                        <td>{{ $level->level_nama }}</td>
                    </tr>
                    {{-- Tambahkan field lain kalau ada --}}
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger" id="btn-confirm-delete-level" data-id="{{ $level->level_id }}">Hapus</button>
            </div>
        </div>
    </div>
</form>
@endif
