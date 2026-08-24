import re
import json
from bs4 import BeautifulSoup

def process_fits_tr_pair(tr_html_pair):
    soup = BeautifulSoup(tr_html_pair, 'html.parser')
    trs = soup.find_all('tr')
    # First tr: details
    block = trs[0].find('blockquote')
    obj_b = block.find('b')
    obj_name = obj_b.text.strip() if obj_b else ''
    obj_title = obj_name
    details_html = str(block)
    slug = re.sub(r'[^a-zA-Z0-9]+', '_', obj_name).strip('_').lower()
    php_name = f'fits_{slug}.php'
    json_name = f'site_data_fits_{slug}.json'
    # Second tr: processed examples
    examples = []
    for td in trs[1].find_all('td'):
        center = td.find('center')
        if center:
            a = center.find('a')
            img = center.find('img')
            label = center.get_text(strip=True).replace('\n', ' ')
            examples.append({
                'link': a['href'] if a else '',
                'thumb': img['src'] if img else '',
                'label': label
            })
    # Compose details
    details = []
    block_soup = BeautifulSoup(details_html, 'html.parser')
    for el in block_soup.find_all(['a', 'font', 'br', 'blockquote']):
        if el.name == 'a' and el['href'].endswith('.FIT'):
            if not any(d['label'] == 'Files' for d in details):
                details.append({'label': 'Files', 'value': ''})
            details[-1]['value'] += str(el) + (el.next_sibling or '') + "<br>"
    if not details:
        details.append({'label': 'Details', 'value': details_html})
    # Add processed examples to details
    details.append({'label': 'Processed Examples', 'value': examples})
    # Gallery record
    gallery_record = {
        'link': f'image_card/{php_name}',
        'thumb': examples[0]['thumb'] if examples else '',
        'alt': obj_name.upper(),
        'title': obj_title,
        'subtitle': obj_name.upper(),
    }
    # JSON data
    json_data = {
        'title': f'Astrophotography - {obj_name} - {obj_title}'.upper(),
        'object': f'{obj_name} - {obj_title}'.upper(),
        'image': {
            'large': "/" + (examples[0]['thumb'].replace('_100', '_2000') if examples else ''),
            'thumb': "/" + (examples[0]['thumb'] if examples else ''),
            'alt': f'{obj_name} - {obj_title}'.upper(),
            'survey': 'P/DSS2/color',
            'fov': 25,
            'target': obj_name,
        },
        'details': details
    }
    php_code = f'''<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/header.php'; ?>\n\n<?php\n// Load site data from JSON\n$data = json_decode(file_get_contents(__DIR__ . '/card_data/{json_name}'), true);\n$title = $data['title'] ?? '';\n$object = $data['object'] ?? '';\n$image = $data['image'] ?? [];\n$details = $data['details'] ?? [];\n?>\n\n<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/image_card_nosky.php'; ?>\n\n<?php include $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php'; ?>\n'''
    return gallery_record, php_code, json.dumps(json_data, indent=2)

# To use manually:
# 1. Paste your <tr>...</tr> block into tr_html below.
# 2. Set php_path and json_path to your desired output locations.
# 3. Uncomment the lines to write the outputs.

tr_html = '''
<tr>
    <td colspan="4">
      <font face="Arial, Helvetica, sans-serif" size="2" color="#0000FF">
      <blockquote>
      <center>
      <b>ngc7960/7979/7992 (Veil  Nebula Region)</b><br><br>
      </center>
      <a href="fits/Veil_060614_10i150m_H.FIT">Veil_060614_10i150m_H.FIT</a> (H-A, 150 min, 15-min subs)<br>
      <a href="fits/Veil_060622_21i105m_L.FIT">Veil_060622_21i105m_L.FIT</a> (Luminance, 105 min, 5-min subs)<br>
      <a href="fits/Veil_060622_5i25m_R.FIT">Veil_060622_5i25m_R.FIT</a> (Red, 25 min, 5-min subs)<br>
      <a href="fits/Veil_060622_5i25m_G.FIT">Veil_060622_5i25m_G.FIT</a> (Green, 25 min, 5-min subs)<br>
      <a href="fits/Veil_060622_5i25m_B.FIT">Veil_060622_5i25m_B.FIT</a> (Blue, 25 min, 5-min subs)<br>
      </blockquote>
      </font>
      <font face="Arial, Helvetica, sans-serif" size="2" color="#0000FF">
      <center>
      Processed Examples Below:
      </center>
      </font>
    </td>
  </tr>
  <tr>
    <td valign="top">
      <center>
      <a href="FSQ106_Veil.htm">
      <img src="Images/Veil_060622_100.jpg"></a>
      <font face="Arial, Helvetica, sans-serif" size="1" color="#0000FF">
      <br>Processed by Jim Misti
      </font>
      </center>
    </td>
  </tr>
'''
name = "ngc7960"

php_path = f'c:/Downloaded Web Sites/www.mistisoftware.com/astronomy/image_card/fits_{name}.php'  # Change as needed
json_path = f'c:/Downloaded Web Sites/www.mistisoftware.com/astronomy/image_card/card_data/site_data_fits_{name}.json'  # Change as needed

gallery, php, json_txt = process_fits_tr_pair(tr_html)

# Add to gallery_fits.json manually:
with open('c:/Downloaded Web Sites/www.mistisoftware.com/astronomy/gallery/gallery_fits.json', 'r+', encoding='utf-8') as f:
    gallery_list = json.load(f)
    gallery_list.append(gallery)
    f.seek(0); json.dump(gallery_list, f, indent=2); f.truncate()

# Write PHP file manually:
with open(php_path, 'w', encoding='utf-8') as f:
    f.write(php)

# Write JSON file manually:
with open(json_path, 'w', encoding='utf-8') as f:
    f.write(json_txt)