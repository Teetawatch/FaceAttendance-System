import re

def remove_fa_link(filename):
    with open(filename, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Regex to match the FontAwesome link block
    new_content = re.sub(r'<!--\s*Font Awesome\s*-->\s*<link[^>]*href="[^"]*font-awesome[^>]*>\s*(?:</link>\s*)?', '', content, flags=re.IGNORECASE)
    
    if new_content != content:
        with open(filename, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print(f"Removed FA from {filename}")

remove_fa_link(r'c:\Project\FaceAttendance-System\resources\views\layouts\app.blade.php')
remove_fa_link(r'c:\Project\FaceAttendance-System\resources\views\layouts\guest.blade.php')
