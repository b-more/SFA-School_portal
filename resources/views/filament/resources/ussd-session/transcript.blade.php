<x-filament-panels::page>
    <style>
        .ussd-phone {
            background: #1c1c1e;
            border-radius: 28px;
            padding: 28px 20px 32px;
            max-width: 380px;
            margin: 0 auto;
            box-shadow: 0 12px 40px rgba(0,0,0,0.25);
            border: 3px solid #3a3a3c;
        }
        .ussd-screen {
            background: #d5f7c9;
            color: #1b3a1b;
            font-family: 'Menlo', 'Consolas', monospace;
            font-size: 14px;
            line-height: 1.55;
            padding: 18px 16px;
            border-radius: 8px;
            min-height: 200px;
            white-space: pre-wrap;
            box-shadow: inset 0 0 8px rgba(0,0,0,0.2);
        }
        .ussd-caption {
            color: #ff9f0a;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            text-align: center;
        }
        .ussd-step {
            margin: 20px 0;
        }
        .ussd-step + .ussd-step {
            border-top: 1px dashed #d1d5db;
            padding-top: 20px;
        }
        .dark .ussd-step + .ussd-step {
            border-color: #374151;
        }
        .ussd-step-header {
            text-align: center;
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .dark .ussd-step-header {
            color: #9ca3af;
        }
        .ussd-input {
            background: #007aff;
            color: white;
            padding: 10px 16px;
            border-radius: 20px;
            display: inline-block;
            font-family: 'Menlo', 'Consolas', monospace;
            font-weight: 700;
            font-size: 15px;
            margin: 8px auto;
        }
        .ussd-input-row {
            text-align: center;
        }
        .ussd-input-caption {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
        }
        .summary-row {
            display: flex;
            gap: 14px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .summary-tile {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px 14px;
            flex: 1;
            min-width: 120px;
        }
        .dark .summary-tile {
            background: #1f2937;
            border-color: #374151;
        }
        .summary-tile .label {
            font-size: 11px;
            text-transform: uppercase;
            color: #6b7280;
            letter-spacing: 0.4px;
            margin: 0 0 2px;
        }
        .summary-tile .value {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }
        .dark .summary-tile .value {
            color: #f3f4f6;
        }
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6b7280;
        }
    </style>

    @php
        $ins  = collect($steps)->where('direction', 'in')->count();
        $outs = collect($steps)->where('direction', 'out')->count();
    @endphp

    <div class="summary-row">
        <div class="summary-tile">
            <p class="label">Session</p>
            <p class="value" style="font-family:ui-monospace,monospace;font-size:13px;word-break:break-all;">{{ $record->session_id }}</p>
        </div>
        <div class="summary-tile">
            <p class="label">Shortcode</p>
            <p class="value" style="font-family:ui-monospace,monospace;font-size:14px;">{{ $record->shortcode ?: '—' }}</p>
        </div>
        <div class="summary-tile">
            <p class="label">Keystrokes</p>
            <p class="value">{{ $ins }}</p>
        </div>
        <div class="summary-tile">
            <p class="label">Screens shown</p>
            <p class="value">{{ $outs }}</p>
        </div>
        <div class="summary-tile">
            <p class="label">Status</p>
            <p class="value" style="font-size:14px;">{{ $record->ended ? '🔴 Ended' : '🟢 Active' }}</p>
        </div>
    </div>

    @if(count($steps) === 0)
        <div class="empty-state">No transcript captured for this session.</div>
    @else
        @php
            // Pair up in/out: dial → first out. Then in → out. Etc.
            // The transcript is chronological. Group consecutive out+in pairs as "screens".
            $paired = [];
            $current = ['input' => null, 'screen' => null, 'time' => null, 'when' => null];
            foreach ($steps as $s) {
                if ($s['direction'] === 'out') {
                    if ($current['screen'] !== null) {
                        $paired[] = $current;
                        $current = ['input' => null, 'screen' => null, 'time' => null, 'when' => null];
                    }
                    $current['screen'] = $s['text'];
                    $current['time']   = $s['time'];
                    $current['when']   = $s['when'];
                } else {
                    // an inbound keystroke — save the previous screen with the input that TRIGGERED the next screen
                    if ($current['screen'] !== null) {
                        $paired[] = $current;
                    }
                    $current = ['input' => $s['text'], 'screen' => null, 'time' => $s['time'], 'when' => $s['when']];
                }
            }
            if ($current['screen'] !== null || $current['input'] !== null) {
                $paired[] = $current;
            }
        @endphp

        @foreach($paired as $idx => $pair)
            <div class="ussd-step">
                <div class="ussd-step-header">
                    Screen {{ $idx + 1 }}
                    @if($pair['when']) · {{ $pair['when'] }} @endif
                </div>

                @if($pair['input'] !== null)
                    <div class="ussd-input-row">
                        <div class="ussd-input">Parent typed: {{ $pair['input'] }}</div>
                    </div>
                @endif

                @if($pair['screen'] !== null)
                    <div class="ussd-phone">
                        <div class="ussd-caption">USSD *388*XX#</div>
                        <div class="ussd-screen">{{ $pair['screen'] }}</div>
                    </div>
                @endif
            </div>
        @endforeach
    @endif
</x-filament-panels::page>
