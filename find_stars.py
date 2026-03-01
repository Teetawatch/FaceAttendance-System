with open('patch.diff', 'r', encoding='utf-16') as f:
    lines = f.readlines()

with open('stars.txt', 'w', encoding='utf-8') as out:
    for i, line in enumerate(lines):
        if '<x-heroicon-o-star' in line:
            for j in range(i-1, i-6, -1):
                if j >= 0 and lines[j].startswith('-') and 'fa-' in lines[j]:
                    out.write(f"L{i}: {lines[j].strip()}  --->  {line.strip()}\n")
                    break
