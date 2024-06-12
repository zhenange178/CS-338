import json
from collections import defaultdict

def count_underscores_in_x(file_path):
    # Load the JSON data from the file
    with open(file_path, 'r') as file:
        data = json.load(file)
    
    # Access the "products" array
    products = data.get("products", [])
    
    # Initialize a dictionary to store the histogram
    underscore_histogram = defaultdict(int)
    
    # Iterate through each product in the array
    for product in products:
        # Count the number of underscores in the 'x' attribute
        if 'mainCatCode' in product:
            count = product['mainCatCode'].count('_')
        else:
            count = 0  # Or handle missing 'x' attributes as needed
        
        # Increment the count in the histogram
        underscore_histogram[count] += 1
    
    sorted_histogram = dict(sorted(underscore_histogram.items()))
    
    return sorted_histogram

# Example usage
file_path = 'init/hm_product_list.json'
histogram = count_underscores_in_x(file_path)
print(histogram)
