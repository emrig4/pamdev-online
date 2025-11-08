{{-- Enhanced Resource Grid Card with Image Optimization --}}
<div class="ereaders-blog-grid-wrap h-full flex flex-col">
    <div class="ereaders-blog-grid-text flex-1 flex flex-col">
        {{-- Optimized Product Image with Lazy Loading --}}
        <div class="ereaders-blog-grid-image mb-4">
            <img src="{{ $resource->cover_image }}" 
                 alt="{{ $resource->title }}" 
                 class="resource-cover w-full h-48 rounded-lg shadow-sm progressive-image"
                 loading="lazy"
                 data-progressive="true"
                 data-src="{{ $resource->cover_image }}"
                 onerror="this.src='{{ theme_asset('images/default-project-cover.png') }}'; this.onerror=null;">
        </div>

        <div class="flex justify-between mb-4">
            <span class="text-sm font-medium text-gray-600">{{ $resource->field }}</span>
            @if( $resource->isNew() )
                <span class="badge badge-primary my-auto" style="color: white; font-size: 10px">NEW</span>
            @elseif( $resource->isTop() )
                <span class="badge badge-warning my-auto" style="color: white; font-size: 10px">TOP</span>
            @endif
        </div>
        
        <h2 class="text-lg font-bold mb-3">
            <a href="{{ route('resources.show', $resource->slug ) }}" class="text-gray-900 hover:text-blue-600">
                {{ $resource->title }}
            </a>
        </h2>

        <div class="ereaders-blog-heading">
            <ul class="ereaders-thumb-option flex flex-col space-y-1">
                <li class="text-sm text-gray-600">
                    <span class="font-medium">Author:</span> 
                    @if($resource->author() && $resource->author()->is_lead && has_profile($resource->author()->username) )
                        <a class="text-blue-600 hover:underline" href="{{ route('account.profile', $resource->author()->username ) }}">{{  $resource->author()->fullname  }}</a>
                    @else
                        <span>{{  $resource->author() ? $resource->author()->fullname : 'Unknown'  }}</span>
                    @endif
                </li>
                <li class="text-sm text-gray-600">
                    <span class="font-medium">Type:</span> 
                    <span class="text-blue-600">{{ $resource->type ?? 'Project' }}</span>
                </li>
            </ul>
        </div>

        <p class="text-gray-700 text-sm mb-4 line-clamp-3 flex-shrink-0">
            {{ Str::limit($resource->description ?? 'No description available.', 120) }}
        </p>

        <div class="flex flex-col space-y-2 mt-auto">
            <a href="{{ route('resources.show', $resource->slug ) }}" 
               class="btn btn-primary ereaders-readmore-btn flex-1 text-center py-2 px-3 w-full">
                <i class="fas fa-download mr-2"></i>Download
            </a>

            @if(auth()->user() && $resource->user_id == auth()->user()->id)
                <div class="flex space-x-2">
                    <a href="{{ route('resources.edit', $resource->slug ) }}" 
                       class="btn btn-warning ereaders-readmore-btn flex-1 text-center py-2 px-2 text-sm">
                        <i class="fas fa-edit text-white mr-1"></i>Edit
                    </a>

                    <a href="{{ route('resources.delete', $resource->slug ) }}" 
                       class="btn btn-danger ereaders-readmore-btn flex-1 text-center py-2 px-2 text-sm"
                       onclick="return confirm('Are you sure you want to delete this resource?')">
                        <i class="fas fa-trash text-white mr-1"></i>Delete
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
