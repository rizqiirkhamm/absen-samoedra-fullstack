#!/bin/sh

# Queue Worker Supervisor Script
#
# Script shell ini akan menjalankan file queue_supervisor.php
# Dijalankan oleh cron job setiap 5 menit

# Tentukan jalur direktori aplikasi
APP_DIR="/home/u909411809/domains/contohdomain.xyz/public_html"
LOG_FILE="$APP_DIR/storage/logs/queue_supervisor.log"

# Catat bahwa script dijalankan
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Menjalankan queue_supervisor.php" >> $LOG_FILE

# Jalankan PHP script
/usr/bin/php $APP_DIR/queue_supervisor.php >> $LOG_FILE 2>&1

# Catat status exit
EXIT_CODE=$?
if [ $EXIT_CODE -eq 0 ]; then
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] queue_supervisor.php berhasil dijalankan" >> $LOG_FILE
else
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] Error saat menjalankan queue_supervisor.php (kode: $EXIT_CODE)" >> $LOG_FILE
fi

exit $EXIT_CODE
