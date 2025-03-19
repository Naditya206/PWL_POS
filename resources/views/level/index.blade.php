@extends('layouts.template')

@section('content')
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ $page->title }}</h3>
        <div class="card-tools">
            <button onclick="modalAction('{{ route('level.create_ajax') }}')" class="btn btn-success btn-sm">Tambah Ajax</button>
        </div>
    </div>

    <div class="card-body">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered table-hover table-sm" id="table_level">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Level</th> {{-- tambahin kode biar lengkap --}}
                    <th>Nama Level</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

{{-- Modal --}}
<div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function modalAction(url = '') {
        $('#myModal').load(url, function() {
            $('#myModal').modal('show');
        });
    }

    $(document).ready(function() {
        const tableLevel = $('#table_level').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('level.list') }}",
                type: "POST",
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                error: function(xhr, error, thrown) {
                    console.log("AJAX Error:", xhr.responseText);
                    Swal.fire("Error!", "Gagal mengambil data dari server!", "error");
                }
            },
            columns: [
                { data: 'DT_RowIndex', className: 'text-center', orderable: false, searchable: false },
                { data: 'level_kode' }, // tambahin kalau ada kode level
                { data: 'level_nama' },
                { data: 'aksi', className: 'text-center', orderable: false, searchable: false }
            ]
        });
    });

    

    function hapusLevel(id) {
        Swal.fire({
            title: 'Yakin mau hapus?',
            text: "Data level tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/level/${id}/delete_ajax`,
                    type: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(res) {
                        if (res.status) {
                            Swal.fire('Berhasil!', res.message, 'success');
                            $('#table_level').DataTable().ajax.reload();
                        } else {
                            Swal.fire('Gagal!', res.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        Swal.fire('Error!', 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    }
</script>
@endpush
