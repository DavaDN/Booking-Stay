@extends('layouts.sidebar')

@section('content')
    <style>
        /* --- CSS Modal Tambah & Edit --- */
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 50px;
        }

        .modal-content {
            background-color: #fff;
            margin: auto;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: relative;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .modal-header span {
            cursor: pointer;
            font-size: 22px;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 15px;
        }

        .btn-secondary {
            background: #bdc3c7;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            cursor: pointer;
            color: #fff;
        }

        .btn-secondary:hover {
            background: #95a5a6;
        }

        .btn-primary {
            background: #3498db;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            cursor: pointer;
            color: #fff;
        }

        .btn-primary:hover {
            background: #2980b9;
        }

        /* --- Style kartu tetap --- */
        .page-header {
            margin-bottom: 10px;
        }

        .page-header h4 {
            font-weight: bold;
            font-size: 18px;
        }

        .page-header p {
            font-size: 13px;
            color: #666;
        }

        .action-bar {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .btn-add {
            background: #3498db;
            color: #fff;
            border-radius: 8px;
            font-size: 13px;
            padding: 6px 14px;
            display: flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border: none;
            cursor: pointer;
        }

        .btn-add:hover {
            background: #2980b9;
        }

        .search-bar {
            display: flex;
            align-items: center;
            flex: 1;
            max-width: 300px;
        }

        .search-bar input {
            border: 1px solid #ccc;
            border-radius: 8px 0 0 8px;
            padding: 6px 10px;
            font-size: 13px;
            width: 100%;
        }

        .search-bar button {
            border: none;
            background: #3498db;
            color: #fff;
            padding: 6px 12px;
            border-radius: 0 8px 8px 0;
            cursor: pointer;
        }

        .room-card {
            border-radius: 10px;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            background: #fff;
            padding: 12px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .room-card .room-header {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
        }

        .badge-status {
            font-size: 11px;
            padding: 3px 6px;
            border-radius: 6px;
            text-transform: capitalize;
        }

        .badge-available {
            background: #eafaf1;
            color: #27ae60;
        }

        .badge-occupied {
            background: #f0f4ff;
            color: #2980b9;
        }

        .badge-maintenance {
            background: #fff4f4;
            color: #e74c3c;
        }

        .room-footer {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 8px;
        }

        .room-footer button {
            border: none;
            background: none;
            cursor: pointer;
        }

        .room-footer .btn-edit {
            color: #2980b9;
        }

        .room-footer .btn-delete {
            color: #e74c3c;
        }
    </style>

    <div class="container-fluid">
        <!-- Header -->
        <div class="page-header">
            <h4>Room Management</h4>
            <p>Kelola status dan informasi kamar individual</p>
        </div>

        <!-- Action Bar -->
        <div class="action-bar">
            <button class="btn-add" id="btnOpenModal">
                <i class="fas fa-plus"></i> Tambah Kamar
            </button>
            <form method="GET" action="{{ route('admin.rooms.index') }}" class="search-bar">
                <input type="text" name="search" placeholder="search room" value="{{ request('search') }}">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <!-- Room Cards -->
        <div class="row" id="roomList">
            @foreach ($rooms as $room)
                <div class="col-md-3">
                    <div class="room-card">
                        <div class="room-header">
                            <span>Room {{ $room->number }}</span>
                        </div>
                        <p class="text-muted mb-1">
                            Lantai {{ $room->floor }} • {{ $room->roomType->name ?? '-' }}
                        </p>
                        <div class="room-footer">
                            <button class="btn-edit" onclick="openEditModal('{{ $room->id }}')">
                                <i class="fas fa-pen"></i>
                            </button>
                            <form action="{{ route('admin.rooms.destroy', $room->id) }}" method="POST"
                                onsubmit="return confirm('Hapus kamar ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="d-flex justify-content-end mt-4">
                {{ $rooms->links('vendor.pagination.custom') }}
            </div>
        </div>
    </div>

    <!-- Modal Tambah Kamar -->
    <div id="modalAddRoom" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span>Tambah Kamar</span>
                <span onclick="closeAddModal()">&times;</span>
            </div>
            <form id="formAddRoom">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nomor Kamar</label>
                    <input type="text" name="number" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Lantai</label>
                    <input type="number" name="floor" class="form-control" required min="1">
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipe Kamar</label>
                    <select name="room_type_id" class="form-control" required>
                        <option value="">-- Pilih Tipe --</option>
                        @foreach (App\Models\RoomType::all() as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeAddModal()">Batal</button>
                    <button type="submit" class="btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Kamar -->
    <div id="modalEditRoom" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span>Edit Kamar</span>
                <span onclick="closeEditModal()">&times;</span>
            </div>
            <form id="formEditRoom">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="editRoomId">

                <div class="mb-3">
                    <label class="form-label">Nomor Kamar</label>
                    <input type="text" name="number" id="editNumber" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Lantai</label>
                    <input type="number" name="floor" id="editFloor" class="form-control" required min="1">
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipe Kamar</label>
                    <select name="room_type_id" id="editRoomType" class="form-control" required>
                        <option value="">-- Pilih Tipe --</option>
                        @foreach (App\Models\RoomType::all() as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeEditModal()">Batal</button>
                    <button type="submit" class="btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // --- Modal Tambah ---
        const modalAdd = document.getElementById("modalAddRoom");

        function closeAddModal() {
            modalAdd.style.display = "none";
        }
        document.getElementById("btnOpenModal").onclick = () => modalAdd.style.display = "block";
        window.onclick = (e) => {
            if (e.target == modalAdd) closeAddModal();
        }

        // Submit form tambah
        document.getElementById("formAddRoom").addEventListener("submit", async function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            let res = await fetch("{{ route('admin.rooms.store') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value
                },
                body: formData
            });

            if (res.ok) {
                location.reload();
            } else {
                alert("Gagal menambahkan kamar");
            }
        });

        // --- Modal Edit ---
        const modalEdit = document.getElementById("modalEditRoom");

        function closeEditModal() {
            modalEdit.style.display = "none";
        }

        async function openEditModal(id) {
            let res = await fetch(`/admin/rooms/${id}`);
            let data = await res.json();

            document.getElementById("editRoomId").value = data.id;
            document.getElementById("editNumber").value = data.number;
            document.getElementById("editFloor").value = data.floor;
            document.getElementById("editRoomType").value = data.room_type_id;
            document.getElementById("editStatus").value = data.status;

            modalEdit.style.display = "block";
        }

        // Submit form edit
        document.getElementById("formEditRoom").addEventListener("submit", async function(e) {
            e.preventDefault();
            let id = document.getElementById("editRoomId").value;
            let formData = new FormData(this);

            let res = await fetch(`/admin/rooms/${id}`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                    "X-HTTP-Method-Override": "PUT"
                },
                body: formData
            });

            if (res.ok) {
                location.reload();
            } else {
                alert("Gagal update kamar");
            }
        });
    </script>
@endsection
