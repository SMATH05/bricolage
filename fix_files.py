
import os

def fix_login():
    path = 'templates/security/login.html.twig'
    with open(path, 'r', encoding='utf-8') as f:
        lines = f.readlines()
    
    # Remove lines after the first {% endblock %} at the end
    # Or just specifically remove the stray </p>
    new_lines = []
    found_end = False
    for line in lines:
        if '{% endblock %}' in line:
            new_lines.append(line)
            found_end = True
            break
        new_lines.append(line)
    
    if found_end:
        with open(path, 'w', encoding='utf-8', newline='\n') as f:
            f.writelines(new_lines)
        print(f"Fixed {path}")
    else:
        print(f"Could not find endblock in {path}")

def fix_base():
    path = 'templates/base.html.twig'
    with open(path, 'r', encoding='utf-8') as f:
        lines = f.readlines()
    
    # Remove one of the redundant </body> tags
    body_count = 0
    new_lines = []
    for line in lines:
        if '</body>' in line:
            body_count += 1
            if body_count > 1:
                continue # Skip redundant one
        new_lines.append(line)
    
    with open(path, 'w', encoding='utf-8', newline='\n') as f:
        f.writelines(new_lines)
    print(f"Fixed {path}")

if __name__ == '__main__':
    fix_login()
    fix_base()
