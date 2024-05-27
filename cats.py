import json

# Function to load JSON data from a file
def load_json(filename):
    with open(filename, 'r') as file:
        return json.load(file)

# Function to extract names of all items
def extract_item_names(data):
    item_names = []
    for item in data:
        item_names.append(item['CatName'])
    return item_names

if __name__ == "__main__":
    # Load the JSON data from the file
    data0 = load_json('hm_categories.json')  # Replace 'path_to_your_file/test.json' with the actual path to your JSON file

    # Extract and print the names of all items
    names0 = extract_item_names(data0)

    for name in names0:
        print(name)