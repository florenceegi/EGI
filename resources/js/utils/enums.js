let enumPromise = null;

export function loadEnums() {
    if (!enumPromise) {
        enumPromise = new Promise(async (resolve, reject) => {
            let attempts = 0;
            const maxAttempts = 3;
            
            const tryFetch = async () => {
                attempts++;
                try {
                    console.log(`⏳ Caricamento ENUM (Tentativo ${attempts}/${maxAttempts})...`);

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                    if (!csrfToken) {
                         console.warn("⚠️ CSRF Token missing in meta tag, skipping request header injection.");
                    }

                    const response = await fetch('/js/enums', {
                        headers: {
                          'Accept': 'application/json',
                          ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {})
                        }
                    });

                    console.log('ENUM response status:', response.status);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP Error: ${response.status}`);
                    }

                    const data = await response.json();
                    window.enums = data;
                    console.log("✅ ENUM caricati:", window.enums);
                    resolve(data);
                } catch (error) {
                    console.error(`❌ Errore caricamento ENUM (Tentativo ${attempts}):`, error);
                    
                    if (attempts < maxAttempts) {
                        const delay = 1000 * attempts; // Exponential backoff-ish
                        console.log(`🔄 Riprovo tra ${delay}ms...`);
                        setTimeout(tryFetch, delay);
                    } else {
                        console.error("💀 Falliti tutti i tentativi di caricamento ENUM.");
                        window.enums = {}; // Fallback sicuro
                        reject(error);
                    }
                }
            };
            
            tryFetch();
        });
    }
    return enumPromise;
}

// ✅ Ora `getEnum()` aspetta il caricamento degli ENUM prima di eseguire il codice
export async function getEnum(enumGroup, key) {
    await loadEnums();
    return window.enums?.[enumGroup]?.[key] || null;
}

// ✅ `isPendingStatus()` ora aspetta gli ENUM prima di eseguire il controllo
export async function isPendingStatus(status) {
    await loadEnums(); // ⏳ Aspettiamo che gli ENUM siano caricati prima di eseguire la funzione

    const pendingStatuses = [
        await getEnum("NotificationStatus", "PENDING"),
        await getEnum("NotificationStatus", "PENDING_CREATE"),
        await getEnum("NotificationStatus", "PENDING_UPDATE")
    ].filter(Boolean); // Rimuove eventuali valori null

    console.log("🔍 ENUM trovati per pending:", pendingStatuses);
    return pendingStatuses.includes(status);
}

