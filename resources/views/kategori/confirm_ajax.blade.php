@if (empty($kategori))
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">Kesalahan</h5>
        </div>
        <div class="modal-body">
            <div class="alert alert-danger">
                Data kategori barang tidak ditemukan.
            </div>
        </div>
    </div>
</div>
@else
<form id="form-delete-kategori" method="POST">
    @csrf
    @method('DELETE')
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Konfirmasi Hapus Kategori Barang</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="alert alert-warning">
                    <p>Apakah Anda yakin ingin menghapus kategori barang berikut?</p>
                </div>

                <table class="table table-bordered">
                    <tr>
                        <th>Kode Kategori</th>
                        <td>{{ $kategori->kategori_kode }}</td>
                    </tr>
                    <tr>
                        <th>Nama Kategori</th>
                        <td>{{ $kategori->kategori_nama }}</td>
                    </tr>
                    {{-- Tambahkan field lain kalau ada --}}
                </table>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger" id="btn-confirm-delete-kategori" data-id="{{ $kategori->kategori_id }}">Hapus</button>
            </div>
        </div>
    </div>
</form>
@endif
