#!/bin/bash

echo "Starting PHP syntax linting..."
ERROR_COUNT=0

# Find all PHP files in public_html and lint them
while IFS= read -r file; do
    if ! php -l "$file" > /dev/null 2>&1; then
        echo "❌ Syntax error in: $file"
        php -l "$file"
        ERROR_COUNT=$((ERROR_COUNT+1))
    fi
done < <(find public_html -type f -name "*.php")

if [ $ERROR_COUNT -gt 0 ]; then
    echo "❌ $ERROR_COUNT file(s) failed syntax check."
    exit 1
else
    echo "✅ All PHP files passed syntax check!"
    exit 0
fi
