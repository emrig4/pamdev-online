<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Resources Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        .stat-card { border-left: 4px solid; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-card.primary { border-left-color: #0d6efd; }
        .stat-card.success { border-left-color: #198754; }
        .stat-card.info { border-left-color: #0dcaf0; }
        .stat-card.warning { border-left-color: #ffc107; }
        .overview-text {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <!-- Header -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">
                                <i class="fas fa-list me-2"></i>
                                Academic Resources Management
                            </h3>
                            <p class="mb-0 mt-2">Manage all academic resources in the system</p>
                        </div>
                        <div>
                            <a href="{{ route('admin.academic.export') }}" class="btn btn-light">
                                <i class="fas fa-download me-2"></i>Export All
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card primary h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-database fa-2x text-primary"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h3 class="h2 mb-0 text-primary">{{ $stats['total_resources'] ?? 0 }}</h3>
                                        <p class="text-muted mb-0">Total Resources</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card success h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-eye fa-2x text-success"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h3 class="h2 mb-0 text-success">{{ $stats['published'] ?? 0 }}</h3>
                                        <p class="text-muted mb-0">Published</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card info h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-dollar-sign fa-2x text-info"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h3 class="h2 mb-0 text-info">₦{{ number_format($stats['total_value'] ?? 0) }}</h3>
                                        <p class="text-muted mb-0">Total Value</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card warning h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-upload fa-2x text-warning"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h3 class="h2 mb-0 text-warning">{{ $stats['recent_uploads'] ?? 0 }}</h3>
                                        <p class="text-muted mb-0">Recent Uploads</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resources Table -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">All Academic Resources</h5>
                            <a href="{{ route('admin.academic.upload') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add New
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(isset($resources) && count($resources) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="resourcesTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Type</th>
                                        <th>Field</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($resources as $resource)
                                    <tr>
                                        <td>{{ $resource->id }}</td>
                                        <td>
                                            <strong>{{ Str::limit($resource->title, 50) }}</strong>
                                            <br><small class="text-muted overview-text">{{ Str::limit($resource->overview, 100) }}</small>
                                        </td>
                                        <td>{{ $resource->author }}</td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $resource->type }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $resource->field }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $resource->currency }} {{ number_format($resource->price) }}</strong>
                                        </td>
                                        <td>
                                            @if($resource->is_published)
                                                <span class="badge bg-success">Published</span>
                                            @else
                                                <span class="badge bg-warning">Draft</span>
                                            @endif
                                        </td>
                                        <td>{{ $resource->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-primary btn-sm" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-success btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $resources->links() }}
                        </div>

                        @else
                        <div class="text-center py-5">
                            <i class="fas fa-database fa-3x text-muted mb-3"></i>
                            <h5>No Academic Resources Found</h5>
                            <p class="text-muted">Start by uploading your first academic resource.</p>
                            <a href="{{ route('admin.academic.upload') }}" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Upload Resources
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-upload fa-2x text-primary mb-2"></i>
                                <h6>Upload New</h6>
                                <a href="{{ route('admin.academic.upload') }}" class="btn btn-outline-primary btn-sm">Add Resources</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-download fa-2x text-success mb-2"></i>
                                <h6>Export Data</h6>
                                <a href="{{ route('admin.academic.export') }}" class="btn btn-outline-success btn-sm">Download Excel</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-chart-bar fa-2x text-info mb-2"></i>
                                <h6>Statistics</h6>
                                <button class="btn btn-outline-info btn-sm" onclick="location.reload()">Refresh Data</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#resourcesTable').DataTable({
                "pageLength": 25,
                "order": [[ 0, "desc" ]],
                "language": {
                    "search": "Search resources:",
                    "lengthMenu": "Show _MENU_ resources per page",
                    "info": "Showing _START_ to _END_ of _TOTAL_ resources"
                }
            });
        });
    </script>
</body>
</html>
