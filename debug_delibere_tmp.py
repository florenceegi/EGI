import sys, os
os.chdir("/home/forge/natan-loc.florenceegi.com/python_ai_service")
with open(".env") as f:
    for line in f:
        line = line.strip()
        if line and not line.startswith('#') and '=' in line:
            k, _, v = line.partition('=')
            os.environ.setdefault(k.strip(), v.strip())

import psycopg2, psycopg2.extras
conn = psycopg2.connect(os.getenv("DATABASE_URL"))
cur = conn.cursor(cursor_factory=psycopg2.extras.RealDictCursor)

# Conta deliberazioni 2018-2020
cur.execute("SELECT COUNT(*) as n FROM natan.rag_documents WHERE tenant_id=2 AND tipo_atto ILIKE '%deliber%' AND anno IN ('2018','2019','2020')")
print("Deliberazioni 2018-2020:", cur.fetchone()['n'])

# Conta con search vector su infrastrutture
cur.execute("SELECT COUNT(*) as n FROM natan.rag_documents WHERE tenant_id=2 AND tipo_atto ILIKE '%deliber%' AND anno IN ('2018','2019','2020') AND search_vector @@ plainto_tsquery('italian', 'infrastrutture stradali lavori pubblici')")
print("Deliberazioni 2018-2020 + infrastrutture stradali:", cur.fetchone()['n'])

cur.execute("SELECT COUNT(*) as n FROM natan.rag_documents WHERE tenant_id=2 AND tipo_atto ILIKE '%deliber%' AND anno IN ('2018','2019','2020') AND search_vector @@ plainto_tsquery('italian', 'finanziamento')")
print("Deliberazioni 2018-2020 + finanziamento:", cur.fetchone()['n'])

# Mostra sample
cur.execute("SELECT document_id, title, tipo_atto, anno FROM natan.rag_documents WHERE tenant_id=2 AND tipo_atto ILIKE '%deliber%' AND anno IN ('2018','2019','2020') LIMIT 5")
print("Sample:")
for r in cur.fetchall():
    print(" -", dict(r))

cur.close()
conn.close()
print("DONE")
