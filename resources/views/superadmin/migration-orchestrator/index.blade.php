<x-layouts.superadmin :pageTitle="$pageTitle">
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-base-content">
            🔄 Migration Orchestrator
        </h1>
        <p class="mt-2 text-lg text-base-content/70">
            Gestione centralizzata delle migration per database condiviso EGI + NATAN_LOC
        </p>
    </div>

    @if(!$orchestrator_exists)
        <div class="alert alert-error mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Orchestrator non trovato. Verifica che il file esista in: <code>/home/fabio/migration_orchestrator/migrate_shared.php</code></span>
        </div>
    @endif

    {{-- Projects Status --}}
    <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2">
        @foreach($projects as $key => $project)
            <div class="card bg-base-200 shadow-xl">
                <div class="card-body">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="card-title text-2xl">
                            <span class="badge badge-{{ $project['color'] }} badge-lg mr-2">{{ $key }}</span>
                            {{ $project['name'] }}
                        </h2>
                        <button class="btn btn-sm btn-outline" onclick="refreshStatus('{{ $key }}')">
                            🔄 Aggiorna
                        </button>
                    </div>

                    <div class="stats stats-vertical lg:stats-horizontal shadow w-full">
                        <div class="stat">
                            <div class="stat-title">Migration Totali</div>
                            <div class="stat-value text-primary">{{ $project['migration_count'] }}</div>
                        </div>

                        <div class="stat">
                            <div class="stat-title">Eseguite</div>
                            <div class="stat-value text-success">
                                {{ isset($project['status']['ran']) ? $project['status']['ran'] : 'N/A' }}
                            </div>
                        </div>

                        <div class="stat">
                            <div class="stat-title">In Attesa</div>
                            <div class="stat-value text-warning">
                                {{ isset($project['status']['pending']) ? $project['status']['pending'] : 'N/A' }}
                            </div>
                        </div>
                    </div>

                    @if(isset($project['status']['error']))
                        <div class="alert alert-error mt-4">
                            <span>{{ $project['status']['error'] }}</span>
                        </div>
                    @endif

                    {{-- Quick Actions --}}
                    <div class="mt-4 flex gap-2 flex-wrap">
                        <button class="btn btn-sm btn-primary" onclick="executeCommand('{{ $key }}', 'migrate:status')">
                            📊 Status
                        </button>
                        <button class="btn btn-sm btn-success" onclick="executeCommand('{{ $key }}', 'migrate')">
                            ▶️ Migrate
                        </button>
                        <button class="btn btn-sm btn-warning" onclick="executeCommand('{{ $key }}', 'migrate:rollback', true)">
                            ⏪ Rollback
                        </button>
                        <button class="btn btn-sm btn-error" onclick="executeCommand('{{ $key }}', 'migrate:refresh', true, true)">
                            ⚠️ Refresh
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Command Execution Panel --}}
    <div class="card bg-base-200 shadow-xl mb-8">
        <div class="card-body">
            <h2 class="card-title text-2xl mb-4">🚀 Esegui Comando</h2>
            
            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text">Progetto</span>
                </label>
                <select id="command-project" class="select select-bordered w-full">
                    <option value="EGI">FlorenceEGI</option>
                    <option value="NATAN">NATAN_LOC</option>
                </select>
            </div>

            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text">Comando</span>
                </label>
                <input type="text" id="command-input" class="input input-bordered w-full" 
                       placeholder="es: migrate, migrate:status, migrate:rollback --step=1" 
                       value="migrate:status">
            </div>

            <div class="alert alert-warning mb-4" id="destructive-warning" style="display: none;">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>⚠️ <strong>Comando Distruttivo!</strong> Questo comando cancellerà tutte le tabelle del database condiviso.</span>
            </div>

            <button class="btn btn-primary btn-block" onclick="executeCustomCommand()">
                ▶️ Esegui Comando
            </button>
        </div>
    </div>

    {{-- Output Panel --}}
    <div class="card bg-base-200 shadow-xl mb-8" id="output-panel" style="display: none;">
        <div class="card-body">
            <h2 class="card-title text-2xl mb-4">📋 Output</h2>
            <div class="mockup-code">
                <pre id="command-output" class="text-sm"></pre>
            </div>
            <button class="btn btn-sm btn-outline mt-4" onclick="clearOutput()">🗑️ Pulisci</button>
        </div>
    </div>

    {{-- Automatic Backup Configuration --}}
    <div class="card bg-base-200 shadow-xl mb-8">
        <div class="card-body">
            <h2 class="card-title text-2xl mb-4">⏰ Backup Automatico (Cron Job)</h2>
            
            <div class="form-control mb-4">
                <label class="cursor-pointer label">
                    <span class="label-text text-lg font-semibold">Abilita Backup Automatico</span>
                    <input type="checkbox" id="backup-enabled" class="toggle toggle-primary" 
                           {{ $backupConfig->is_enabled ? 'checked' : '' }} 
                           onchange="updateBackupConfig()">
                </label>
            </div>

            <div id="backup-config-panel" style="display: {{ $backupConfig->is_enabled ? 'block' : 'none' }};">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Intervallo</span>
                        </label>
                        <select id="backup-interval-type" class="select select-bordered" onchange="updateBackupConfig()">
                            <option value="hours" {{ $backupConfig->interval_type === 'hours' ? 'selected' : '' }}>Ore</option>
                            <option value="days" {{ $backupConfig->interval_type === 'days' ? 'selected' : '' }}>Giorni</option>
                            <option value="weeks" {{ $backupConfig->interval_type === 'weeks' ? 'selected' : '' }}>Settimane</option>
                        </select>
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Valore Intervallo</span>
                        </label>
                        <input type="number" id="backup-interval-value" class="input input-bordered" 
                               value="{{ $backupConfig->interval_value }}" min="1" 
                               onchange="updateBackupConfig()">
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Ora di Inizio</span>
                        </label>
                        <input type="time" id="backup-start-time" class="input input-bordered" 
                               value="{{ $backupConfig->start_time }}" 
                               onchange="updateBackupConfig()">
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Retention (giorni)</span>
                        </label>
                        <input type="number" id="backup-retention" class="input input-bordered" 
                               value="{{ $backupConfig->retention_days }}" min="1" max="365"
                               onchange="updateBackupConfig()">
                    </div>

                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Max Backup da Mantenere</span>
                        </label>
                        <input type="number" id="backup-max-count" class="input input-bordered" 
                               value="{{ $backupConfig->max_backups }}" min="1" max="100"
                               onchange="updateBackupConfig()">
                    </div>
                </div>

                @if($backupConfig->last_backup_at)
                    <div class="alert alert-info mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <div class="font-bold">Ultimo Backup:</div>
                            <div>{{ $backupConfig->last_backup_at->format('d/m/Y H:i:s') }}</div>
                        </div>
                    </div>
                @endif

                @if($backupConfig->next_backup_at)
                    <div class="alert alert-success mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <div class="font-bold">Prossimo Backup:</div>
                            <div>{{ $backupConfig->next_backup_at->format('d/m/Y H:i:s') }}</div>
                        </div>
                    </div>
                @endif

                @if($backupConfig->last_error)
                    <div class="alert alert-error mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <div class="font-bold">Ultimo Errore:</div>
                            <div class="text-sm">{{ $backupConfig->last_error }}</div>
                        </div>
                    </div>
                @endif

                <div class="alert alert-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <div class="font-bold">Configurazione Cron Job:</div>
                        <code class="text-sm">* * * * * cd {{ base_path() }} && php artisan backup:automatic >> /dev/null 2>&1</code>
                        <p class="text-sm mt-2">Aggiungi questa riga al tuo crontab per eseguire il backup automatico ogni minuto (il comando verificherà se è il momento giusto).</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Backup Management --}}
    <div class="card bg-base-200 shadow-xl">
        <div class="card-body">
            <div class="flex items-center justify-between mb-4">
                <h2 class="card-title text-2xl">💾 Gestione Backup Database</h2>
                <button class="btn btn-primary" onclick="createBackup()">
                    ➕ Crea Backup Manuale
                </button>
            </div>

            {{-- Create Backup Panel --}}
            <div class="mb-6 p-4 bg-base-300 rounded-lg" id="create-backup-panel" style="display: none;">
                <div class="form-control mb-4">
                    <label class="label">
                        <span class="label-text">Etichetta Backup (opzionale)</span>
                    </label>
                    <input type="text" id="backup-label" class="input input-bordered w-full" 
                           placeholder="es: prima-modifica-importante" 
                           maxlength="100">
                </div>
                <button class="btn btn-success btn-block" onclick="confirmCreateBackup()">
                    ✅ Conferma Creazione Backup
                </button>
                <button class="btn btn-outline btn-block mt-2" onclick="cancelCreateBackup()">
                    ❌ Annulla
                </button>
            </div>
            
            @if(count($backups) > 0)
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>File</th>
                                <th>Dimensione</th>
                                <th>Data Creazione</th>
                                <th>Azioni</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($backups as $backup)
                                <tr>
                                    <td>
                                        <code class="text-sm">{{ $backup['filename'] }}</code>
                                    </td>
                                    <td>{{ $backup['size_human'] }}</td>
                                    <td>{{ $backup['modified_human'] }}</td>
                                    <td>
                                        <div class="flex gap-2">
                                            <button class="btn btn-xs btn-primary" onclick="restoreBackup('{{ $backup['path'] }}', '{{ $backup['filename'] }}')">
                                                🔄 Ripristina
                                            </button>
                                            <button class="btn btn-xs btn-outline" onclick="downloadBackup('{{ $backup['path'] }}')">
                                                📥 Scarica
                                            </button>
                                            <button class="btn btn-xs btn-error" onclick="deleteBackup('{{ $backup['path'] }}', '{{ $backup['filename'] }}')">
                                                🗑️ Elimina
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-base-content/70">Nessun backup disponibile.</p>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        const downloadBackupBaseUrl = '{{ route('superadmin.migration-orchestrator.backups.download') }}';
        const destructiveCommands = ['refresh', 'reset', 'fresh'];
        
        function refreshStatus(project) {
            fetch(`{{ route('superadmin.migration-orchestrator.status', ['project' => '__PROJECT__']) }}`.replace('__PROJECT__', project))
                .then(r => r.json())
                .then(data => {
                    location.reload();
                })
                .catch(err => {
                    alert('Errore aggiornamento status: ' + err.message);
                });
        }

        function executeCommand(project, command, requiresConfirmation = false, isDestructive = false) {
            if (isDestructive) {
                if (!confirm(`⚠️ ATTENZIONE: Il comando "${command}" è DISTRUTTIVO!\n\nCancellerà TUTTE le tabelle del database condiviso.\n\nContinuare?`)) {
                    return;
                }
            }

            if (requiresConfirmation && !confirm(`Eseguire il comando "${command}" per il progetto ${project}?`)) {
                return;
            }

            showOutput(`Eseguendo: ${project} -> ${command}...\n\n`);

            fetch('{{ route("superadmin.migration-orchestrator.execute") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    project: project,
                    command: command,
                    confirmed: isDestructive || requiresConfirmation
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showOutput(data.output || 'Comando eseguito con successo.');
                } else {
                    showOutput('ERRORE:\n' + (data.error || data.output || 'Errore sconosciuto'));
                }
            })
            .catch(err => {
                showOutput('ERRORE: ' + err.message);
            });
        }

        function executeCustomCommand() {
            const project = document.getElementById('command-project').value;
            const command = document.getElementById('command-input').value.trim();
            
            if (!command) {
                alert('Inserisci un comando');
                return;
            }

            const commandBase = command.split(':').pop().split(' ')[0];
            const isDestructive = destructiveCommands.includes(commandBase);

            // Show/hide warning
            const warningEl = document.getElementById('destructive-warning');
            if (isDestructive) {
                warningEl.style.display = 'flex';
            } else {
                warningEl.style.display = 'none';
            }

            if (isDestructive) {
                if (!confirm(`⚠️ ATTENZIONE: Il comando "${command}" è DISTRUTTIVO!\n\nCancellerà TUTTE le tabelle del database condiviso.\n\nContinuare?`)) {
                    return;
                }
            }

            executeCommand(project, command, false, isDestructive);
        }

        function showOutput(text) {
            const panel = document.getElementById('output-panel');
            const output = document.getElementById('command-output');
            panel.style.display = 'block';
            output.textContent = text;
            panel.scrollIntoView({ behavior: 'smooth' });
        }

        function clearOutput() {
            document.getElementById('output-panel').style.display = 'none';
            document.getElementById('command-output').textContent = '';
        }

        function createBackup() {
            document.getElementById('create-backup-panel').style.display = 'block';
            document.getElementById('backup-label').focus();
        }

        function cancelCreateBackup() {
            document.getElementById('create-backup-panel').style.display = 'none';
            document.getElementById('backup-label').value = '';
        }

        function confirmCreateBackup() {
            const label = document.getElementById('backup-label').value.trim();
            
            showOutput('Creazione backup in corso...\n\n');

            fetch('{{ route("superadmin.migration-orchestrator.backups.create") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    label: label || null
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showOutput(`✅ Backup creato con successo!\n\nFile: ${data.backup.filename}\nDimensione: ${data.backup.size_human}\n\n`);
                    cancelCreateBackup();
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showOutput('❌ ERRORE: ' + (data.error || 'Errore sconosciuto'));
                }
            })
            .catch(err => {
                showOutput('❌ ERRORE: ' + err.message);
            });
        }

        function restoreBackup(path, filename) {
            if (!confirm(`⚠️ ATTENZIONE: Ripristinare il backup?\n\nFile: ${filename}\n\nQuesto sovrascriverà TUTTO il database corrente.\n\nVerrà creato un backup di sicurezza prima del ripristino.\n\nContinuare?`)) {
                return;
            }

            if (!confirm(`⚠️ CONFERMA FINALE: Procedere con il ripristino del backup?\n\n${filename}\n\nQuesta operazione NON può essere annullata!`)) {
                return;
            }

            showOutput(`Ripristino backup in corso...\nFile: ${filename}\n\nCreazione backup di sicurezza...\n\n`);

            fetch('{{ route("superadmin.migration-orchestrator.backups.restore") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    backup_path: path,
                    confirmed: true
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showOutput(`✅ Backup ripristinato con successo!\n\nBackup di sicurezza creato: ${data.safety_backup}\n\n`);
                    setTimeout(() => location.reload(), 3000);
                } else {
                    showOutput('❌ ERRORE:\n' + (data.error || data.output || 'Errore sconosciuto'));
                }
            })
            .catch(err => {
                showOutput('❌ ERRORE: ' + err.message);
            });
        }

        function deleteBackup(path, filename) {
            if (!confirm(`Eliminare il backup?\n\nFile: ${filename}\n\nQuesta operazione non può essere annullata.`)) {
                return;
            }

            fetch('{{ route("superadmin.migration-orchestrator.backups.delete") }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    backup_path: path
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showOutput(`✅ Backup eliminato con successo!\n\nFile: ${filename}\n\n`);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showOutput('❌ ERRORE: ' + (data.error || 'Errore sconosciuto'));
                }
            })
            .catch(err => {
                showOutput('❌ ERRORE: ' + err.message);
            });
        }

        function downloadBackup(path) {
            // Download diretto tramite route dedicata
            const url = `${downloadBackupBaseUrl}?path=${encodeURIComponent(path)}`;
            window.open(url, '_blank');
        }

        function updateBackupConfig() {
            const isEnabled = document.getElementById('backup-enabled').checked;
            const panel = document.getElementById('backup-config-panel');
            panel.style.display = isEnabled ? 'block' : 'none';

            if (!isEnabled) {
                // Se disabilitato, salva subito
                saveBackupConfig();
                return;
            }

            // Debounce per salvare dopo 1 secondo dall'ultimo cambio
            clearTimeout(window.backupConfigTimeout);
            window.backupConfigTimeout = setTimeout(saveBackupConfig, 1000);
        }

        function saveBackupConfig() {
            const config = {
                is_enabled: document.getElementById('backup-enabled').checked,
                interval_type: document.getElementById('backup-interval-type').value,
                interval_value: parseInt(document.getElementById('backup-interval-value').value),
                start_time: document.getElementById('backup-start-time').value,
                retention_days: parseInt(document.getElementById('backup-retention').value),
                max_backups: parseInt(document.getElementById('backup-max-count').value),
            };

            fetch('{{ route("superadmin.migration-orchestrator.backup-config.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(config)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showOutput(`✅ Configurazione backup aggiornata!\n\nProssimo backup: ${data.config.next_backup_at || 'N/A'}\n\n`);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showOutput('❌ ERRORE: ' + (data.error || 'Errore sconosciuto'));
                }
            })
            .catch(err => {
                showOutput('❌ ERRORE: ' + err.message);
            });
        }

        // Check for destructive commands in input
        document.getElementById('command-input').addEventListener('input', function(e) {
            const command = e.target.value.trim();
            const commandBase = command.split(':').pop().split(' ')[0];
            const warningEl = document.getElementById('destructive-warning');
            
            if (destructiveCommands.includes(commandBase)) {
                warningEl.style.display = 'flex';
            } else {
                warningEl.style.display = 'none';
            }
        });
    </script>
    @endpush
</x-layouts.superadmin>

