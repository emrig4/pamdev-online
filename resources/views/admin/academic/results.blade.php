<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Resources Upload Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .stat-card { border-left: 4px solid; transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-card.success { border-left-color: #28a745; }
        .stat-card.error { border-left-color: #dc3545; }
        .stat-card.info { border-left-color: #17a2b8; }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-xl-12">
                <!-- Header -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>
                            Academic Resources Upload Results
                        </h3>
                        <p class="mb-0 mt-2">Upload completed on {{ $results['uploaded_at']->format('M d, Y \a\t h:i A') }}</p>
                    </div>
                </div>

                @if(isset($results) && $results)
                <!-- Upload Summary Statistics -->
                <div class="row mb-4">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card success h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle fa-2x text-success"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h3 class="h2 mb-0 text-success">{{ $results['success_count'] }}</h3>
                                        <p class="text-muted mb-0">Successfully Imported</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card error h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h3 class="h2 mb-0 text-danger">{{ $results['error_count'] }}</h3>
                                        <p class="text-muted mb-0">Failed Imports</p>
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
                                        <i class="fas fa-database fa-2x text-info"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h3 class="h2 mb-0 text-info">{{ $results['total_rows'] }}</h3>
                                        <p class="text-muted mb-0">Total Rows</p>
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
                                        <i class="fas fa-file-excel fa-2x text-warning"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h3 class="h2 mb-0 text-warning">{{ $results['file_name'] }}</h3>
                                        <p class="text-muted mb-0">File Name</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Success Messages -->
                @if($results['success_count'] > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            Successfully Imported Resources ({{ $results['success_count'] }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(count($results['success']) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Row</th>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Field</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($results['success'] as $success)
                                    <tr>
                                        <td>{{ $success['row'] }}</td>
                                        <td>{{ Str::limit($success['title'], 50) }}</td>
                                        <td>{{ $success['message'] ?? 'Imported' }}</td>
                                        <td>
                                            <span class="badge bg-success">Success</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <p class="text-muted">No successful imports to display.</p>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Error Messages -->
                @if($results['error_count'] > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Import Errors ({{ $results['error_count'] }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($results['errors'] as $error)
                        <div class="alert alert-danger">
                            <strong>Row {{ $error['row'] }}:</strong> {{ $error['error'] }}
                            @if(isset($error['data']))
                                <br><small>Data: {{ json_encode(array_slice($error['data'], 0, 3)) }}...</small>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Action Buttons -->
                <div class="row">
                    <div class="col-md-4">
                        <a href="{{ route('admin.academic.upload') }}" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-upload me-2"></i>Upload More
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('admin.academic.history') }}" class="btn btn-success btn-lg w-100">
                            <i class="fas fa-list me-2"></i>View All Resources
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('admin.academic.export') }}" class="btn btn-info btn-lg w-100">
                            <i class="fas fa-download me-2"></i>Export All
                        </a>
                    </div>
                </div>

                @else
                <div class="alert alert-warning">
                    <h4>No upload results found</h4>
                    <p>Please upload a file first.</p>
                    <a href="{{ route('admin.academic.upload') }}" class="btn btn-primary">Go to Upload</a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
