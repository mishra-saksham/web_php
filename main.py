import requests
import time
import re 
import random
url ="https://genius.com/The-weeknd-starboy-lyrics"
class_to_get="Lyrics__Container-sc-1ynbvzw-5 Dzxov"
req =  requests.get(url)
text = req.text
from bs4 import BeautifulSoup

def extract_data_from_element(html, css_class):
    soup = BeautifulSoup(html, 'html.parser')
    elements = soup.find_all(class_=css_class)
    data = ["\n".join(element.stripped_strings) for element in elements]
    return "".join(re.split(r'\[.*?\]',"".join(data)))

# Example usage
html = '<div class="my-class">Hello, World!</div><div class="my-class">Goodbye, World!</div>'
css_class = 'my-class'
data = extract_data_from_element(html, 