<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO Meta Tags Management</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .table-container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        .data-table th { background: #f9fafb; font-weight: 600; }
        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 500; }
        .status-configured { background: #d1fae5; color: #065f46; }
        .status-needs-review { background: #fee2e2; color: #991b1b; }
        .status-optional { background: #e0e7ff; color: #3730a3; }
        .btn { padding: 8px 16px; border: none; border-radius: 6px; font-weight: 500; text-decoration: none; cursor: pointer; }
        .btn-primary { background: #3b82f6; color: white; }
        .btn-secondary { background: #6b7280; color: white; }
        .btn:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SEO Meta Tags Management</h1>
            <a href="/admin/seo/dashboard" class="btn btn-secondary">← Back to Dashboard</a>
        </div>

        <div class="table-container">
            <h3>Meta Tags</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tag Name</th>
                        <th>Description</th>
                        <th>Example</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($metaTags ?? [] as $tag)
                    <tr>
                        <td style="font-weight: 600;">{{ $tag['name'] ?? 'Unknown' }}</td>
                        <td>{{ $tag['description'] ?? 'No description' }}</td>
                        <td style="font-style: italic; color: #6b7280;">{{ $tag['example'] ?? 'No example' }}</td>
                        <td>
                            <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $tag['status'] ?? 'unknown')) }}">
                                {{ $tag['status'] ?? 'Unknown' }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #6b7280; font-style: italic;">
                            No meta tags found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-container">
            <h3>Common Meta Tags</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">
                @forelse($commonTags ?? [] as $tag => $description)
                <div style="padding: 15px; border: 1px solid #e5e7eb; border-radius: 6px;">
                    <div style="font-weight: 600; margin-bottom: 5px;">{{ $tag }}</div>
                    <div style="color: #6b7280; font-size: 0.9rem;">{{ $description }}</div>
                </div>
                @empty
                <div style="text-align: center; color: #6b7280; font-style: italic;">
                    No common tags available.
                </div>
                @endforelse
            </div>
        </div>

        <div class="table-container">
            <h3>Quick Actions</h3>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="/admin/seo/pages" class="btn btn-primary">Manage Pages</a>
                <a href="/admin/seo/sitemap" class="btn btn-secondary">View Sitemaps</a>
            </div>
        </div>
    </div>
</body>
</html>
