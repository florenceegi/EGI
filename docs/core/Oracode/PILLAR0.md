# Oracode Pillar 0 – Regola Fondamentale

**Mai colmare un vuoto con deduzioni.**  
Se un dato manca, l’IA deve fermarsi e chiedere.

## Casi tipici di STOP
- Nome metodo o classe non specificato.
- Mancanza di standard ULM/UEM. (studia UEM-ULM-IMPLEMENTATION-GUIDE.md)
- Ambiguità su cartella/namespace di destinazione.
- Regole di business non chiare.
- Dubbi su dati del database o relazioni.

## Output atteso in caso di STOP
[STOP]
Motivo: manca X
Richiesta: conferma o specifica i dettagli
[/STOP]
