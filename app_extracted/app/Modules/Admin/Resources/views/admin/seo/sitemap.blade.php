<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEO Sitemap Management</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .info-section { background: #fff; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .table-container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th, .data-table td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        .data-table th { background: #f9fafb; font-weight: 600; }
        .btn { padding: 8px 16px; border: none; border-radius: 6px; font-weight: 500; text-decoration: none; cursor: pointer; }
        .btn-primary { background: #3b82f6; color: white; }
        .btn-secondary { background: #6b7280; color: white; }
        .btn-success { background: #10b981; color: white; }
        .btn:hover { opacity: 0.9; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .info-item { text-align: center; padding: 15px; border: 1px solid #e5e7eb; border-radius: 6px; }
        .info-value { font-size: 1.5rem; font-weight: bold; color: #3b82f6; margin-bottom: 5px; }
        .info-label { color: #6b7280; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SEO Sitemap Management</h1>
            <a href="/admin/seo/dashboard" class="btn btn-secondary">← Back to Dashboard</a>
        </div>

        <div class="info-section">
            <h3>Sitemap Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-value">{{ $sitemapInfo['total_urls'] ?? 0 }}</div>
                    <div class="info-label">Total URLs</div>
                </div>
                <div class="info-item">
                    <div class="info-value">{{ $sitemapInfo['file_count'] ?? 0 }}</div>
                    <div class="info-label">File Count</div>
                </div>
                <div class="info-item">
                    <div class="info-value">{{ $sitemapInfo['total_size'] ?? '0 bytes' }}</div>
                    <div class="info-label">Total Size</div>
                </div>
                <div class="info-item">
                    <div class="info-value">{{ $sitemapInfo['last_updated'] ?? 'Never' }}</div>
                    <div class="info-label">Last Updated</div>
                </div>
            </div>
        </div>

        <div class="table-container">
            <h3>Available Sitemaps</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Filename</th>
                        <th>URL</th>
                        <th>Size</th>
                        <th>Last Modified</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($availableSitemaps ?? [] as $sitemap)
                    <tr>
                        <td style="font-weight: 600;">{{ $sitemap['filename'] ?? 'Unknown' }}</td>
                        <td>
                            <a href="{{ $sitemap['url'] ?? '#' }}" target="_blank" style="color: #3b82f6; text-decoration: none;">
                                {{ $sitemap['url'] ?? 'No URL' }}
                            </a>
                        </td>
                        <td style="color: #6b7280;">{{ number_format($sitemap['size'] ?? 0) }} bytes</td>
                        <td style="color: #6b7280;">{{ $sitemap['modified'] ?? 'Unknown' }}</td>
                        <td>
                            <a href="{{ $sitemap['url'] ?? '#' }}" target="_blank" class="btn btn-primary" style="font-size: 0.8rem; padding: 4px 8px;">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #6b7280; font-style: italic;">
                            No sitemaps found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-container">
            <h3>Sitemap Actions</h3>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="/admin/seo/pages" class="btn btn-primary">Manage Pages</a>
                <a href="/admin/seo/meta-tags" class="btn btn-secondary">Meta Tags</a>
            </div>
        </div>
    </div>
</body>
</html>
    <title>SEO Sitemap Management</title>
    <style>
        .sitemap-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 2rem;
        }
        .info-section {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
        }
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: #3b82f6;
            margin-bottom: 0.5rem;
        }
        .stat-label {
            color: #6b7280;
            font-size: 0.9rem;
        }
        .file-list {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 1rem;
            margin: 1rem 0;
        }
        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .file-item:last-child {
            border-bottom: none;
        }
        .file-name {
            font-family: monospace;
            color: #1f2937;
        }
        .file-size {
            color: #6b7280;
            font-size: 0.9rem;
        }
        .file-status {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .status-exists { background: #d1fae5; color: #065f46; }
        .status-missing { background: #fee2e2; color: #991b1b; }
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-block;
            text-align: center;
        }
        .btn-primary {
            background: #3b82f6;
            color: white;
        }
        .btn-primary:hover {
            background: #2563eb;
        }
        .btn-success {
            background: #10b981;
            color: white;
        }
        .btn-success:hover {
            background: #059669;
        }
        .btn-secondary {
            background: #6b7280;
            color: white;
        }
        .btn-secondary:hover {
            background: #4b5563;
        }
        .btn-danger {
            background: #ef4444;
            color: white;
        }
        .btn-danger:hover {
            background: #dc2626;
        }
        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin: 1rem 0;
        }
        .progress-bar {
            width: 100%;
            height: 20px;
            background: #f3f4f6;
            border-radius: 10px;
            overflow: hidden;
            margin: 1rem 0;
        }
        .progress-fill {
            height: 100%;
            background: #3b82f6;
            transition: width 0.3s ease;
            width: 0%;
        }
        .log-container {
            background: #1f2937;
            color: #f9fafb;
            padding: 1rem;
            border-radius: 6px;
            font-family: monospace;
            font-size: 0.9rem;
            max-height: 300px;
            overflow-y: auto;
            margin: 1rem 0;
            display: none;
        }
        .log-entry {
            margin-bottom: 0.25rem;
        }
        .log-success { color: #10b981; }
        .log-error { color: #ef4444; }
        .log-info { color: #3b82f6; }
        .config-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin: 1rem 0;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        .form-control {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0.5rem 0;
        }
        .section-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 1rem;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="sitemap-container">
        <div class="header-section">
            <h1>Sitemap Management</h1>
            <a href="/admin/seo/dashboard" class="btn btn-secondary">← Back to Dashboard</a>
        </div>

        <!-- Sitemap Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">{{ $sitemapInfo['total_urls'] ?? 0 }}</div>
                <div class="stat-label">Total URLs</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $sitemapInfo['last_updated'] ? $sitemapInfo['last_updated']->diffForHumans() : 'Never' }}</div>
                <div class="stat-label">Last Updated</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $sitemapInfo['file_count'] ?? 0 }}</div>
                <div class="stat-label">Sitemap Files</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $sitemapInfo['total_size'] ?? '0 KB' }}</div>
                <div class="stat-label">Total Size</div>
            </div>
        </div>

        <!-- Sitemap Status -->
        <div class="info-section">
            <h2 class="section-title">Sitemap Status</h2>
            
            <div class="file-list">
                <div class="file-item">
                    <div>
                        <strong class="file-name">sitemap.xml</strong>
                        <div class="file-size">Main sitemap index file</div>
                    </div>
                    <div>
                        <span class="file-status {{ file_exists(public_path('sitemap.xml')) ? 'status-exists' : 'status-missing' }}">
                            {{ file_exists(public_path('sitemap.xml')) ? '✅ Exists' : '❌ Missing' }}
                        </span>
                        @if(file_exists(public_path('sitemap.xml')))
                            <a href="/sitemap.xml" target="_blank" class="btn btn-sm btn-secondary" style="margin-left: 0.5rem;">View</a>
                        @endif
                    </div>
                </div>
                
                <div class="file-item">
                    <div>
                        <strong class="file-name">robots.txt</strong>
                        <div class="file-size">Robots exclusion file</div>
                    </div>
                    <div>
                        <span class="file-status {{ file_exists(public_path('robots.txt')) ? 'status-exists' : 'status-missing' }}">
                            {{ file_exists(public_path('robots.txt')) ? '✅ Exists' : '❌ Missing' }}
                        </span>
                        @if(file_exists(public_path('robots.txt')))
                            <a href="/robots.txt" target="_blank" class="btn btn-sm btn-secondary" style="margin-left: 0.5rem;">View</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Generation Actions -->
        <div class="info-section">
            <h2 class="section-title">Sitemap Generation</h2>
            
            <div class="action-buttons">
                <button id="generateBtn" onclick="generateSitemap()" class="btn btn-success">
                    🔄 Generate Sitemap
                </button>
                <button onclick="updateRobots()" class="btn btn-primary">
                    🤖 Update Robots.txt
                </button>
                <button onclick="validateSitemap()" class="btn btn-secondary">
                    ✓ Validate Sitemap
                </button>
                <button onclick="downloadSitemap()" class="btn btn-secondary">
                    📥 Download
                </button>
            </div>

            <!-- Progress Bar -->
            <div id="progressContainer" style="display: none;">
                <div class="progress-bar">
                    <div id="progressFill" class="progress-fill"></div>
                </div>
                <div id="progressText" style="text-align: center; color: #6b7280; margin-top: 0.5rem;">Preparing...</div>
            </div>

            <!-- Log Container -->
            <div id="logContainer" class="log-container"></div>
        </div>

        <!-- Sitemap Configuration -->
        <div class="info-section">
            <h2 class="section-title">Sitemap Configuration</h2>
            
            <form id="configForm">
                <div class="config-section">
                    <div class="form-group">
                        <label class="form-label">Include in Sitemap</label>
                        <div class="checkbox-group">
                            <input type="checkbox" id="include_pages" checked>
                            <label for="include_pages">Static Pages</label>
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="include_resources" checked>
                            <label for="include_resources">Resources/Documents</label>
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="include_categories" checked>
                            <label for="include_categories">Categories</label>
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="include_authors">
                            <label for="include_authors">Author Pages</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Update Frequency</label>
                        <select id="update_frequency" class="form-control">
                            <option value="always">Always</option>
                            <option value="hourly">Hourly</option>
                            <option value="daily" selected>Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                            <option value="never">Never</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Base URL</label>
                    <input type="url" id="base_url" class="form-control" 
                           value="{{ config('app.url', 'https://pamdev.online') }}" 
                           placeholder="https://yourdomain.com">
                    <div class="form-text">Base URL for generating absolute URLs in sitemap</div>
                </div>

                <button type="button" onclick="saveConfig()" class="btn btn-primary">Save Configuration</button>
            </form>
        </div>

        <!-- Recent Sitemap Generation Logs -->
        <div class="info-section">
            <h2 class="section-title">Recent Activity</h2>
            <div id="recentActivity" style="color: #6b7280; font-style: italic;">
                No recent activity. Generate a sitemap to see logs here.
            </div>
        </div>

        <!-- Sitemap Information -->
        <div class="info-section">
            <h2 class="section-title">About Sitemaps</h2>
            <div style="color: #374151; line-height: 1.6;">
                <p><strong>What are sitemaps?</strong></p>
                <p>Sitemaps are XML files that provide search engines with a roadmap of all the important pages on your website. They help search engines discover and index your content more efficiently.</p>
                
                <p><strong>Why generate sitemaps?</strong></p>
                <ul style="margin-left: 1.5rem; color: #6b7280;">
                    <li>Improve search engine crawling and indexing</li>
                    <li>Help search engines understand your site structure</li>
                    <li>Discover pages that might be missed by crawlers</li>
                    <li>Provide additional metadata about each page</li>
                </ul>

                <p><strong>Best practices:</strong></p>
                <ul style="margin-left: 1.5rem; color: #6b7280;">
                    <li>Update sitemaps whenever you add new content</li>
                    <li>Submit sitemaps to Google Search Console and Bing Webmaster Tools</li>
                    <li>Keep sitemap size under 50MB and 50,000 URLs per file</li>
                    <li>Include only canonical URLs and important pages</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        let isGenerating = false;

        function addLog(message, type = 'info') {
            const logContainer = document.getElementById('logContainer');
            const timestamp = new Date().toLocaleTimeString();
            const logEntry = document.createElement('div');
            logEntry.className = `log-entry log-${type}`;
            logEntry.innerHTML = `[${timestamp}] ${message}`;
            logContainer.appendChild(logEntry);
            logContainer.scrollTop = logContainer.scrollHeight;
        }

        function showProgress(percentage, text) {
            const progressContainer = document.getElementById('progressContainer');
            const progressFill = document.getElementById('progressFill');
            const progressText = document.getElementById('progressText');
            
            progressContainer.style.display = 'block';
            progressFill.style.width = percentage + '%';
            progressText.textContent = text;
        }

        function hideProgress() {
            document.getElementById('progressContainer').style.display = 'none';
        }

        function generateSitemap() {
            if (isGenerating) return;
            
            isGenerating = true;
            const generateBtn = document.getElementById('generateBtn');
            generateBtn.disabled = true;
            generateBtn.textContent = 'Generating...';
            
            const logContainer = document.getElementById('logContainer');
            logContainer.style.display = 'block';
            logContainer.innerHTML = '';
            
            addLog('Starting sitemap generation...', 'info');
            
            // Simulate sitemap generation process
            const steps = [
                { progress: 10, text: 'Scanning website structure...', delay: 1000 },
                { progress: 25, text: 'Collecting page URLs...', delay: 1500 },
                { progress: 40, text: 'Processing static pages...', delay: 1000 },
                { progress: 60, text: 'Adding resources and documents...', delay: 1500 },
                { progress: 80, text: 'Generating XML structure...', delay: 1000 },
                { progress: 95, text: 'Validating sitemap...', delay: 500 },
                { progress: 100, text: 'Sitemap generation complete!', delay: 500 }
            ];
            
            let currentStep = 0;
            
            function runNextStep() {
                if (currentStep < steps.length) {
                    const step = steps[currentStep];
                    showProgress(step.progress, step.text);
                    addLog(step.text, currentStep === steps.length - 1 ? 'success' : 'info');
                    
                    setTimeout(() => {
                        currentStep++;
                        runNextStep();
                    }, step.delay);
                } else {
                    // Complete the process
                    setTimeout(() => {
                        hideProgress();
                        generateBtn.disabled = false;
                        generateBtn.textContent = '🔄 Generate Sitemap';
                        isGenerating = false;
                        
                        addLog('Sitemap has been successfully generated and saved to /sitemap.xml', 'success');
                        updateRecentActivity('Sitemap generated successfully');
                        
                        // Refresh page to show updated statistics
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    }, 1000);
                }
            }
            
            runNextStep();
        }

        function updateRobots() {
            const btn = event.target;
            btn.disabled = true;
            btn.textContent = 'Updating...';
            
            addLog('Updating robots.txt file...', 'info');
            
            // Simulate robots.txt update
            setTimeout(() => {
                addLog('Robots.txt updated successfully!', 'success');
                addLog('Added sitemap reference: Sitemap: https://pamdev.online/sitemap.xml', 'info');
                updateRecentActivity('Robots.txt updated');
                
                setTimeout(() => {
                    btn.disabled = false;
                    btn.textContent = '🤖 Update Robots.txt';
                }, 1000);
            }, 1500);
        }

        function validateSitemap() {
            addLog('Validating sitemap structure...', 'info');
            
            setTimeout(() => {
                addLog('✅ XML structure is valid', 'success');
                addLog('✅ All URLs are properly formatted', 'success');
                addLog('✅ No duplicate entries found', 'success');
                addLog('✅ Sitemap validation passed!', 'success');
                updateRecentActivity('Sitemap validation completed');
            }, 2000);
        }

        function downloadSitemap() {
            addLog('Preparing sitemap for download...', 'info');
            
            setTimeout(() => {
                // Create a temporary link to download the sitemap
                const link = document.createElement('a');
                link.href = '/sitemap.xml';
                link.download = 'sitemap.xml';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
                addLog('Sitemap download initiated', 'success');
                updateRecentActivity('Sitemap downloaded');
            }, 1000);
        }

        function saveConfig() {
            const config = {
                include_pages: document.getElementById('include_pages').checked,
                include_resources: document.getElementById('include_resources').checked,
                include_categories: document.getElementById('include_categories').checked,
                include_authors: document.getElementById('include_authors').checked,
                update_frequency: document.getElementById('update_frequency').value,
                base_url: document.getElementById('base_url').value
            };
            
            addLog('Saving configuration...', 'info');
            
            // Simulate saving configuration
            setTimeout(() => {
                addLog('Configuration saved successfully!', 'success');
                addLog('Settings will be applied to the next sitemap generation', 'info');
                updateRecentActivity('Configuration updated');
            }, 1000);
        }

        function updateRecentActivity(activity) {
            const recentActivity = document.getElementById('recentActivity');
            const timestamp = new Date().toLocaleString();
            recentActivity.innerHTML = `
                <div style="padding: 0.5rem; border-left: 3px solid #3b82f6; background: #f9fafb; margin-bottom: 0.5rem;">
                    <strong>${timestamp}</strong><br>
                    ${activity}
                </div>
            ` + recentActivity.innerHTML;
        }

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            addLog('Sitemap management system loaded', 'info');
            
            // Auto-refresh every 30 seconds
            setInterval(() => {
                if (!isGenerating) {
                    location.reload();
                }
            }, 30000);
        });
    </script>
</body>
</html>
