<x-filament-panels::page>
    <style>
        .wa-chat {
            background: #efeae2;
            padding: 20px;
            border-radius: 8px;
            max-height: 70vh;
            overflow-y: auto;
            position: relative;
        }
        .dark .wa-chat {
            background: #0b141a;
        }
        .wa-date-divider {
            text-align: center;
            margin: 18px 0 10px;
        }
        .wa-date-divider span {
            background: rgba(11, 20, 26, 0.06);
            color: #54656f;
            padding: 4px 12px;
            font-size: 12px;
            border-radius: 8px;
            font-weight: 500;
        }
        .dark .wa-date-divider span {
            background: rgba(255,255,255,0.06);
            color: #a3b8bf;
        }
        .wa-bubble {
            max-width: 78%;
            padding: 8px 12px 6px;
            border-radius: 8px;
            margin-bottom: 6px;
            position: relative;
            font-size: 14px;
            line-height: 1.4;
            box-shadow: 0 1px 0.5px rgba(11,20,26,0.13);
            word-wrap: break-word;
        }
        .wa-bubble-in {
            background: white;
            color: #111b21;
            margin-right: auto;
            border-top-left-radius: 0;
        }
        .dark .wa-bubble-in {
            background: #202c33;
            color: #e9edef;
        }
        .wa-bubble-out {
            background: #d9fdd3;
            color: #111b21;
            margin-left: auto;
            border-top-right-radius: 0;
        }
        .dark .wa-bubble-out {
            background: #005c4b;
            color: #e9edef;
        }
        .wa-kind {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            color: #667781;
            margin-bottom: 3px;
            font-weight: 600;
        }
        .dark .wa-kind {
            color: #8696a0;
        }
        .wa-content {
            white-space: pre-wrap;
        }
        .wa-content-preview {
            font-style: italic;
            color: #667781;
        }
        .wa-time {
            font-size: 11px;
            color: #667781;
            text-align: right;
            margin-top: 2px;
        }
        .dark .wa-time {
            color: #8696a0;
        }
        .wa-attachment {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px;
            background: rgba(0,0,0,0.03);
            border-radius: 6px;
            margin-bottom: 4px;
            font-size: 13px;
        }
        .dark .wa-attachment {
            background: rgba(255,255,255,0.05);
        }
        .wa-meta-choices {
            font-size: 12px;
            color: #54656f;
            padding-left: 6px;
            border-left: 3px solid #25d366;
            margin-top: 4px;
        }
        .dark .wa-meta-choices {
            color: #a3b8bf;
        }
        .wa-meta-choices .row {
            padding: 3px 0;
        }
        .wa-meta-choices .row strong {
            color: #111b21;
        }
        .dark .wa-meta-choices .row strong {
            color: #e9edef;
        }
        .wa-state-badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 600;
            background: #f0f2f5;
            color: #54656f;
            margin-left: 6px;
            font-family: ui-monospace, "SF Mono", Menlo, monospace;
        }
        .dark .wa-state-badge {
            background: rgba(255,255,255,0.08);
            color: #a3b8bf;
        }
        .wa-summary {
            display: flex;
            gap: 14px;
            margin-bottom: 10px;
        }
        .wa-summary-tile {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px 14px;
            flex: 1;
        }
        .dark .wa-summary-tile {
            background: #1f2937;
            border-color: #374151;
        }
        .wa-summary-tile .label {
            font-size: 11px;
            text-transform: uppercase;
            color: #6b7280;
            letter-spacing: 0.4px;
            margin: 0 0 2px;
        }
        .wa-summary-tile .value {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }
        .dark .wa-summary-tile .value {
            color: #f3f4f6;
        }
    </style>

    <div class="wa-summary">
        <div class="wa-summary-tile">
            <p class="label">Total messages</p>
            <p class="value">{{ $inboundCount + $outboundCount }}</p>
        </div>
        <div class="wa-summary-tile">
            <p class="label">From parent</p>
            <p class="value">{{ $inboundCount }}</p>
        </div>
        <div class="wa-summary-tile">
            <p class="label">From bot</p>
            <p class="value">{{ $outboundCount }}</p>
        </div>
        <div class="wa-summary-tile">
            <p class="label">Days active</p>
            <p class="value">{{ count($groupedByDay) }}</p>
        </div>
    </div>

    <div class="wa-chat" id="wa-thread">
        @forelse($groupedByDay as $day)
            <div class="wa-date-divider">
                <span>{{ $day['label'] }}</span>
            </div>
            @foreach($day['messages'] as $m)
                @php
                    $isOut = $m['direction'] === 'out';
                    $isReply = str_ends_with($m['kind'], '_reply');
                    $isInteractive = str_starts_with($m['kind'], 'interactive_');
                    $isDoc = $m['kind'] === 'document';
                    $isImage = $m['kind'] === 'image';
                @endphp
                <div class="wa-bubble {{ $isOut ? 'wa-bubble-out' : 'wa-bubble-in' }}">
                    <div class="wa-kind">
                        @if($m['kind'] === 'text')
                            Text
                        @elseif($m['kind'] === 'list_reply')
                            👆 List choice
                        @elseif($m['kind'] === 'button_reply')
                            👆 Button choice
                        @elseif($isInteractive)
                            {{ $m['kind'] === 'interactive_list' ? '📋 Interactive list' : '🔘 Interactive buttons' }}
                        @elseif($isDoc)
                            📎 Document
                        @elseif($isImage)
                            🖼️ Image
                        @elseif($m['kind'] === 'template')
                            📨 Template
                        @else
                            {{ $m['kind'] }}
                        @endif
                        @if($m['state'] && ! $isOut)
                            <span class="wa-state-badge">{{ $m['state'] }}</span>
                        @endif
                    </div>

                    {{-- Attachment header for docs/images --}}
                    @if($isDoc && $m['meta']['filename'] ?? null)
                        <div class="wa-attachment">
                            <span>📄</span>
                            <span><strong>{{ $m['meta']['filename'] }}</strong></span>
                        </div>
                    @elseif($isImage)
                        <div class="wa-attachment">
                            <span>🖼️</span>
                            <span>Image sent</span>
                        </div>
                    @endif

                    {{-- Text content --}}
                    @if($m['content'])
                        <div class="wa-content">{!! nl2br(e($m['content'])) !!}</div>
                    @elseif(!$isDoc && !$isImage)
                        <div class="wa-content-preview">(no text)</div>
                    @endif

                    {{-- Interactive options / reply meta --}}
                    @if($isInteractive && ($m['meta']['rows'] ?? false))
                        <div class="wa-meta-choices">
                            @foreach($m['meta']['rows'] as $row)
                                <div class="row">
                                    <strong>{{ $row['title'] ?? $row['id'] }}</strong>
                                    @if($row['description'] ?? null)
                                        — {{ $row['description'] }}
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @if($isInteractive && ($m['meta']['buttons'] ?? false))
                        <div class="wa-meta-choices">
                            @foreach($m['meta']['buttons'] as $btn)
                                <div class="row">
                                    <strong>[{{ $btn['title'] ?? $btn['id'] }}]</strong>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @if($isReply && ($m['meta']['id'] ?? null))
                        <div class="wa-meta-choices">
                            <div class="row">id: <code>{{ $m['meta']['id'] }}</code></div>
                        </div>
                    @endif

                    <div class="wa-time" title="{{ $m['when'] }}">
                        {{ $m['time'] }}
                        @if($isOut) ✓✓ @endif
                    </div>
                </div>
            @endforeach
        @empty
            <div style="text-align: center; padding: 40px 20px; color: #667781;">
                No messages yet from this number.
            </div>
        @endforelse
    </div>

    <script>
        // Auto-scroll to bottom on load, like a real chat app.
        document.addEventListener('DOMContentLoaded', () => {
            const chat = document.getElementById('wa-thread');
            if (chat) chat.scrollTop = chat.scrollHeight;
        });
    </script>
</x-filament-panels::page>
