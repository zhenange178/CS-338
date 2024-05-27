import json

# Load the JSON data from the file
with open('hm_products_4.json', 'r') as file:
    data = json.load(file)

# Count the number of products in the 'results' array
number_of_products = len(data['results'])

print(f"Total number of products: {number_of_products}")