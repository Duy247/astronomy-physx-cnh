import os
import sys
import json
from bs4 import BeautifulSoup
import re

def parse_html_to_json(input_path, output_dir):
    # Normalize output_dir to avoid trailing slash/backslash issues
    output_dir = output_dir.rstrip('\\/')
    with open(input_path, encoding='utf-8') as f:
        soup = BeautifulSoup(f, 'html.parser')

    # Title
    title = soup.title.string.strip() if soup.title else ""

    # Object name (from the big heading in the table)
    object_name = ""
    for font in soup.find_all('font', size="4"):
        if font.center:
            text = font.center.get_text(strip=True)
            # Capitalize first letter, add space after catalog if missing, e.g., m8(Lagoon Nebula) -> M8 (Lagoon Nebula)
            match = re.match(r"([a-zA-Z]+)(\d+)\s*\((.+)\)", text)
            if match:
                object_name = f"{match.group(1).upper()}{match.group(2)} ({match.group(3)})"
            else:
                object_name = text
            break

    # Image info
    img_tag = soup.find('img')
    thumb = img_tag['src'] if img_tag else ""
    large = ""
    if img_tag and img_tag.parent.name == 'a':
        large = img_tag.parent['href']
    thumb = "/" + thumb
    large = "/" + large
    alt = img_tag['alt'] if img_tag and img_tag.has_attr('alt') else object_name

    # Details table
    details = []
    table = soup.find_all('table')
    if table:
        # Find the first table with two columns and blockquote tags
        for tr in table[-1].find_all('tr'):
            tds = tr.find_all('td')
            if len(tds) == 2:
                label = tds[0].get_text(strip=True).replace(":", "")
                value = tds[1].decode_contents().strip()
                value_list = value.split("\n<blockquote>\n")
                if label and value:
                    details.append({"label": label, "value": value_list[1]})

    # Compose JSON
    data = {
        "title": title,
        "object": object_name.upper(),
        "image": {
            "large": large,
            "thumb": thumb,
            "alt": alt.upper(),
            "survey": "P/DSS2/color",
            "fov": 25,
            "target": alt.upper().split(" ")[0]
        },
        "details": details
    }

    # Output filename
    base = os.path.splitext(os.path.basename(input_path))[0]
    output_path = os.path.join(output_dir, f"site_data_{base}.json")
    with open(output_path, "w", encoding="utf-8") as out:
        json.dump(data, out, indent=2, ensure_ascii=False)
    print(f"Wrote {output_path}")

def create_php_card_file(card_name, card_data_dir="card_data"):
    php_code = f"""<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/header.php'; ?>\n\n
<?php
// Load site data from JSON
$data = json_decode(file_get_contents(__DIR__ . '/{card_data_dir}/site_data_{card_name}.json'), true);
$title = $data['title'] ?? '';
$object = $data['object'] ?? '';
$image = $data['image'] ?? [];
$details = $data['details'] ?? [];
?>\n
<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/image_card.php'; ?>\n
<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>\n"""
    out_path = f"image_card/{card_name}.php"
    with open(out_path, "w", encoding="utf-8") as f:
        f.write(php_code)
    print(f"Wrote {out_path}")

def move_htm_to_legacy(input_htm):
    legacy_dir = "c:\\Downloaded Web Sites\\www.mistisoftware.com\\astronomy\\legacy"
    if not os.path.exists(legacy_dir):
        os.makedirs(legacy_dir)
    base_name = os.path.basename(input_htm)
    dst = os.path.join(legacy_dir, base_name)
    try:
        if os.path.exists(input_htm):
            os.rename(input_htm, dst)
            print(f"Moved {input_htm} -> {dst}")
        else:
            print(f"File not found: {input_htm}")
    except Exception as e:
        print(f"Error moving file: {e}")

# Example usage:
# create_php_card_file("178ED_m16")

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: python output_data.py <input_htm_file> [<gallery_json_file>]")
        sys.exit(1)
    input_htm = f"c:\\Downloaded Web Sites\\www.mistisoftware.com\\astronomy\\{sys.argv[1]}.htm"
    output_dir = "c:\\Downloaded Web Sites\\www.mistisoftware.com\\astronomy\\image_card\\card_data"
    parse_html_to_json(input_htm, output_dir)
    htm_name = input_htm.split("\\")[-1]
    card_name = htm_name.replace(".htm", "")
    create_php_card_file(card_name)

    # If gallery file path is provided, append gallery record
    if len(sys.argv) > 2:
        gallery_json_file = f"c:\\Downloaded Web Sites\\www.mistisoftware.com\\astronomy\\gallery\\{sys.argv[3]}.json"
        # Load the just-created JSON data
        base = os.path.splitext(os.path.basename(input_htm))[0]
        json_path = os.path.join(output_dir, f"site_data_{base}.json")
        with open(json_path, "r", encoding="utf-8") as f:
            data = json.load(f)
        # Extract the part inside parentheses from data['title']
        match = re.search(r'\(([^)]+)\)', data['title'])
        object_name = match.group(1) if match else sys.argv[2]
        gallery_record = {
            'link': f'image_card/{card_name}.php',
            'thumb': data['image']['thumb'].replace("800","100"),
            'alt': data['image']['alt'].split(" ")[0],
            'title': object_name,
            'subtitle': data['object'].split(" ")[0],
        }
        # Append to gallery file
        try:
            with open(gallery_json_file, 'r+', encoding='utf-8') as f:
                gallery_list = json.load(f)
                gallery_list.append(gallery_record)
                f.seek(0)
                json.dump(gallery_list, f, indent=2, ensure_ascii=False)
                f.truncate()
            print(f"Appended gallery record to {gallery_json_file}")
                # Move the input_htm file to legacy folder
            move_htm_to_legacy(input_htm)
        except Exception as e:
            print(f"Error updating gallery file: {e}")

