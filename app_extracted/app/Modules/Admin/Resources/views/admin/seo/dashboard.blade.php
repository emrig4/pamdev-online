<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px; }
        .stat-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; }
        .stat-value { font-size: 2rem; font-weight: bold; color: #3b82f6; margin-bottom: 5px; }
        .stat-label { color: #6b7280; font-size: 0.9rem; }
        .info-section { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .action-buttons { display: flex; gap: 10px; flex-wrap: wrap; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; font-weight: 500; text-decoration: none; cursor: pointer; transition: all 0.2s; }
        .btn-primary { background: #3b82f6; color: white; }
        .btn-secondary { background: #6b7280; color: white; }
        .btn:hover { opacity: 0.9; }
        .status-indicator { display: inline-block; width: 12px; height: 12px; border-radius: 50%; margin-right: 8px; }
        .status-good { background: #10b981; }
        .status-warning { background: #f59e0b; }
        .back-link { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SEO Management Dashboard</h1>
            <p>Manage your website's search engine optimization settings</p>
        </div>

        <!-- SEO Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">{{ $stats['total_pages'] ?? 0 }}</div>
                <div class="stat-label">Total Pages</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $stats['total_resources'] ?? 0 }}</div>
                <div class="stat-label">Total Resources</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $stats['total_blogs'] ?? 0 }}</div>
                <div class="stat-label">Total Blogs</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $stats['sitemap_files'] ?? 0 }}</div>
                <div class="stat-label">Sitemap Files</div>
            </div>
        </div>

        <!-- SEO Status -->
        <div class="info-section">
            <h3>System Status</h3>
            <div style="display: flex; align-items: center; margin: 10px 0;">
                <span class="status-indicator status-good"></span>
                <strong>SEO Status:</strong> {{ $seoStatus ?? 'Active' }}
            </div>
            <div style="display: flex; align-items: center; margin: 10px 0;">
                <span class="status-indicator status-good"></span>
                <strong>Last Sitemap Update:</strong> {{ $lastSitemapUpdate ?? 'Unknown' }}
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="info-section">
            <h3>Quick Actions</h3>
            <div class="action-buttons">
                <a href="/admin/seo/pages" class="btn btn-primary">Manage Pages</a>
                <a href="/admin/seo/meta-tags" class="btn btn-primary">Meta Tags</a>
                <a href="/admin/seo/sitemap" class="btn btn-secondary">Sitemap Management</a>
            </div>
        </div>

        <div class="back-link">
            <a href="/admin" class="btn btn-secondary">← Back to Admin Dashboard</a>
        </div>
    </div>
</body>
</html>
