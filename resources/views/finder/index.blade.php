@extends('layouts.app')

@section('content')
<div class="finder-app" data-finder-app>
    <header class="topbar">
        <div class="topbar-left">
            <div class="brand-lockup"><span class="brand-mark">⌁</span><span class="brand-name">Backup Finder</span></div>
            <div class="topbar-divider"></div><span class="workspace-label">Workspace</span>
        </div>
        <div class="topbar-right">
            <span class="status-dot"></span><span class="sync-status">Connected</span>
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="logout-button">Sign out</button></form>
            <div class="avatar">{{ strtoupper(substr(auth()->user()->username, 0, 1)) }}</div>
        </div>
    </header>
    <main class="finder-window">
        <aside class="sidebar">
            <div class="sidebar-section">
                <p class="sidebar-heading">Library</p>
                <a href="{{ route('finder') }}" class="sidebar-link active"><span class="sidebar-icon">▣</span><span>All backups</span><span class="sidebar-count">{{ $listing['items']->count() }}</span></a>
            </div>
            <div class="sidebar-section">
                <p class="sidebar-heading">Quick access</p>
                <a href="{{ route('finder', ['path' => 'daily']) }}" class="sidebar-link"><span class="sidebar-icon">◷</span><span>Daily snapshots</span></a>
                <a href="{{ route('finder', ['path' => 'weekly']) }}" class="sidebar-link"><span class="sidebar-icon">◫</span><span>Weekly archives</span></a>
            </div>
            <div class="sidebar-bottom">
                <div class="storage-card"><div class="storage-icon">◇</div><div><strong>Backup storage</strong><span>Live directory</span></div><span class="storage-live"></span></div>
                <p class="sidebar-footnote">Source<br><strong>{{ basename($listing['source']) }}</strong></p>
            </div>
        </aside>
        <section class="file-area">
            <div class="file-toolbar">
                <div class="toolbar-leading"><button class="tool-button" type="button" data-sidebar-toggle aria-label="Toggle sidebar">☰</button><div class="breadcrumbs"><a href="{{ route('finder') }}">Backups</a>@foreach(array_filter(explode('/', $listing['path'])) as $crumb)<span>/</span><span>{{ $crumb }}</span>@endforeach</div></div>
                <div class="toolbar-actions">
                    <label class="search-box"><span>⌕</span><input type="search" placeholder="Search files" value="{{ $query }}" data-file-search aria-label="Search files"><kbd>⌘ K</kbd></label>
                    <button class="tool-button always-visible" type="button" data-refresh aria-label="Refresh">↻</button>
                    <div class="view-toggle" role="group" aria-label="View mode"><button type="button" class="view-button active" data-view="grid" aria-label="Grid view">▦</button><button type="button" class="view-button" data-view="list" aria-label="List view">☷</button></div>
                </div>
            </div>
            <div class="file-content">
                <div class="content-heading"><div><p class="eyebrow">BACKUP LIBRARY</p><h1>{{ $listing['path'] ? basename($listing['path']) : 'All backups' }}</h1></div><div class="content-meta"><span data-visible-count>{{ $listing['items']->count() }} items</span><span class="meta-divider"></span><span>Updated just now</span></div></div>
                @if ($listing['items']->isEmpty())
                    <div class="empty-state"><div class="empty-icon">⌁</div><h2>No backups here yet</h2><p>Files placed in the configured backup directory will appear here.</p></div>
                @else
                    <div class="file-grid" data-file-grid>
                        @foreach ($listing['items'] as $item)
                            <article class="file-card" data-file-card data-name="{{ strtolower($item['name']) }}" data-directory="{{ $item['is_directory'] ? 'true' : 'false' }}">
                                <div class="card-topline"><span class="file-kind {{ $item['is_directory'] ? 'folder-kind' : 'document-kind' }}">{{ $item['is_directory'] ? 'DIR' : strtoupper($item['extension'] ?: 'FILE') }}</span><button class="more-button" type="button" aria-label="More actions">···</button></div>
                                <a class="file-card-main" href="{{ $item['is_directory'] ? route('finder', ['path' => $item['path']]) : '#' }}" data-file-open>
                                    <div class="file-icon {{ $item['is_directory'] ? 'folder-icon' : 'document-icon' }}"><span>{{ $item['is_directory'] ? '▰' : '▤' }}</span></div>
                                    <h2 title="{{ $item['name'] }}">{{ $item['name'] }}</h2><p>{{ $item['size_label'] }} <span>·</span> {{ $item['modified_at'] }}</p>
                                </a>
                                <div class="card-actions">
                                    @if ($item['is_directory']) <a href="{{ route('finder', ['path' => $item['path']]) }}" class="small-action">Open <span>→</span></a>
                                    @else <button type="button" class="small-action preview-trigger" data-preview-url="{{ route('finder.preview', ['path' => $item['path']]) }}" data-download-url="{{ route('finder.download', ['path' => $item['path']]) }}" data-file-name="{{ $item['name'] }}">Preview <span>↗</span></button><a href="{{ route('finder.download', ['path' => $item['path']]) }}" class="small-action download-action" aria-label="Download {{ $item['name'] }}">↓</a>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <div class="no-results" data-no-results hidden><div class="empty-icon">⌕</div><h2>No matching files</h2><p>Try a different search term.</p></div>
                @endif
            </div>
        </section>
    </main>
</div>
<div class="preview-modal" data-preview-modal aria-hidden="true">
    <div class="modal-backdrop" data-preview-close></div>
    <section class="preview-panel" role="dialog" aria-modal="true" aria-labelledby="preview-title">
        <header class="preview-header"><div><p class="eyebrow">FILE PREVIEW</p><h2 id="preview-title" data-preview-title>Preview</h2></div><button type="button" class="modal-close" data-preview-close aria-label="Close preview">×</button></header>
        <div class="preview-body"><div class="preview-loading" data-preview-loading>Loading preview...</div><pre data-preview-content hidden></pre><div class="preview-message" data-preview-message hidden></div></div>
        <footer class="preview-footer"><span>Read-only preview</span><a href="#" class="primary-button compact" data-preview-download>Download file <span>↓</span></a></footer>
    </section>
</div>
@endsection
