<div class="ereaders-blog ereaders-blog-grid">
    <div class="row equal-height-row">
        @foreach($resources as $resource)
            <div class="col-md-4">
                @include('resource.partials.inc.grid_card', $resource)
            </div>
        @endforeach
    </div>
</div>

<style>
.equal-height-row {
    display: flex !important;
    flex-wrap: wrap !important;
    align-items: stretch !important;
    margin-right: -15px;
    margin-left: -15px;
}

.equal-height-row > [class*="col-"] {
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

@media (max-width: 768px) {
    .equal-height-row {
        flex-direction: column;
    }
    
    .equal-height-row > [class*="col-"] {
        flex: 0 0 100% !important;
        max-width: 100% !important;
    }
}
</style>