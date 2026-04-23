import os

# The specific folders and files you actually wrote (Ignoring vendor and node_modules)
DIRECTORIES_TO_SCAN =[
    'app/Http/Controllers',
    'app/Models',
    'app/Services',
    'database/migrations',
    'database/seeders',
    'resources/views',
    'routes',
    'public/css'
]

OUTPUT_FILE = 'SourceCode.md'

def should_include_file(filename):
    # Only grab PHP, Blade, CSS, and JS files
    return filename.endswith(('.php', '.css', '.js'))

with open(OUTPUT_FILE, 'w', encoding='utf-8') as outfile:
    outfile.write("# THE TCG EXCHANGE - SOURCE CODE ARCHIVE\n\n")
    
    for directory in DIRECTORIES_TO_SCAN:
        if not os.path.exists(directory):
            continue
            
        for root, _, files in os.walk(directory):
            for file in files:
                if should_include_file(file):
                    file_path = os.path.join(root, file)
                    
                    # Write the Header
                    outfile.write(f"## FILE: `{file_path}`\n")
                    outfile.write("```php\n" if file.endswith('.php') else "```html\n" if file.endswith('.blade.php') else "```css\n")
                    
                    # Read and write the contents
                    try:
                        with open(file_path, 'r', encoding='utf-8') as infile:
                            outfile.write(infile.read())
                    except Exception as e:
                        outfile.write(f"// Could not read file: {e}")
                        
                    outfile.write("\n```\n\n")

print(f"Extraction Complete. Open {OUTPUT_FILE} in VS Code and Print to PDF.")