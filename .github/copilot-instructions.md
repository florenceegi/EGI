.sslip.io al fine di poter usare https con Let's Encript.

# Copilot Instructions — FlorenceEGI (repo-wide)

**Queste istruzioni sono vincolate esclusivamente ai due documenti seguenti, copiati integralmente nella repo. Niente deduzioni. Se manca un dato: FERMA e CHIEDI (REGOLA ZERO).**

-   Documento OS3 completo → `docs/ai/os3-rules.md`
-   Pattern GDPR + ULM + UEM completo → `docs/ai/gdpr-ulm-uem-pattern.md`

## Regole

1. Applica sempre la **REGOLA ZERO** descritta in `docs/ai/os3-rules.md`.
2. **RICERCA PRIMA DI PRESUMERE**: Se non conosci il problema specifico, cerca in documentazione, GitHub issues, Stack Overflow PRIMA di procedere con supposizioni.
3. **UMILTÀ TECNICA**: Meglio dire "non conosco questo caso, lasciami cercare" che insistere con tentativi basati su presupposti errati.
4. Consegna **un file per volta**. Nessun placeholder/TODO.
5. **Security by default** e **compliance GDPR** come da documenti.
6. Quando tocchi PII, integra **UltraLogManager**, **ErrorManagerInterface**, **AuditLogService**, **ConsentService** come definito in `docs/ai/gdpr-ulm-uem-pattern.md`.
7. Per esempi e snippet, **usa solo** quelli presenti nei due documenti sopra. Non inventare funzioni, classi o metodi non specificati.

## Note operative

-   Se un requisito contrasta con OS3 o con il pattern GDPR/ULM/UEM, **fermati e chiedi** prima di procedere.
-   Mantieni il codice AI‑readable e documentato secondo **OS2/OS3**.
-   **PROCESSO OBBLIGATORIO**: LEGGI → VERIFICA info → **RICERCA** (se necessario) → CHIEDI → CAPISCE → PRODUCI → CONSEGNI
-   **Anti-presunzione**: La ricerca approfondita è il PRIMO passo per problemi sconosciuti, non l'ultimo.
