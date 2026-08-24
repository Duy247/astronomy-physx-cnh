import os
import json
import shutil

gallery_json = r"c:\Downloaded Web Sites\www.mistisoftware.com\astronomy\gallery\gallery_178ed.json"
root_dir = r"c:\Downloaded Web Sites\www.mistisoftware.com\astronomy"
legacy_dir = os.path.join(root_dir, "legacy")

with open(gallery_json, encoding="utf-8") as f:
    gallery = json.load(f)

for entry in gallery:
    link = entry.get("link", "")
    if link.startswith("/image_card/") and link.endswith(".php"):
        base = os.path.basename(link).replace(".php", ".htm")
        src = os.path.join(root_dir, base)
        dst = os.path.join(legacy_dir, base)
        if os.path.exists(src):
            shutil.move(src, dst)
            print(f"Moved {src} -> {dst}")
        else:
            print(f"File not found: {src}")
