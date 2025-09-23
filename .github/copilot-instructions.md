# Copilot Instructions — FlorenceEGI (repo-wide)

**Queste istruzioni sono vincolate esclusivamente ai due documenti seguenti, copiati integralmente nella repo. Niente deduzioni. Se manca un dato: FERMA e CHIEDI (REGOLA ZERO).**

- Documento OS3 completo → `docs/ai/os3-rules.md`
- Pattern GDPR + ULM + UEM completo → `docs/ai/gdpr-ulm-uem-pattern.md`

## Regole
1. Applica sempre la **REGOLA ZERO** descritta in `docs/ai/os3-rules.md`.
2. Consegna **un file per volta**. Nessun placeholder/TODO.
3. **Security by default** e **compliance GDPR** come da documenti.
4. Quando tocchi PII, integra **UltraLogManager**, **ErrorManagerInterface**, **AuditLogService**, **ConsentService** come definito in `docs/ai/gdpr-ulm-uem-pattern.md`.
5. Per esempi e snippet, **usa solo** quelli presenti nei due documenti sopra. Non inventare funzioni, classi o metodi non specificati.

## Note operative
- Se un requisito contrasta con OS3 o con il pattern GDPR/ULM/UEM, **fermati e chiedi** prima di procedere.
- Mantieni il codice AI‑readable e documentato secondo **OS2/OS3**.
