<h2 class="ereaders-section-heading">Related Resources</h2>

<div class="ereaders-blog ereaders-blog-grid">
    <div class="row equal-height-row">
        @foreach($related as $resource)
            <div class="col-md-4">
                <div class="ereaders-blog-grid-wrap">
                    <div class="ereaders-blog-grid-text">

                        {{-- Resource Image --}}
                        <div class="ereaders-blog-grid-image mb-4">
                            <a href="{{ route('resources.show', $resource->slug) }}">
                                <img src="{{ $resource->cover_image }}" 
                                     alt="{{ $resource->title }}" 
                                     class="w-full h-48 object-cover rounded-lg shadow-sm">
                            </a>
                        </div>

                        {{-- Resource Meta --}}
                        <div class="flex justify-between mb-3">
                            <span class="text-sm font-medium text-gray-600">{{ $resource->field }}</span>
                            @if($resource->isNew())
                                <span class="badge badge-primary my-auto" style="color: white; font-size: 10px;">NEW</span>
                            @elseif($resource->isTop())
                                <span class="badge badge-warning my-auto" style="color: white; font-size: 10px;">TOP</span>
                            @endif
                        </div>

                        {{-- Resource Title --}}
                        <h2 class="text-lg font-bold mb-2">
                            <a href="{{ route('resources.show', $resource->slug) }}" 
                               class="text-gray-900 hover:text-blue-600">
                               {{ $resource->title }}
                            </a>
                        </h2>

                        {{-- Author & Type --}}
                        <div class="ereaders-blog-heading mb-3">
                            <ul class="ereaders-thumb-option flex flex-col space-y-1">
                                <li class="text-sm text-gray-600">
                                    <span class="font-medium">Author:</span>
                                    @if($resource->author() && $resource->author()->is_lead && has_profile($resource->author()->username))
                                        <a class="text-blue-600 hover:underline" 
                                           href="{{ route('account.profile', $resource->author()->username) }}">
                                            {{ $resource->author()->fullname }}
                                        </a>
                                    @else
                                        <span>{{ $resource->author() ? $resource->author()->fullname : 'Unknown' }}</span>
                                    @endif
                                </li>

                                <li class="text-sm text-gray-600">
                                    <span class="font-medium">Type:</span>
                                    <span class="text-blue-600">{{ $resource->type ?? 'Project' }}</span>
                                </li>
                            </ul>
                        </div>

                        {{-- Description --}}
                        <p class="text-gray-700 text-sm mb-4 line-clamp-3">
                            {{ Str::limit($resource->description ?? '.', 120) }}
                        </p>

                        {{-- Buttons --}}
                        <div class="mt-auto flex flex-col space-y-2">
                            <a href="{{ route('resources.show', $resource->slug) }}" 
                               class="btn btn-primary ereaders-readmore-btn text-center">
                                Download
                            </a>

                            @if(auth()->user() && $resource->user_id == auth()->user()->id)
                                <div class="flex space-x-2">
                                    <a href="{{ route('resources.edit', $resource->slug) }}" 
                                       class="btn btn-warning ereaders-readmore-btn flex-1 text-center">
                                        <i class="fa fa-edit text-white"></i> Edit
                                    </a>
                                    <a href="{{ route('resources.delete', $resource->slug) }}" 
                                       class="btn btn-danger ereaders-readmore-btn flex-1 text-center"
                                       onclick="return confirm('Are you sure you want to delete this resource?')">
                                        <i class="fa fa-trash text-white"></i> Delete
                                    </a>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
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
.ereaders-blog-grid-image img {
    transition: transform 0.3s ease;
}
.ereaders-blog-grid-image:hover img {
    transform: scale(1.05);
}
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
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
