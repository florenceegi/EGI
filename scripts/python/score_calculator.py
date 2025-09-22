import json

# File di input (dataset iniziale)
input_file = "dataset_ai.json"

# File di output (con metriche e categorie aggiornate)
output_file = "scored_classes_with_categories.json"

# Funzione per calcolare metriche mancanti (se non già presenti)
def calculate_metrics(class_code):
    complexity = class_code.count("if") + class_code.count("for") + class_code.count("while")
    srp = class_code.count("class")
    ocp = class_code.count("extends") + class_code.count("implements")
    isp = class_code.count("interface")
    dip = class_code.count("__construct")
    return {
        "complexity": complexity,
        "srp": srp,
        "ocp": ocp,
        "isp": isp,
        "dip": dip,
    }

# Funzione per categorizzare le classi
def categorize_class(class_data):
    name = class_data.get("name", "").lower()
    if "service" in name:
        return "simple_service"
    elif "controller" in name:
        return "controller"
    elif "trait" in name or "interface" in name:
        return "interface_or_trait"
    else:
        return "domain_class"

# Funzione per calcolare il punteggio
def calculate_score(class_data, category):
    metrics = class_data.get("metrics", {})
    complexity = metrics.get("complexity", 0)
    srp = metrics.get("srp", 0)
    ocp = metrics.get("ocp", 0)
    isp = metrics.get("isp", 0)
    dip = metrics.get("dip", 0)

    if category == "simple_service":
        return srp + complexity
    elif category == "controller":
        return srp + ocp + complexity
    elif category == "interface_or_trait":
        return isp + dip
    else:  # domain_class
        return srp + ocp + isp + dip + complexity

# Funzione per verificare se è un PFV
def is_pfv(score, category):
    threshold = 7 if category != "simple_service" else 5
    return score >= threshold

# Caricamento del dataset
with open(input_file, "r") as file:
    dataset = json.load(file)

# Elaborazione e aggiunta del campo "code"
results = []
for class_data in dataset:
    name = class_data.get("name", "Unknown")
    code = class_data.get("code", "")  # Recupera il codice della classe
    metrics = class_data.get("metrics", None)

    # Calcola le metriche se mancanti
    if not metrics:
        metrics = calculate_metrics(code)

    category = categorize_class(class_data)
    score = calculate_score({"metrics": metrics}, category)
    pfv_flag = is_pfv(score, category)

    # Crea il risultato con tutti i campi necessari
    results.append({
        "name": name,
        "code": code,  # Inserisce il codice
        "category": category,
        "metrics": metrics,
        "score": score,
        "is_pfv": pfv_flag,
    })

    print(f"Classe: {name}, Categoria: {category}, Punteggio: {score}, PFV: {pfv_flag}")

# Scrittura del file aggiornato
with open(output_file, "w") as file:
    json.dump(results, file, indent=4)

print(f"File aggiornato salvato in: {output_file}")
