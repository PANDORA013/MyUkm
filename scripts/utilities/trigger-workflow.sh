#!/bin/bash

# Script to trigger GitHub Actions workflow after authentication
# Usage: ./trigger-workflow.sh

echo "GitHub Actions Workflow Trigger Script"
echo "======================================"

# Check if gh CLI is installed
if ! command -v gh &> /dev/null; then
    echo "Error: GitHub CLI (gh) is not installed"
    echo "Please install it from: https://cli.github.com/"
    exit 1
fi

# Check if authenticated
if ! gh auth status &> /dev/null; then
    echo "Not authenticated with GitHub CLI"
    echo "Please run: gh auth login"
    echo "Then run this script again"
    exit 1
fi

# Trigger the workflow
echo "Triggering Laravel Tests workflow..."
gh workflow run laravel-tests.yml

if [ $? -eq 0 ]; then
    echo "✅ Workflow triggered successfully!"
    echo ""
    echo "You can check the status with:"
    echo "  gh run list"
    echo "  gh run view --web (to open in browser)"
    echo ""
    echo "Or visit: https://github.com/PANDORA013/MyUkm/actions"
else
    echo "❌ Failed to trigger workflow"
    exit 1
fi
