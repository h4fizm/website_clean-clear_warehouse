@extends('dashboard_page.main')
@section('title', 'Daftar Pengguna')
@section('content')

{{-- Welcome Section --}}
<div class="col-12 mb-3">
    <div class="card p-4 position-relative welcome-card">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center p-0">
            <div class="w-100 order-md-1 text-center text-md-start">
                <h4 class="mb-1 fw-bold" id="summary-title">
                    Daftar Pengguna
                </h4>
                <p class="mb-2 opacity-8" id="summary-text">
                    Kelola data pengguna, hak akses, dan informasi akun.
                </p>
            </div>
            <div class="text-center text-md-end mb-3 mb-md-0 order-md-2 ms-md-auto me-md-4">
                <img src="{{ asset('dashboard_template/assets/img/icon.png') }}"
                    alt="Pertamina Patra Niaga Logo"
                    class="welcome-card-icon"
                    style="height: 60px;">
            </div>
        </div>
        <div class="welcome-card-background"></div>
    </div>
</div>

{{-- Tabel Data Pengguna --}}
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4" style="min-height: 450px;">
            <div class="card-header pb-0 d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex flex-column">
                    <h4>Tabel Data Pengguna</h4>
                    <h6>Daftar seluruh pengguna yang terdaftar dalam sistem.</h6>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2 mt-md-0 align-items-center ms-auto">
                    {{-- Search --}}
                    <input type="text" id="search-input" class="form-control form-control-sm" placeholder="Cari Nama, Email, Role..." style="width: 250px; height: 35px;">
                </div>
            </div>
            
            <div class="card-body px-0 pt-0 pb-5">
                <div class="table-responsive p-0">
                    <table class="table align-items-center mb-0" id="table-users">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nama Pengguna</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Email</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Role</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Data will be rendered here by JavaScript --}}
                        </tbody>
                    </table>
                    <div id="no-data" class="text-center text-muted py-4" style="display: none;">
                        Data Kosong
                    </div>
                </div>

                {{-- Pagination --}}
                <div class="mt-3 px-3 d-flex justify-content-center">
                    <nav aria-label="Page navigation example">
                        <ul class="pagination pagination-sm mb-0" id="pagination-users">
                            {{-- Pagination links will be rendered here by JavaScript --}}
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal for Editing User Data --}}
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit Data Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="edit-user-id">
                    <div class="mb-3">
                        <label for="edit-nama" class="form-label">Nama Pengguna</label>
                        <input type="text" class="form-control" id="edit-nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-email" class="form-label">Email Pengguna</label>
                        <input type="email" class="form-control" id="edit-email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="edit-password" placeholder="Kosongkan jika tidak ingin diubah">
                    </div>
                    <div class="mb-3">
                        <label for="edit-confirm-password" class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" id="edit-confirm-password" placeholder="Kosongkan jika tidak ingin diubah">
                    </div>
                    <div class="mb-3">
                        <label for="edit-role" class="form-label">Role</label>
                        <select class="form-select" id="edit-role" required>
                            <option value="">Pilih Role</option>
                            <option value="Manager">Manager</option>
                            <option value="Admin P.Layang">Admin P.Layang</option>
                            <option value="User P.Layang">User P.Layang</option>
                            <option value="User SA Jambi">User SA Jambi</option>
                            <option value="User SA Bengkulu">User SA Bengkulu</option>
                            <option value="User SA Lampung">User SA Lampung</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveUserChanges">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // --- Data Dummy Pengguna ---
    const dataDummyUsers = [
        { id: 1, nama: 'Muhammad Amir', email: 'amir.m@gmail.com', role: 'Manager' },
        { id: 2, nama: 'Siti Rahayu', email: 'siti.r@pertamina.com', role: 'Admin P.Layang' },
        { id: 3, nama: 'Joko Prabowo', email: 'joko.p@perusahaan.com', role: 'User P.Layang' },
        { id: 4, nama: 'Ahmad Faisal', email: 'ahmad.f@perusahaan.com', role: 'User P.Layang' },
        { id: 5, nama: 'Budi Hartanto', email: 'budi.h@spbe-jambi.com', role: 'User SA Jambi' },
        { id: 6, nama: 'Dewi Lestari', email: 'dewi.l@spbe-bengkulu.com', role: 'User SA Bengkulu' },
        { id: 7, nama: 'Eko Sudrajat', email: 'eko.s@spbe-lampung.com', role: 'User SA Lampung' },
        { id: 8, nama: 'Fatimah Az-Zahra', email: 'fatimah.a@perusahaan.com', role: 'User P.Layang' },
        { id: 9, nama: 'Gani Nurcahyo', email: 'gani.n@spbe-jambi.com', role: 'User SA Jambi' },
        { id: 10, nama: 'Hasan Basri', email: 'hasan.b@perusahaan.com', role: 'User P.Layang' },
        { id: 11, nama: 'Indah Permata', email: 'indah.p@spbe-bengkulu.com', role: 'User SA Bengkulu' },
        { id: 12, nama: 'Joni Setiawan', email: 'joni.s@spbe-lampung.com', role: 'User SA Lampung' },
    ];
    // --- END Data Dummy ---

    let searchQuery = '';
    let currentPage = 1;
    const itemsPerPage = 10;
    const maxPagesToShow = 5;

    function filterData() {
        return dataDummyUsers.filter(item => {
            const matchSearch = searchQuery ?
                                (item.nama.toLowerCase().includes(searchQuery.toLowerCase()) ||
                                item.email.toLowerCase().includes(searchQuery.toLowerCase()) ||
                                item.role.toLowerCase().includes(searchQuery.toLowerCase()))
                                : true;
            return matchSearch;
        });
    }

    function renderTable() {
        const tbody = document.querySelector('#table-users tbody');
        const noData = document.getElementById('no-data');
        const data = filterData();
        const start = (currentPage - 1) * itemsPerPage;
        const paginated = data.slice(start, start + itemsPerPage);

        tbody.innerHTML = '';
        if (paginated.length === 0) {
            noData.style.display = 'block';
        } else {
            noData.style.display = 'none';
            paginated.forEach((item, index) => {
                const rowIndex = start + index + 1;
                
                tbody.innerHTML += `
                    <tr>
                        <td class="text-center">
                            <p class="text-xs font-weight-bold mb-0">${rowIndex}</p>
                        </td>
                        <td>
                            <p class="text-xs font-weight-bold mb-0">${item.nama}</p>
                        </td>
                        <td>
                            <p class="text-xs text-secondary mb-0">${item.email}</p>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-gradient-info text-white text-xs">${item.role}</span>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-warning text-white edit-btn" data-id="${item.id}" data-bs-toggle="modal" data-bs-target="#editUserModal" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger text-white delete-btn" data-id="${item.id}" title="Hapus">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            attachActionListeners();
        }

        renderPagination(data.length);
    }

    function attachActionListeners() {
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const user = dataDummyUsers.find(u => u.id == id);
                if (user) {
                    document.getElementById('edit-user-id').value = user.id;
                    document.getElementById('edit-nama').value = user.nama;
                    document.getElementById('edit-email').value = user.email;
                    document.getElementById('edit-role').value = user.role;

                    // Clear password fields
                    document.getElementById('edit-password').value = '';
                    document.getElementById('edit-confirm-password').value = '';
                }
            });
        });

        document.getElementById('saveUserChanges').addEventListener('click', function() {
            const id = document.getElementById('edit-user-id').value;
            const nama = document.getElementById('edit-nama').value;
            const email = document.getElementById('edit-email').value;
            const password = document.getElementById('edit-password').value;
            const confirmPassword = document.getElementById('edit-confirm-password').value;
            const role = document.getElementById('edit-role').value;

            // Simple validation
            if (!nama || !email || !role) {
                Swal.fire('Gagal!', 'Nama, Email, dan Role tidak boleh kosong.', 'error');
                return;
            }

            if (password && password !== confirmPassword) {
                Swal.fire('Gagal!', 'Password dan Konfirmasi Password tidak cocok.', 'error');
                return;
            }

            const userIndex = dataDummyUsers.findIndex(u => u.id == id);
            if (userIndex > -1) {
                dataDummyUsers[userIndex].nama = nama;
                dataDummyUsers[userIndex].email = email;
                dataDummyUsers[userIndex].role = role;
                // No password update in dummy data, but in a real app, you would handle this
            }

            const myModal = bootstrap.Modal.getInstance(document.getElementById('editUserModal'));
            myModal.hide();

            Swal.fire('Berhasil!', 'Data pengguna berhasil disimpan.', 'success');
            renderTable();
        });

        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data pengguna ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire(
                            'Berhasil Dihapus!',
                            'Data pengguna telah berhasil dihapus.',
                            'success'
                        );
                        // Implement deletion logic here, e.g., filter the array
                        const userIndex = dataDummyUsers.findIndex(u => u.id == id);
                        if(userIndex > -1) {
                            dataDummyUsers.splice(userIndex, 1);
                            renderTable();
                        }
                    }
                });
            });
        });
    }

    function renderPagination(totalItems) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const ul = document.getElementById('pagination-users');
        ul.innerHTML = '';

        const createButton = (label, page, disabled = false, active = false) => {
            const li = document.createElement('li');
            li.className = `page-item${disabled ? ' disabled' : ''}${active ? ' active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#">${label}</a>`;
            if (!disabled) {
                li.querySelector('a').addEventListener('click', e => {
                    e.preventDefault();
                    currentPage = page;
                    renderTable();
                });
            }
            return li;
        };

        if (totalPages > 1) {
            ul.appendChild(createButton('«', 1, currentPage === 1));
            ul.appendChild(createButton('‹', currentPage - 1, currentPage === 1));

            let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
            let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

            if (endPage - startPage + 1 < maxPagesToShow && totalPages >= maxPagesToShow) {
                startPage = Math.max(1, endPage - maxPagesToShow + 1);
            }

            for (let i = startPage; i <= endPage; i++) {
                ul.appendChild(createButton(i, i, false, i === currentPage));
            }

            ul.appendChild(createButton('›', currentPage + 1, currentPage === totalPages));
            ul.appendChild(createButton('»', totalPages, currentPage === totalPages));
        }
    }

    document.getElementById('search-input').addEventListener('input', function () {
        searchQuery = this.value;
        currentPage = 1;
        renderTable();
    });

    document.addEventListener('DOMContentLoaded', renderTable);
</script>
@endpush
@endsection