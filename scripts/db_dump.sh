#!/usr/bin/env bash
# Bash MySQL dump script for TrackPro (Laravel backend)
#
# Usage:
#   chmod +x ./db_dump.sh
#   ./db_dump.sh [ENV_PATH] [OUT_DIR]
#
# Defaults:
#   ENV_PATH=../.env
#   OUT_DIR=.
#
# Produces: db_dump_<database>_<timestamp>.sql.gz

set -euo pipefail

ENV_PATH=${1:-"$(dirname "$0")/../.env"}
OUT_DIR=${2:-"$(pwd)"}

if [ ! -f "$ENV_PATH" ]; then
  echo "Env file not found: $ENV_PATH" >&2
  exit 1
fi

if ! command -v mysqldump >/dev/null 2>&1; then
  echo "mysqldump not found in PATH. Install MySQL client." >&2
  exit 1
fi

# Read .env keys (ignore comments). Handle quoted values.
declare -A ENV
while IFS= read -r line; do
  [[ -z "$line" || "$line" =~ ^# ]] && continue
  if [[ "$line" =~ ^([^=]+)=(.*)$ ]]; then
    key=${BASH_REMATCH[1]}
    val=${BASH_REMATCH[2]}
    # Trim quotes
    if [[ "$val" =~ ^".*"$ ]]; then val=${val:1:${#val}-2}; fi
    if [[ "$val" =~ ^'.*'$ ]]; then val=${val:1:${#val}-2}; fi
    ENV[$key]="$val"
  fi
done < "$ENV_PATH"

conn=${ENV[DB_CONNECTION]:-mysql}
if [[ "$conn" != "mysql" ]]; then
  echo "DB_CONNECTION is '$conn'. This script supports MySQL only." >&2
  echo "Hint: Your local .env shows sqlite; run this on the server with MySQL." >&2
  exit 1
fi

host=${ENV[DB_HOST]:-}
port=${ENV[DB_PORT]:-3306}
db=${ENV[DB_DATABASE]:-}
user=${ENV[DB_USERNAME]:-}
pass=${ENV[DB_PASSWORD]:-}

if [[ -z "$host" || -z "$db" || -z "$user" ]]; then
  echo "Missing DB settings in .env (need DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD)." >&2
  exit 1
fi

mkdir -p "$OUT_DIR"
ts=$(date +%Y%m%d_%H%M%S)
outfile="$OUT_DIR/db_dump_${db}_$ts.sql.gz"

echo "Dumping MySQL database '$db' from $host:$port to '$outfile'..."

# Use --single-transaction for InnoDB consistency; include routines/triggers/events.
mysqldump \
  -h "$host" -P "$port" -u "$user" -p"$pass" \
  --default-character-set=utf8mb4 \
  --single-transaction \
  --routines --triggers --events \
  "$db" | gzip -9 > "$outfile"

size=$(stat -c%s "$outfile" 2>/dev/null || wc -c < "$outfile")
kb=$(awk -v s="$size" 'BEGIN{printf "%.2f", s/1024}')
echo "Done. File size: ${kb} KB"