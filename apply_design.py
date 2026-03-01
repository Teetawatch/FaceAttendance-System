import os
import re

directory = r"c:\Project\FaceAttendance-System\resources\views"
skips = ["kiosk.blade.php", "dashboard.blade.php"]

def apply_design_system(content):
    # Colors replace
    content = content.replace("bg-slate-50", "bg-background")
    content = content.replace("bg-white", "bg-card")
    content = content.replace("text-slate-800", "text-text font-bold font-mono")
    content = content.replace("text-slate-700", "text-text")
    content = content.replace("text-slate-600", "text-text/80")
    content = content.replace("text-slate-500", "text-primary-600/70")
    content = content.replace("text-slate-400", "text-primary-400")
    content = content.replace("border-slate-100", "border-primary-50")
    content = content.replace("border-slate-200", "border-primary-100")
    
    # Primary CTA buttons
    content = content.replace("bg-blue-600 hover:bg-blue-700", "bg-primary-600 hover:bg-primary-700")
    content = content.replace("bg-indigo-600 hover:bg-indigo-700", "bg-primary-600 hover:bg-primary-700 text-white shadow-md shadow-primary-500/20")
    
    # Layout container classes (add shadow-md to card instead of shadow-sm)
    content = content.replace("shadow-sm border border-slate-100", "shadow-md border border-primary-50 hover:shadow-lg transition-all")
    
    # Text font for h2, h3
    content = re.sub(r'h2 class="([^"]+)"', r'h2 class="\1 font-mono"', content)
    content = re.sub(r'h3 class="([^"]+)"', r'h3 class="\1 font-mono"', content)
    
    # Emojis (removing some hardcoded emojis from headers)
    content = content.replace("👨‍🎓 ", "")
    content = content.replace("🎓 ", "")
    
    return content

count = 0
for root, dirs, files in os.walk(directory):
    for filename in files:
        if filename.endswith(".blade.php") and filename not in skips:
            filepath = os.path.join(root, filename)
            with open(filepath, 'r', encoding='utf-8') as f:
                content = f.read()
                
            new_content = apply_design_system(content)
            if new_content != content:
                with open(filepath, 'w', encoding='utf-8') as f:
                    f.write(new_content)
                count += 1

print(f"Updated {count} files.")
