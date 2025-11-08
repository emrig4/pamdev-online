<div class="ereaders-blog ereaders-blog-grid">
    <ul class="row equal-height-row list-unstyled">
        @foreach($resources as $resource)
            <li class="col-md-4">
                <div class="ereaders-blog-grid-wrap">
                    @include('resource.partials.inc.grid_card', $resource)
                </div>
            </li>
        @endforeach
    </ul>
</div>

<style>
/* === Make related section identical to grid === */

.equal-height-row {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: stretch !important;
    margin-right: -15px;
    margin-left: -15px;
}

.equal-height-row > [class*="col-"],
.equal-height-row > li[class*="col-"] {
    display: flex;
    padding-right: 15px;
    padding-left: 15px;
    margin-bottom: 30px;
}

.ereaders-blog-grid-wrap {
    display: flex;
    flex-direction: column;
    height: 100% !important;
    width: 100%;
}

.ereaders-blog-grid-text {
    display: flex;
    flex-direction: column;
    flex: 1;
    height: 100%;
}

/* ✅ Center the download button at bottom */
.ereaders-blog-grid .download-btn-wrapper {
    margin-top: auto;
    display: flex;
    justify-content: center;
    align-items: center;
    padding-top: 10px;
}

/* Prevent clipping of buttons or overlays */
.ereaders-blog-grid .card,
.ereaders-blog-grid-wrap {
    overflow: visible !important;
}

@media (max-width: 768px) {
    .equal-height-row {
        flex-direction: column;
    }

    .equal-height-row > [class*="col-"],
    .equal-height-row > li[class*="col-"] {
        flex: 0 0 100% !important;
        max-width: 100% !important;
    }
}
</style>
