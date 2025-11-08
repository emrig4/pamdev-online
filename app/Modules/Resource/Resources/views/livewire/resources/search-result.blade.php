<div class="">
    <style>
        .table p {
            margin: 0;
        }

        .filter-container {
            margin-bottom: 15px;
            padding: 5px;
            background: white;
            color: black;
            font-size: 16px;
        }

        .filter-container > .row {
            margin: 0;
        }

        .filter-container > .row > div {
            padding: 0 5px;
        }

        .filter-container input[type="text"] {
            min-height: 28px !important;
            font-size: 14px;
            background: white;
        }

        .filter-container .form-control {
            padding: 6px 12px;
            color: #9ca3af;
            border: 1px solid #cccccc69;
        }

        /* === Reuse grid structure from your working layout === */

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

        /* ✅ Center the download button at the bottom of each card */
        .ereaders-blog-grid .download-btn-wrapper {
            margin-top: auto;
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 10px;
        }

        .ereaders-blog-grid .card,
        .ereaders-blog-grid-wrap {
            overflow: visible !important;
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

    <div class="col-md-12">
        <div class="ereaders-fancy-title">
            <h2>SEARCH RESULTS</h2>
            <div class="clearfix"></div>
            <p>We found '{{ $count }}' resource(s) for your search</p>
            <div>
                <a class="ereaders-color" href="" data-toggle="modal" data-target="#modalPoll-1">
                    Search again
                </a>
            </div>
        </div>
    </div>

    <div class="clearfix mb-10"></div>
    <div wire:loading>
        {{ $loading_message }}
    </div>

    <!-- FILTERS -->
    <div class="filter-container">
        <div class="flex justify-around flex-wrap gap-2">
            <div class="md:w-1/2">
                <input wire:change="$emit('filtered')" placeholder="Filter by title"
                    type="text" class="form-control" wire:model="filter.title">
            </div>

            <div>
                <select wire:model="filter.field" wire:change="$emit('filtered')" class="form-control">
                    <option value="">Field</option>
                    <option value="ANTHROPOLOGY">Anthropology</option>
                    <option value="history">History</option>
                </select>
            </div>

            <div>
                <select wire:model="filter.subfield" wire:change="$emit('filtered')" class="form-control">
                    <option value="">Subfield</option>
                    <option value="mythology">Mythology</option>
                    <option value="jkk">Kjj</option>
                    <option value="human-behavioral-ecology">Human Behavioral Ecology</option>
                </select>
            </div>

            <div>
                <select wire:model="items_per_page" wire:change="$emit('filtered')" class="form-control">
                    <option value="">Per Page</option>
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="20">20</option>
                </select>
            </div>

            <div style="display: flex; align-items: flex-end;">
                <button type="button" wire:click="filterList" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </div>

    <!-- SEARCH RESULTS GRID -->
    @if(!empty($objects))
        <div class="ereaders-blog ereaders-blog-grid">
            <div class="row equal-height-row">
                @foreach($resources as $resource)
                    <div class="col-md-4">
                        <div class="ereaders-blog-grid-wrap">
                            @include('resource.partials.inc.grid_card', $resource)
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- PAGINATION -->
    <div class="ereaders-pagination">
        {!! $resources->links('partials.pagination') !!}
    </div>
</div>
