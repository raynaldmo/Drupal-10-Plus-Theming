#!/usr/bin/env bash
# Backup site
DDEV=/usr/local/bin/ddev

# Set to desired location for site backups
backup_dir=$HOME/Desktop/Website-Backups
site_dir=${PWD}
site=$(basename ${site_dir})

echo "Site is ${site}"
${DDEV} export-db -d db -f ${backup_dir}/${site}/${site}-db-$(date +"%m-%d-%Y-%H-%M-%S").sql.gz
