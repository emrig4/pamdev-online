<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Resources Upload</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .upload-zone {
            border: 3px dashed #dee2e6;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .upload-zone:hover, .upload-zone.dragover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }
        .upload-zone.active {
            border-color: #28a745;
            background-color: #d4edda;
        }
        .upload-zone.error {
            border-color: #dc3545;
            background-color: #f8d7da;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">
                            <i class="fas fa-upload me-2"></i>
                            Academic Resources Upload
                        </h3>
                        <p class="mb-0 mt-2">Upload comprehensive academic resources (10 columns)</p>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-8">
                                <h5><i class="fas fa-info-circle me-2"></i>Academic Resources Format</h5>
                                <p>Upload Excel files with 10 columns: filename, title, overview, author, type, field, sub_fields, currency, price, preview_limit</p>
                                
                                <!-- Template Download -->
                                <div class="mb-3">
                                    <a href="{{ route('admin.academic.template') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-download me-2"></i>Download Template
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h5><i class="fas fa-list me-2"></i>Column Guide</h5>
                                <ul class="list-unstyled small">
                                    <li><strong>filename:</strong> S3 file path</li>
                                    <li><strong>title:</strong> Resource title</li>
                                    <li><strong>overview:</strong> Description</li>
                                    <li><strong>author:</strong> Author name</li>
                                    <li><strong>type:</strong> project/book/article</li>
                                    <li><strong>field:</strong> Academic field</li>
                                    <li><strong>sub_fields:</strong> Specialization</li>
                                    <li><strong>currency:</strong> NGN/USD/EUR</li>
                                    <li><strong>price:</strong> Numeric price</li>
                                    <li><strong>preview_limit:</strong> Preview pages</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Upload Form -->
                        <form action="{{ route('admin.academic.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                            @csrf
                            <div class="upload-zone" id="uploadZone">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <h5>Drop Excel file here or click to select</h5>
                                <p class="text-muted">Supports .xlsx, .xls, .csv (Max 10MB)</p>
                                <input type="file" name="excel_file" id="excelFile" accept=".xlsx,.xls,.csv" style="display: none;" required>
                            </div>

                            <div id="filePreview" class="mt-3" style="display: none;">
                                <div class="alert alert-info">
                                    <strong>Selected File:</strong> <span id="fileName"></span><br>
                                    <strong>Size:</strong> <span id="fileSize"></span>
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-success btn-lg" id="uploadBtn" disabled>
                                    <i class="fas fa-upload me-2"></i>Upload Academic Resources
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-history fa-2x text-primary mb-2"></i>
                                <h6>View History</h6>
                                <a href="{{ route('admin.academic.history') }}" class="btn btn-outline-primary btn-sm">Manage Resources</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-download fa-2x text-success mb-2"></i>
                                <h6>Export Data</h6>
                                <a href="{{ route('admin.academic.export') }}" class="btn btn-outline-success btn-sm">Download All</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <i class="fas fa-chart-bar fa-2x text-info mb-2"></i>
                                <h6>Statistics</h6>
                                <a href="{{ route('admin.academic.history') }}" class="btn btn-outline-info btn-sm">View Stats</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const uploadZone = document.getElementById('uploadZone');
        const excelFile = document.getElementById('excelFile');
        const filePreview = document.getElementById('filePreview');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const uploadBtn = document.getElementById('uploadBtn');

        // Drag and drop functionality
        uploadZone.addEventListener('click', () => excelFile.click());
        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadZone.classList.add('dragover');
        });
        uploadZone.addEventListener('dragleave', () => {
            uploadZone.classList.remove('dragover');
        });
        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                excelFile.files = files;
                handleFileSelection();
            }
        });

        excelFile.addEventListener('change', handleFileSelection);

        function handleFileSelection() {
            const file = excelFile.files[0];
            if (file) {
                // Validate file type
                const validTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
                                  'application/vnd.ms-excel', 'text/csv'];
                if (!validTypes.includes(file.type) && !file.name.match(/\.(xlsx|xls|csv)$/i)) {
                    uploadZone.classList.add('error');
                    alert('Please select a valid Excel or CSV file.');
                    return;
                }

                // Validate file size (10MB)
                if (file.size > 10 * 1024 * 1024) {
                    uploadZone.classList.add('error');
                    alert('File size must be less than 10MB.');
                    return;
                }

                uploadZone.classList.add('active');
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                filePreview.style.display = 'block';
                uploadBtn.disabled = false;
            }
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Form submission
        document.getElementById('uploadForm').addEventListener('submit', function() {
            uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';
            uploadBtn.disabled = true;
        });
    </script>
</body>
</html>
