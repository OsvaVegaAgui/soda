#!/bin/bash

export PATH=$HOME:$PATH

echo "=== Iniciando deploy ==="

echo ""
echo ">>> Subiendo archivos de la app a laravel_app/..."
git-ftp push --scope app

echo ""
echo ">>> Subiendo archivos publicos a public_html/..."
git-ftp push --scope public

echo ""
echo "=== Deploy completado ==="
