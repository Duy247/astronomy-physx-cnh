import re
import os

php_file = "Compare_mel15_Med.php"
directory = os.path.dirname(os.path.abspath(php_file))

# Read the PHP file
with open(php_file, "r", encoding="utf-8") as f:
    php_content = f.read()

# Find all href="Compare...htm" (not .php)
compare_links = set(re.findall(r'href="(Compare_[^"]+?\.htm)"', php_content))

# Map of old href to new image src
href_to_imgsrc = {}

for link in compare_links:
    htm_path = os.path.join(directory, link)
    if not os.path.exists(htm_path):
        continue
    with open(htm_path, "r", encoding="utf-8") as f:
        htm_content = f.read()
    # Find the first <img src="...">
    img_match = re.search(r'<img\s+[^>]*src=["\']([^"\']+)["\']', htm_content, re.IGNORECASE)
    if img_match:
        img_src = img_match.group(1)
        href_to_imgsrc[link] = img_src

# Replace hrefs in the PHP file content
def replace_href(match):
    href = match.group(1)
    if href in href_to_imgsrc:
        return f'href="/{href_to_imgsrc[href]}"'
    else:
        return match.group(0)

new_php_content = re.sub(r'href="(Compare_[^"]+?\.htm)"', replace_href, php_content)

# Write the updated PHP file (backup original first)
os.rename(php_file, php_file + ".bak")
with open(php_file, "w", encoding="utf-8") as f:
    f.write(new_php_content)

print("Done. Original file backed up as Compare_m8_Med.php.bak")