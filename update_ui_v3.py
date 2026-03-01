import os
import re

directories = [
    r'c:\Project\FaceAttendance-System\resources\views'
]

exclude_files = [
    r'c:\Project\FaceAttendance-System\resources\views\monitor\kiosk.blade.php'.lower()
]

# Mapping common FontAwesome icons to Heroicons
icon_map = {
    'fa-users': 'users',
    'fa-user-check': 'user-circle',
    'fa-user-xmark': 'x-circle',
    'fa-user-graduate': 'academic-cap',
    'fa-gauge-high': 'chart-pie',
    'fa-tablet-screen-button': 'device-tablet',
    'fa-tablet-button': 'device-tablet',
    'fa-users-gear': 'cog-8-tooth',
    'fa-id-card': 'identification',
    'fa-book': 'book-open',
    'fa-chart-column': 'chart-bar',
    'fa-chart-pie': 'chart-pie',
    'fa-camera': 'camera',
    'fa-desktop': 'computer-desktop',
    'fa-clock-rotate-left': 'clock',
    'fa-calendar-check': 'calendar-days',
    'fa-calendar-day': 'calendar',
    'fa-gear': 'cog-6-tooth',
    'fa-sliders': 'cog-6-tooth',
    'fa-right-from-bracket': 'arrow-right-on-rectangle',
    'fa-arrow-right-from-bracket': 'arrow-left-on-rectangle',
    'fa-arrow-right-to-bracket': 'arrow-right-on-rectangle',
    'fa-bars': 'bars-3',
    'fa-bell': 'bell',
    'fa-chevron-down': 'chevron-down',
    'fa-chevron-right': 'chevron-right',
    'fa-chevron-left': 'chevron-left',
    'fa-download': 'arrow-down-tray',
    'fa-upload': 'arrow-up-tray',
    'fa-cloud-arrow-up': 'arrow-up-tray',
    'fa-plus': 'plus',
    'fa-pen-to-square': 'pencil-square',
    'fa-file-signature': 'pencil-square',
    'fa-trash': 'trash',
    'fa-trash-can': 'trash',
    'fa-face-viewfinder': 'viewfinder-circle',
    'fa-arrow-trend-up': 'arrow-trending-up',
    'fa-arrow-trend-down': 'arrow-trending-down',
    'fa-file-import': 'document-arrow-up',
    'fa-circle-check': 'check-circle',
    'fa-check': 'check',
    'fa-circle-xmark': 'x-circle',
    'fa-file-circle-xmark': 'document-minus',
    'fa-user': 'user',
    'fa-info-circle': 'information-circle',
    'fa-exclamation-triangle': 'exclamation-triangle',
    'fa-triangle-exclamation': 'exclamation-triangle',
    'fa-exclamation': 'exclamation-triangle',
    'fa-circle-exclamation': 'exclamation-circle',
    'fa-users-slash': 'users',
    'fa-xmark': 'x-mark',
    'fa-calendar': 'calendar',
    'fa-user-clock': 'clock',
    'fa-hourglass-half': 'clock',
    'fa-server': 'server',
    'fa-location-dot': 'map-pin',
    'fa-clock': 'clock',
    'fa-user-plus': 'user-plus',
    'fa-database': 'circle-stack',
    'fa-network-wired': 'globe-alt',
    'fa-search': 'magnifying-glass',
    'fa-magnifying-glass': 'magnifying-glass',
    'fa-filter': 'funnel',
    'fa-circle-notch': 'arrow-path',
    'fa-spinner': 'arrow-path',
    'fa-crosshairs': 'viewfinder-circle',
    'fa-rotate-left': 'arrow-uturn-left',
    'fa-floppy-disk': 'document-check',
    'fa-save': 'document-check',
    'fa-lightbulb': 'light-bulb',
    'fa-print': 'printer',
    'fa-image': 'photo',
    'fa-clipboard-list': 'clipboard-document-list',
    'fa-file-pdf': 'document-text',
    'fa-envelope': 'envelope',
    'fa-paper-plane': 'paper-airplane',
    'fa-file-export': 'document-arrow-down',
    'fa-barcode': 'qr-code',
    'fa-graduation-cap': 'academic-cap',
    'fa-arrow-up-right-from-square': 'arrow-top-right-on-square',
    'fa-file-excel': 'table-cells',
    'fa-calendar-week': 'calendar-days',
    'fa-building': 'building-office',
    'fa-eye': 'eye',
    'fa-address-card': 'identification',
    'fa-laptop-code': 'computer-desktop',
    'fa-face-smile': 'face-smile',
    'fa-face-smile-wink': 'face-smile',
    'fa-arrow-right': 'arrow-right',
    'fa-arrow-right-long': 'arrow-right',
    'fa-arrow-left': 'arrow-left',
    'fa-person-running': 'bolt',
    'fa-lock': 'lock-closed',
    'fa-key': 'key',
    'fa-briefcase': 'briefcase',
    'fa-satellite-dish': 'signal'
}

def update_file(file_path):
    if file_path.lower() in exclude_files:
        return

    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()

    original_content = content

    def icon_replacer(match):
        fa_classes = match.group(1).split()
        other_classes = []
        target_icon = 'star' # Default fallback
        for cls in fa_classes:
            if cls.startswith('fa-'):
                if cls in icon_map:
                    target_icon = icon_map[cls]
            elif cls not in ['fa-solid', 'fa-regular', 'fa-brands', 'fa-spin', 'fa-fw', 'fa-lg', 'fa-sm', 'fa-xs']:
                other_classes.append(cls)
        
        if not any(c.startswith('w-') for c in other_classes):
            other_classes.append('w-5')
            
        class_str = ' '.join(other_classes)
        if class_str:
            return f'<x-heroicon-o-{target_icon} class="{class_str}"/>'
        else:
            return f'<x-heroicon-o-{target_icon} />'

    content = re.sub(r'<i\s+class="([^"]*fa-[^"]*)"\s*>\s*</i>', icon_replacer, content)

    # Convert specific generic tags if needed like background color
    content = re.sub(r'bg-background', 'bg-slate-50', content)
    content = re.sub(r'border-primary-50', 'border-slate-200/60', content)
    content = re.sub(r'border-primary-100', 'border-slate-200', content)
    content = re.sub(r'bg-primary-600 hover:bg-primary-700', 'bg-indigo-600 hover:bg-indigo-700 shadow-md hover:shadow-lg transition-all', content)
    content = re.sub(r'text-primary-600', 'text-indigo-600', content)
    content = re.sub(r'bg-primary-50', 'bg-indigo-50/50', content)
    content = re.sub(r'bg-card rounded-2xl shadow-md border', 'bg-white/80 backdrop-blur-xl rounded-2xl shadow-glass border', content)

    if content != original_content:
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Updated {file_path}")

for directory in directories:
    for root, dirs, files in os.walk(directory):
        for file in files:
            file_path = os.path.join(root, file)
            if file_path.endswith('.blade.php'):
                update_file(file_path)

print("Done updating UI.")
