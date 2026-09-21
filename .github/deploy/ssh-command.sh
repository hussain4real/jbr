#!/usr/bin/env bash
set -euo pipefail

# Installed root-owned; the GitHub key can only request deployment of a commit.
if [[ ${SSH_ORIGINAL_COMMAND:-} =~ ^deploy\ ([0-9a-f]{40})$ ]]; then
    exec /usr/bin/timeout --signal=TERM --kill-after=30s 20m \
        sudo -n -u jbr /usr/local/bin/jbr-deploy "${BASH_REMATCH[1]}"
fi

echo 'Only deploy <40-character commit SHA> is permitted.' >&2
exit 1
