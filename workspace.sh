#!/bin/bash

set -e
set -u

PROJECT_ROOT=$(pwd)
PROJECT_NAME=$(basename "$PROJECT_ROOT")

# Start the server if not already running
tmux start-server 2> /dev/null

# Connect to a session or create a new one
tmux attach-session -d -t "$PROJECT_NAME" || {
    echo "Creating a new session"

    ## 0: Editor
    tmux new-session -d -s "$PROJECT_NAME" bash
    tmux send-keys -t "$PROJECT_NAME" "e ." C-m

    ## 1: Shell
    tmux new-window -a -t "$PROJECT_NAME" bash

    ## 2: Gulp
    tmux new-window -a -t "$PROJECT_NAME" "gulp watch"

    ## 3: Dev server
    tmux new-window -a -t "$PROJECT_NAME" -n "devserver" "php artisan serve --host 0.0.0.0 --port 8083"

    tmux select-window -t "$PROJECT_NAME":0

    tmux attach-session -t "$PROJECT_NAME"
}
