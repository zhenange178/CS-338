import json

# Function to load JSON data from a file
def load_json(filename):
    with open(filename, 'r') as file:
        return json.load(file)

# Function to extract names of all items
def extract_item_names(data):
    item_names = []
    for item in data['results']:
        item_names.append(item['name'])
    return item_names

# Main execution
if __name__ == "__main__":
    # Load the JSON data from the file
    data0 = load_json('hm_products_0.json')  # Replace 'path_to_your_file/test.json' with the actual path to your JSON file

    # Extract and print the names of all items
    names0 = extract_item_names(data0)

    #1
    data1 = load_json('hm_products_1.json')  
    names1 = extract_item_names(data1)

    #2
    data2 = load_json('hm_products_2.json')  
    names2 = extract_item_names(data2)

     #3
    data3 = load_json('hm_products_3.json')  
    names3 = extract_item_names(data3)

    #4
    data4 = load_json('hm_products_4.json')  
    names4 = extract_item_names(data4)

    names_comb = names0 + names1 + names2 + names3 + names4
    names_comb.sort()

    for item in names_comb:
        print(item)