<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO Pages Management</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .table-container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        .data-table th { background: #f9fafb; font-weight: 600; }
        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 500; }
        .status-optimized { background: #d1fae5; color: #065f46; }
        .status-unoptimized { background: #fee2e2; color: #991b1b; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .btn { padding: 8px 16px; border: none; border-radius: 6px; font-weight: 500; text-decoration: none; cursor: pointer; }
        .btn-primary { background: #3b82f6; color: white; }
        .btn-secondary { background: #6b7280; color: white; }
        .btn:hover { opacity: 0.9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SEO Pages Management</h1>
            <a href="/admin/seo/dashboard" class="btn btn-secondary">← Back to Dashboard</a>
        </div>

        <div class="table-container">
            <h3>Pages</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Page Title</th>
                        <th>URL</th>
                        <th>Status</th>
                        <th>Meta Title</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages ?? [] as $page)
                    <tr>
                        <td>{{ $page['title'] ?? 'Untitled Page' }}</td>
                        <td style="font-family: monospace; font-size: 0.9rem; color: #6b7280;">{{ $page['url'] ?? 'No URL' }}</td>
                        <td>
                            <span class="status-badge status-{{ $page['status'] ?? 'pending' }}">
                                {{ ucfirst($page['status'] ?? 'Pending') }}
                            </span>
                        </td>
                        <td>
                            @if($page['meta_title'] ?? false)
                                <span style="color: #10b981;">✓</span> {{ Str::limit($page['meta_title'], 30) }}
                            @else
                                <span style="color: #ef4444;">✗</span> No meta title
                            @endif
                        </td>
                        <td style="color: #6b7280;">{{ $page['updated_at'] ?? 'Never' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #6b7280; font-style: italic;">
                            No pages found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-container">
            <h3>Quick Actions</h3>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="/admin/seo/meta-tags" class="btn btn-primary">Manage Meta Tags</a>
                <a href="/admin/seo/sitemap" class="btn btn-secondary">View Sitemaps</a>
            </div>
        </div>

        <div class="header">
            <h3>Page Statistics</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px;">
                <div style="text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: bold; color: #3b82f6;">{{ count($pages ?? []) }}</div>
                    <div style="color: #6b7280;">Total Pages</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: bold; color: #10b981;">{{ collect($pages ?? [])->where('status', 'optimized')->count() }}</div>
                    <div style="color: #6b7280;">Optimized</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: bold; color: #ef4444;">{{ collect($pages ?? [])->where('status', 'unoptimized')->count() }}</div>
                    <div style="color: #6b7280;">Need Optimization</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: bold; color: #f59e0b;">{{ collect($pages ?? [])->where('status', 'pending')->count() }}</div>
                    <div style="color: #6b7280;">Pending Review</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
