            <div class="students-card system-logs-card is-hidden" id="systemLogsSection">
                <div class="system-logs-head">
                    <div>
                        <h2 class="system-logs-title">System Logs</h2>
                        <p class="system-logs-subtitle">Recent application events from the Laravel log file</p>
                    </div>
                    <button type="button" class="section-close-btn section-close-trigger" data-close-section="system-logs" aria-label="Close system logs section">&times;</button>
                </div>

                <div class="system-logs-summary">
                    <div class="system-log-stat">
                        <span class="system-log-stat-value">{{ $systemLogs->count() }}</span>
                        <span class="system-log-stat-label">Recent Entries</span>
                    </div>
                    <div class="system-log-stat">
                        <span class="system-log-stat-value">{{ $systemLogs->where('level', 'error')->count() + $systemLogs->where('level', 'critical')->count() }}</span>
                        <span class="system-log-stat-label">Errors</span>
                    </div>
                    <div class="system-log-stat">
                        <span class="system-log-stat-value">{{ $systemLogs->where('level', 'warning')->count() }}</span>
                        <span class="system-log-stat-label">Warnings</span>
                    </div>
                </div>

                <div class="system-logs-list">
                    @forelse ($systemLogs as $log)
                        @php
                            $level = strtolower((string) ($log['level'] ?? 'info'));
                            $message = trim((string) ($log['message'] ?? ''));
                            $context = trim((string) ($log['context'] ?? ''));
                        @endphp
                        <article class="system-log-item">
                            <div class="system-log-top">
                                <span class="system-log-level system-log-level-{{ $level }}">{{ strtoupper($level) }}</span>
                                <span class="system-log-time">{{ $log['timestamp'] ?? '--' }}</span>
                            </div>
                            <div class="system-log-message">{{ $message !== '' ? $message : 'No message provided.' }}</div>
                            @if ($context !== '')
                                <details class="system-log-context">
                                    <summary>View context</summary>
                                    <pre>{{ \Illuminate\Support\Str::limit($context, 1200) }}</pre>
                                </details>
                            @endif
                        </article>
                    @empty
                        <div class="overview-empty">No system logs found.</div>
                    @endforelse
                </div>
            </div>
