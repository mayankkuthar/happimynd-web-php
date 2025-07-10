@props(['paginator', 'perPageOptions' => [10, 25, 50, 100, 500, 1000]])

<div class="pagination-controls-wrapper" style="display: flex; justify-content: space-between; align-items: center; margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 8px; border: 1px solid #dee2e6;">
    <div class="pagination-info">
        <span class="text-muted">
            Showing 
            <strong>{{ $paginator->firstItem() ?? 0 }}</strong> 
            to 
            <strong>{{ $paginator->lastItem() ?? 0 }}</strong> 
            of 
            <strong>{{ number_format($paginator->total()) }}</strong> 
            results
        </span>
    </div>
    
    <div class="pagination-controls" style="display: flex; align-items: center; gap: 15px;">
        <div class="per-page-selector">
            <label for="per_page" style="margin-right: 8px; font-weight: 500; color: #495057;">Rows per page:</label>
            <select id="per_page" name="per_page" onchange="changePerPage(this.value)" style="padding: 6px 12px; border: 1px solid #ced4da; border-radius: 4px; background: white; font-size: 14px;">
                @php
                    $currentPerPage = request('per_page', $paginator->perPage());
                @endphp
                @foreach($perPageOptions as $option)
                    <option value="{{ $option }}" {{ $currentPerPage == $option ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="pagination-links">
            {{ $paginator->appends(request()->except('page'))->links() }}
        </div>
    </div>
</div>

<script>
function changePerPage(value) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', value);
    url.searchParams.delete('page'); // Reset to first page when changing per page
    window.location.href = url.toString();
}
</script>

<style>
.pagination-controls-wrapper {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}

.pagination-controls-wrapper .pagination {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 5px;
}

.pagination-controls-wrapper .pagination li {
    list-style: none;
}

.pagination-controls-wrapper .pagination a,
.pagination-controls-wrapper .pagination span {
    display: inline-block;
    padding: 8px 12px;
    text-decoration: none;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    color: #495057;
    font-size: 14px;
    font-weight: 500;
    min-width: 35px;
    text-align: center;
    transition: all 0.2s ease;
}

.pagination-controls-wrapper .pagination a:hover {
    background-color: #e9ecef;
    border-color: #adb5bd;
    color: #495057;
}

.pagination-controls-wrapper .pagination .active span {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.pagination-controls-wrapper .pagination .disabled span {
    color: #6c757d;
    background-color: #f8f9fa;
    border-color: #dee2e6;
    cursor: not-allowed;
}

.per-page-selector select:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

@media (max-width: 768px) {
    .pagination-controls-wrapper {
        flex-direction: column;
        gap: 15px;
    }
    
    .pagination-controls {
        flex-direction: column;
        gap: 10px;
    }
}
</style> 