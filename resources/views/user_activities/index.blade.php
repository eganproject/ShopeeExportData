@extends('layouts.main')

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .hero {
        background: linear-gradient(135deg, rgba(13,110,253,.1), rgba(13,202,240,.1));
        border: 1px solid #e5e7eb; border-radius: 16px; padding: 20px 24px;
    }
    .filter-card { border: 1px solid #e5e7eb; border-radius: 14px; }
    .filter-card .card-header { background: #fff; border-bottom: 1px solid #eef2f7; }
    .filter-icon { width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; }
    .filter-icon.primary { background: rgba(13,110,253,.12); color: #0d6efd; }
    .filter-icon.teal { background: rgba(13,202,240,.12); color: #0dcaf0; }
    .filter-icon.orange { background: rgba(253,126,20,.12); color: #fd7e14; }
    .badge-action { background: #eef2ff; color: #4f46e5; border: 1px solid #dfe3ff; }
    .badge-method { background: #e8fff3; color: #16a34a; border: 1px solid #c9f7df; }
    .badge-route { background: #fff7ed; color: #d97706; border: 1px solid #fde7ce; }
    .cell-desc { white-space: normal; line-height: 1.4; }
    .table thead th { position: sticky; top: 0; background: #fff; z-index: 1; }
    .table-hover tbody tr:hover { background-color: #f8fafc; }
    .dt-length, .dt-search { margin: .5rem 0; }
    .dt-info { font-size: .9rem; color: #6b7280; }
    .dt-paging .dt-paging-button { border-radius: 8px; }
    .icon-col { width: 32px; text-align: center; }
    .rounded-soft { border-radius: 12px; }
    .shadow-soft { box-shadow: 0 8px 24px rgba(16,24,40,.06); }
    .truncate-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
    .truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .toolbar { display:flex; gap:.5rem; align-items:center; }
    .toolbar .form-select, .toolbar .form-control { min-width: 220px; }
    .btn-modern { display:inline-flex; align-items:center; gap:.5rem; border-radius: 12px; padding:.55rem .9rem; font-weight:600; transition: all .2s ease; }
    .btn-modern-primary { background: linear-gradient(135deg,#0d6efd,#0dcaf0); border: none; color: #fff; box-shadow: 0 8px 16px rgba(13,110,253,.15); }
    .btn-modern-primary:hover { filter: brightness(1.05); box-shadow: 0 10px 20px rgba(13,110,253,.22); transform: translateY(-1px); }
    .btn-modern-ghost { background:#fff; border:1px solid #e5e7eb; color:#334155; box-shadow: 0 6px 14px rgba(16,24,40,.06); }
    .btn-modern-ghost:hover { background:#f8fafc; border-color:#d1d5db; transform: translateY(-1px); }
    .btn-modern i { font-size: 1rem; }
    @media (max-width: 991.98px) {
      .toolbar { flex-direction: column; align-items: stretch; }
    }
  </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="hero mb-3 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <div class="filter-icon primary"><i class="bi bi-clock-history"></i></div>
            <div>
                <h5 class="mb-0 fw-bold">History User Activity</h5>
                <small class="text-muted">Fokus pada deskripsi aktivitas. Pagination server-side (limit/offset).</small>
            </div>
        </div>
    </div>

    <div class="card filter-card shadow-soft mb-3 rounded-soft">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <div class="filter-icon teal"><i class="bi bi-funnel"></i></div>
                <strong>Filter</strong>
            </div>
            <div class="toolbar">
                <select id="user_id" class="form-select">
                    <option value="">Semua User</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
                <select id="action" class="form-select">
                    <option value="">Semua Action</option>
                    @foreach($actions as $a)
                        <option value="{{ $a }}">{{ $a }}</option>
                    @endforeach
                </select>
                <input type="text" id="date_range" class="form-control" placeholder="Rentang tanggal" />
                <input type="hidden" id="date_from" />
                <input type="hidden" id="date_to" />
                <div class="d-flex gap-2">
                    <button id="btnFilter" class="btn-modern btn-modern-primary" title="Terapkan filter">
                        <i class="bi bi-sliders"></i>
                        <span>Terapkan</span>
                    </button>
                    <button id="btnReset" class="btn-modern btn-modern-ghost" title="Reset filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>Reset</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-soft rounded-soft">
        <div class="card-body">
            <div class="table-responsive">
                <table id="activitiesTable" class="table align-middle">
                    <thead>
                        <tr>
                            <th class="icon-col"></th>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Deskripsi</th>
                            <th>Action</th>
                            <th>Route</th>
                            <th>Method</th>
                            <th>IP</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dayjs@1/plugin/relativeTime.js"></script>
<script src="https://cdn.jsdelivr.net/npm/dayjs@1/locale/id.js"></script>
<script>
    dayjs.extend(dayjs_plugin_relativeTime);
    dayjs.locale('id');

    let table;
    $(function() {
        // Flatpickr range -> sets hidden date_from/date_to
        const fp = flatpickr('#date_range', {
            mode: 'range', dateFormat: 'Y-m-d',
            onChange: function(selectedDates, dateStr){
                if(selectedDates.length === 2){
                    const [from, to] = selectedDates;
                    $('#date_from').val(dayjs(from).format('YYYY-MM-DD'));
                    $('#date_to').val(dayjs(to).format('YYYY-MM-DD'));
                } else {
                    $('#date_from').val('');
                    $('#date_to').val('');
                }
            }
        });

        table = $('#activitiesTable').DataTable({
            processing: true,
            serverSide: true, // uses start/length (offset/limit)
            searching: true,
            order: [[1, 'desc']],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            ajax: {
                url: '{{ route('userActivities.data') }}',
                type: 'GET',
                data: function(d){
                    d.user_id = $('#user_id').val();
                    d.action = $('#action').val();
                    d.date_from = $('#date_from').val();
                    d.date_to = $('#date_to').val();
                }
            },
            columns: [
                { data: null, orderable:false, searchable:false },
                { data: 'created_at', name: 'created_at' },
                { data: 'user', name: 'user' },
                { data: 'description', name: 'description' },
                { data: 'action', name: 'action' },
                { data: 'route', name: 'route' },
                { data: 'method', name: 'method' },
                { data: 'ip', name: 'ip' }
            ],
            columnDefs: [
                {
                    targets: 0,
                    className: 'icon-col',
                    render: function(){
                        return '<i class="bi bi-activity text-primary"></i>';
                    }
                },
                {
                    targets: 1,
                    render: function(data){
                        if(!data) return '-';
                        const rel = dayjs(data).fromNow();
                        return '<div class="d-flex align-items-center gap-2"><i class="bi bi-calendar4-week text-primary"></i><div><div class="fw-semibold">'+data+'</div><small class="text-muted">'+rel+'</small></div></div>';
                    }
                },
                {
                    targets: 2,
                    render: function(data){
                        return '<div class="d-flex align-items-center gap-2"><i class="bi bi-person-circle text-secondary"></i><span class="truncate-1">'+(data||'-')+'</span></div>';
                    }
                },
                {
                    targets: 3,
                    className: 'cell-desc',
                    render: function(data){
                        return '<div class="d-flex gap-2 align-items-start"><i class="bi bi-chat-left-text text-success"></i><div class="truncate-2">'+(data||'-')+'</div></div>';
                    }
                },
                {
                    targets: 4,
                    render: function(data){
                        return '<span class="badge badge-action rounded-pill">'+(data||'-')+'</span>';
                    }
                },
                {
                    targets: 5,
                    render: function(data){
                        return '<span class="badge badge-route rounded-pill">'+(data||'-')+'</span>';
                    }
                },
                {
                    targets: 6,
                    render: function(data){
                        const color = String(data).toUpperCase()==='GET' ? 'secondary' : 'success';
                        return '<span class="badge badge-method rounded-pill">'+(data||'-')+'</span>';
                    }
                },
                {
                    targets: 7,
                    render: function(data){
                        return '<code>'+ (data || '-') +'</code>';
                    }
                }
            ]
        });

        $('#btnFilter').on('click', function(){ table.ajax.reload(); });
        $('#btnReset').on('click', function(){
            $('#user_id').val('');
            $('#action').val('');
            $('#date_range').val('');
            $('#date_from').val('');
            $('#date_to').val('');
            table.ajax.reload();
        });
    });
</script>
@endpush
