import json
import matplotlib.pyplot as plt

# Percorso del file JSON
output_file = "/home/fabio/EGI/scored_classes_with_categories.json"

with open("dataset_ai.json", "r") as file:
    data = json.load(file)
    print(f"Numero totale di file elaborati: {len(data)}")


# Carica i risultati
with open(output_file, "r") as file:
    results = json.load(file)

# Distribuzione dei punteggi
scores = [result["score"] for result in results]
plt.figure(figsize=(10, 6))
plt.hist(scores, bins=10, color="skyblue", edgecolor="black")
plt.title("Distribuzione dei punteggi")
plt.xlabel("Punteggio")
plt.ylabel("Numero di classi")
plt.grid(axis="y", linestyle="--", alpha=0.7)
plt.savefig("/home/fabio/EGI/score_distribution.png", dpi=300)
plt.close()

# Percentuale di PFV per categoria
categories = set(result["category"] for result in results)
pfv_counts = {category: 0 for category in categories}
total_counts = {category: 0 for category in categories}

for result in results:
    category = result["category"]
    total_counts[category] += 1
    if result["is_pfv"]:
        pfv_counts[category] += 1

# Percentuale per categoria
percentages = {
    category: (pfv_counts[category] / total_counts[category]) * 100
    for category in categories
}

# Grafico a barre
plt.figure(figsize=(12, 6))
plt.bar(percentages.keys(), percentages.values(), color="green", alpha=0.7)
plt.title("Percentuale di PFV per categoria")
plt.xlabel("Categoria")
plt.ylabel("Percentuale di PFV")
plt.xticks(rotation=45, ha="right")
plt.grid(axis="y", linestyle="--", alpha=0.7)
plt.tight_layout()
plt.savefig("/home/fabio/EGI/pfv_percentage_by_category.png", dpi=300)
plt.close()
